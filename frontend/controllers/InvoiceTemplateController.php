<?php

namespace frontend\controllers;

use Yii;
use frontend\helpers\PdfHelper;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Invoice\InvoiceTemplate;


class InvoiceTemplateController extends Controller
{
    const MODULE_NAME = 'invoice-templates';

    public static function getUserRules() {
        return [
            ['id' => 1, 'name' => 'Просматривать реестр шаблонов счетов'],
            ['id' => 2, 'name' => 'Создавать/редактировать шаблоны счетов'],
            ['id' => 2, 'name' => 'Удалять шаблоны счетов'],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function() {
                    echo $this->render('/site/accessDenied');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'get-pdf'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 3));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-signers'],
                        'matchCallback' => function () {
                            return Yii::$app->request->getIsAjax();
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => InvoiceTemplate::find(),
            'sort' => false,
        ]);

        $this->view->params['fullWidth'] = true;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new InvoiceTemplate();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('create', $model->getFormData());
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }
        return $this->render('update', $model->getFormData());
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = false;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionGetSigners($organization_id)
    {
        if (Yii::$app->request->isAjax) {
            $signers = [];
            foreach (\common\models\Old\Signer::findAll(['organization_id' => $organization_id]) as $row) {
                $signers[] = ['id' => $row['signatory_id'], 'name' => $row['signatory_name']];
            }

            return json_encode($signers);
        }
    }

    public function actionGetPdf($id)
    {
        $model = $this->findModel($id);
        $templateData = $model->getExportTemplateData();
        $templateData['model'] = $model;

        \yii\helpers\FileHelper::createDirectory('files/temp/', $mode = 0775, $recursive = true);
        define('_MPDF_TTFONTDATAPATH', 'files/temp/');
        $mpdf = new \mPDF('','A4', 8, 'times new roman');
        $mpdf->setTitle('Шаблон счета');
        $mpdf->img_dpi = 72;

        $stylesheet = file_get_contents(Yii::getAlias('@webroot/css/invoice/pdf.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf_buffer = new \mPDF('', [210, $mpdf->h * 5],10,'arial', 15,15, 0, 0, 0, 0);
        $mpdf_buffer->img_dpi = 72;
        $mpdf_buffer->WriteHTML($stylesheet,1);

        $goodsListBlock = $this->renderPartial('export/_goods_list', $templateData);
        $goodsTotalBlock = $this->renderPartial('export/_goods_total', $templateData);
        $standartNoteBlock = $this->renderPartial('export/_standart_note', $templateData);

        $footerBlock = $this->renderPartial('export/_footer', $templateData);
        $noteBlock = $this->renderPartial('export/_note', $templateData);
        $signsBlock = $this->renderPartial('export/_signs', $templateData);

        $headerBlock = $this->renderPartial('export/_header', $templateData);

        $mpdf->WriteHTML($headerBlock);

        $pageHeight = $mpdf->h - $mpdf->bMargin - $mpdf->tMargin;
        $headerBlockHeight = PdfHelper::getBlockHeight($mpdf_buffer, $headerBlock);
        $availableHeight = $pageHeight - $headerBlockHeight;

        $block = $goodsListBlock . $goodsTotalBlock . $standartNoteBlock . $noteBlock . $signsBlock;
        $height = PdfHelper::getBlockHeight($mpdf_buffer, $block);

        if ($height > $availableHeight) {
            do {
                $nextPageGoods = [];
                $goodsListBlock = $this->renderPartial('export/_goods_list', $templateData);
                $goodsListHeight = PdfHelper::getBlockHeight($mpdf_buffer, $goodsListBlock);

                while ($goodsListHeight > $availableHeight - 5) {
                    $nextPageGoods[] = array_pop($templateData['goods']);
                    $goodsListBlock = $this->renderPartial('export/_goods_list', $templateData);
                    $goodsListHeight = PdfHelper::getBlockHeight($mpdf_buffer, $goodsListBlock);
                }

                if (count($nextPageGoods) > 0) {
                    $goodsListBlock = $this->renderPartial('export/_goods_list', $templateData);
                    $mpdf->WriteHTML($goodsListBlock);
                    $mpdf->WriteHTML('Продолжение на следующей странице');
                    $mpdf->AddPage();
                    $templateData['goods'] = $nextPageGoods;
                } else {
                    $block = $goodsTotalBlock . $standartNoteBlock . $noteBlock . $signsBlock;
                    $blockHeight = PdfHelper::getBlockHeight($mpdf_buffer, $block);
                    if ($goodsListHeight + $blockHeight < $availableHeight) {
                        $goodsListBlock = $this->renderPartial('export/_goods_list', $templateData);
                        PdfHelper::sliceAndWriteHtml($mpdf, $goodsListBlock);
                        PdfHelper::sliceAndWriteHtml($mpdf, $block);
                    } else {
                        $nextPageGoods[] = array_pop($templateData['goods']);
                        $goodsListBlock = $this->renderPartial('export/_goods_list', $templateData);
                        $mpdf->WriteHTML($goodsListBlock);
                        $mpdf->WriteHTML('Продолжение на следующей странице');
                        $mpdf->AddPage();
                        $templateData['goods'] = $nextPageGoods;
                    }
                }
                $availableHeight = $pageHeight;
            } while (count($nextPageGoods) > 0);
        } else {
            $block = $goodsListBlock . $goodsTotalBlock . $standartNoteBlock . $noteBlock . $signsBlock;
            PdfHelper::sliceAndWriteHtml($mpdf, $block);
        }

        if (!empty($model->promos_filenames)) {
            $model->promos_filenames = unserialize($model->promos_filenames);
        }

        $availableHeight = $pageHeight - $mpdf->y;
        $promoFilename = false;
        if ($availableHeight > 80) {
            if (isset($model->promos_filenames['8'])) {
                $promoFilename = $model->promos_filenames['8'];
            } elseif (isset($model->promos_filenames['5'])) {
                $promoFilename = $model->promos_filenames['5'];
            } elseif (isset($model->promos_filenames['2'])) {
                $promoFilename = $model->promos_filenames['2'];
            }
        } elseif ($availableHeight > 50) {
            if (isset($model->promos_filenames['5'])) {
                $promoFilename = $model->promos_filenames['5'];
            } elseif (isset($model->promos_filenames['2'])) {
                $promoFilename = $model->promos_filenames['2'];
            }
        } elseif ($availableHeight > 20) {
            if (isset($model->promos_filenames['2'])) {
                $promoFilename = $model->promos_filenames['2'];
            }
        }

        if ($promoFilename) {
            $templateData['promoFilename'] = $promoFilename;
            $promoBlock = $this->renderPartial('export/_promo', $templateData);
            PdfHelper::sliceAndWriteHtml($mpdf, $promoBlock);
        }

        PdfHelper::sliceAndWriteHtml($mpdf, $footerBlock);

        if (!empty($model->attachments_pages_ids)) {
            $model->attachments_pages_ids = unserialize($model->attachments_pages_ids);
            $mpdf->SetImportUse();
            foreach ($model->attachments_pages_ids as $page) {
                $page = \common\models\Attachment::findOne($page);
                if ($page) {
                    if ($page->type == \common\models\Attachment::TYPE_IMAGE) {
                        $mpdf->AddPage();
                        $mpdf->WriteHTML('<img src="' . Yii::getAlias('@attachments/' . $page->filename) . '">');
                    } elseif ($page->type == \common\models\Attachment::TYPE_PDF) {
                        $pageCount = $mpdf->SetSourceFile(
                            Yii::getAlias('@webroot/') . Yii::getAlias('@attachments/' . $page->filename)
                        );

                        $mpdf->AddPage();
                        for ($i = 1; $i <= $pageCount; $i++) {
                            $templateId = $mpdf->ImportPage($i);
                            $mpdf->UseTemplate($templateId);

                            if ($i < $pageCount)
                                $mpdf->AddPage();
                        }
                    }
                }
            }
        }

        return $mpdf->Output('', 'I');
    }

    protected function findModel($id)
    {
        if (($model = InvoiceTemplate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
