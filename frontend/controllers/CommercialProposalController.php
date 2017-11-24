<?php

namespace frontend\controllers;

use Yii;
use common\helpers\UploadHelper;
use frontend\helpers\PdfHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
use frontend\models\ContractorSearch;
use frontend\models\GoodSearch;
use yii\web\UploadedFile;
use common\models\CommercialProposal\CommercialProposal;
use common\models\CommercialProposal\CommercialProposalSearch;
use common\models\CommercialProposal\CommercialProposalEmail;
use common\models\CommercialProposal\CommercialProposalGood;
use common\models\CommercialProposal\CommercialProposalTemplate;
use common\models\CommercialProposal\CommercialProposalComment;
use common\models\CommercialProposal\CommercialProposalFile;


class CommercialProposalController extends Controller
{
    const MODULE_NAME = 'commercial-proposals';

    public static function getUserRules() {
        return [
            ['id' => 1, 'name' => 'Просматривать реестр коммерческих предложений'],
            ['id' => 2, 'name' => 'Создавать/редактировать коммерческие предложения'],
            ['id' => 3, 'name' => 'Менять данные шаблона в коммерческом предложении'],
            ['id' => 4, 'name' => 'Удалять коммерческие предложения'],
            ['id' => 5, 'name' => 'Редактировать коммерческое предложение после отправки'],
        ];
    }

    public function actionDeleteFile() {
        $file_id = Yii::$app->request->post('key');
        $result = CommercialProposalFile::deleteAll(['id' => $file_id]);
        return json_encode($result);
    }

    public function actionUploadImage() {
        $image = UploadedFile::getInstanceByName('upload');
        if ($image) {
            $directory = Yii::getAlias('@commercial_proposals_images');
            $filename = UploadHelper::uploadWithUniqueFilename($image, $directory);

            $answer = '
                <script type="text/javascript">
                    var CKEditorFuncNum = ' . Yii::$app->request->get('CKEditorFuncNum') . ';
                    window.parent.CKEDITOR.tools.callFunction(CKEditorFuncNum, '.json_encode($directory . '/' . $filename).', "");
                </script>';

            return $answer;
        }

        return false;
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
                        'actions' => ['index'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'get-pdf', 'upload-image', 'get-html'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 4));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' =>
                            [
                                'get-signers',
                                'get-template-data',
                                'add-comment',
                                'get-template-id',
                                'send-email',
                                'search-goods',
                                'search-contractors',
                                'get-good',
                                'get-goods',
                                'get-contractor-info',
                            ],
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
        $searchModel = new CommercialProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->view->params['fullWidth'] = true;

        $commercial_proposals = CommercialProposal::find()->all();
        $organizations = [];
        $subjects = [];
        $statuses = [];
        $managers = [];

        foreach ($commercial_proposals as $item) {
            $organizations[$item->organization->organization_id] = $item->organization->organization_name;
            $subjects[$item->subject->theme_id] = $item->subject->name;
            $statuses[$item->status_id] = $item->status->name;
            $managers[$item->manager_id] = $item->manager->getShortName();
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $searchModel,
            'organizations' => $organizations,
            'subjects' => $subjects,
            'statuses' => $statuses,
            'managers' => $managers,
        ]);
    }

    public function actionCreate($instance_id = false)
    {
        $model = new CommercialProposal();
        if ($model->load(Yii::$app->request->post())) {

            if (!CommercialProposal::findOne(['order_id' => $model->order_id])) {
                $model->primary = true;
            }

            $model->status_id = $model::STATUS_NEW;
            if ($model->save()) {
                $model->handleGoods();
                $model->handleAttachments();

                $model->addStatusComment($model::STATUS_NEW);

                return $this->redirect(['index']);
            }
        }

        if ($instance_id) {
            $instance = $this->findModel($instance_id);
            $model->setAttributes($instance->getAttributes());
            $model->status_id = CommercialProposal::STATUS_NEW;
            $model->primary = false;

            if ($model->save()) {
                $model->addStatusComment($model::STATUS_NEW);

                $instance->copyGoods($model->id);
                return $this->redirect(['update?id=' . $model->id]);
            } else {
                return $this->redirect(['index']);
            }
        }

        $data = $model->getFormData();

        $contractorSearchModel = new ContractorSearch();
        $data['contractorSearchModel'] = $contractorSearchModel;
        $goodSearchModel = new GoodSearch();
        $data['goodSearchModel'] = $goodSearchModel;

        $data['units'] = [];
        foreach (\common\models\Old\Unit::find()->createCommand()->queryAll() as $unit) {
            $data['units'][$unit['list_unit_id']] = $unit['list_unit_name'];
        }

        $data['permissions'] = $this->getUserPermissions($model);
        $data['model'] = $model;

        return $this->render('create', $data);
    }

    private function getUserPermissions($model) {
        $is_user_approver = false;
        if (!empty($model->template->needs_approval_by_ids)) {
            $approvers = unserialize($model->template->needs_approval_by_ids);
            $is_user_approver = in_array(Yii::$app->user->identity->getPostId(), $approvers);
        }

        $can_user_send = ($model->status_id == $model::STATUS_APPROVED || empty($model->template->needs_approval_by_ids));
        $can_edit_template_data = Yii::$app->user->identity->checkRule(self::MODULE_NAME, 3);
        $can_edit_after_send = Yii::$app->user->identity->checkRule(self::MODULE_NAME, 5);

        return [
            'can_edit_after_send' => $can_edit_after_send,
            'can_edit_template_data' => $can_edit_template_data,
            'is_user_approver' => $is_user_approver,
            'can_user_send' => $can_user_send
        ];
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $permissions = $this->getUserPermissions($model);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->status_id !== $model::STATUS_SENT || $permissions['can_edit_after_send']) {
                $new_status = Yii::$app->request->post('new_status');
                if ($new_status && $new_status != $model->status_id) {
                    if ($model->status_id != $model::STATUS_SENT) {
                        if ($new_status == $model::STATUS_APPROVED || $new_status == $model::STATUS_CORRECTION) {
                            if ($permissions['is_user_approver']) {
                                $model->status = $new_status;
                                $model->addStatusComment($new_status);
                            }
                        } else {
                            $model->status = $new_status;
                            $model->addStatusComment($new_status);
                        }
                    }
                } else if ($model->status_id == $model::STATUS_APPROVED && $model->hasChanges()) {
                    $model->status_id = $model::STATUS_CORRECTION;
                    $model->addStatusComment($model::STATUS_CORRECTION);
                }

                if ($model->save()) {
                    $model->handleGoods();
                    $model->handlePrimary();
                    $model->handleAttachments();
                    $model = $this->findModel($id);
                    $permissions = $this->getUserPermissions($model);
                }
            }
        }

        $data = $model->getFormData();
        $data['permissions'] = $permissions;

        $contractorSearchModel = new ContractorSearch();
        $data['contractorSearchModel'] = $contractorSearchModel;
        $goodSearchModel = new GoodSearch();
        $data['goodSearchModel'] = $goodSearchModel;

        $data['units'] = [];
        foreach (\common\models\Old\Unit::find()->createCommand()->queryAll() as $unit) {
            $data['units'][$unit['list_unit_id']] = $unit['list_unit_name'];
        }

        $data['model'] = $model;

        return $this->render('update', $data);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = false;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionGetTemplateData($template_id) {
        $template = CommercialProposalTemplate::findOne($template_id);
        if ($template) {
            $signers = [];
            foreach (\common\models\Old\Signer::findAll(['organization_id' => $template->organization_id]) as $row) {
                $signers[] = ['id' => $row['signatory_id'], 'name' => $row['signatory_name']];
            }

            $data = [
                'signers' => $signers,
                'signer_id' => $template->signer_id,
                'organization_id' => $template->organization_id,
                'prepayment_percentage' => $template->prepayment_percentage,
                'term_days' => $template->term_days,
                'delivery_stock' => $template->delivery_stock_id,
                'note' => $template->note,
                'attachments_pages_ids' => !empty($template->attachments_pages_ids) ? unserialize($template->attachments_pages_ids) : '',
            ];

            return json_encode($data);
        }
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

    public function actionAddComment() {
        $model = new CommercialProposalComment();
        $model->load(Yii::$app->request->post());
        $cp_id = $model->commercial_proposal_id;

        $success = false;
        if ($model->save()) {
            $success = true;
            $model = new CommercialProposalComment();
            $model->commercial_proposal_id = $cp_id;
        }

        return $this->renderPartial('pjax/_comments', ['model' => $model, 'success' => $success]);
    }

    public function actionGetTemplateId($subject_id, $amount) {
        $template = CommercialProposalTemplate::find()
            ->where(['<', 'amount_greater_than', $amount])
            ->andWhere(['>', 'amount_less_than', $amount])
            ->andWhere(['subject_id' => $subject_id])
        ->one();

        if ($template) {
            return json_encode($template->id);
        }

        return json_encode(false);
    }

    public function actionSendEmail() {
        $emailModel = new CommercialProposalEmail();
        $id = Yii::$app->request->post('commercial_proposal_id');
        $model = $this->findModel($id);

        $emailModel->load(Yii::$app->request->post());

        $status = $model->status_id;
        $success = false;
        if ($emailModel->validate()) {
            $success = $emailModel->send($this->actionGetPdf($id, 'S'));
            if ($success) {
                $success = true;
                $status = $model::STATUS_SENT;
            }
        }

        return $this->renderPartial('pjax/_send_email_form', [
            'status' => $status,
            'success' => $success,
            'model' => $emailModel,
            'commercial_proposal_id' => $id,
        ]);
    }

    public function actionSearchGoods() {
        $goodSearchModel = new GoodSearch();
        $query = $goodSearchModel->search(Yii::$app->request->post());
        if ($query) {
            $pages = new Pagination([
                'totalCount' => $query->count(),
            ]);
            $pages->setPage($goodSearchModel->page);
            $goods = $query->offset($pages->offset)->limit($pages->limit)->all();
        } else {
            $goods = [];
            $pages = new Pagination(['totalCount' => 0]);
        }

        foreach ($goods as $good) {
            $balance = $good->getStocksBalance();
            $good->freeAmounts = [
                'che' => $balance['stocks'][1][$good->goods_id] - $balance['reserved']['amount'][1][$good->goods_id],
                'avm' => $balance['stocks'][2][$good->goods_id] - $balance['reserved']['amount'][2][$good->goods_id],
            ];
        }

        return $this->renderPartial('pjax/_good_search_form', [
            'goodSearchModel' => $goodSearchModel,
            'goods' => $goods,
            'pages' => $pages,
        ]);
    }

    public function actionSearchContractors() {
        $contractorSearchModel = new ContractorSearch();
        $query = $contractorSearchModel->search(Yii::$app->request->post());

        if ($query) {
            $pages = new Pagination(['totalCount' => $query->count()]);
            $pages->setPage($contractorSearchModel->page);
            $contractors = $query->offset($pages->offset)->limit($pages->limit)->all();
            $contractorSearchModel->indentKeywords($contractors);
        } else {
            $pages = new Pagination(['totalCount' => 0]);
            $contractors = [];
        }

        return $this->renderPartial('pjax/_contractor_search_form', [
            'contractorSearchModel' => $contractorSearchModel,
            'contractors' => $contractors,
            'pages' => $pages,
        ]);
    }

    public function actionGetGood() {
        $good_id = Yii::$app->request->post('id');
        $good = \common\models\Old\Good::find()->where(['goods_id' => $good_id])->one();

        $good = [
            'good_id' => $good->goods_id,
            'name' => $good->goods_name,
            'unit_id' => !empty($good->unit) ? $good->unit->list_unit_id : 1,
            'delivery_period' => $good->delivery_period,
            'price' => $good->price,
            'mrc_percent' => $good->mrc,
            'base_price_percent' => $good->margin,
            'base_margin' => $good->margin,
            'extra_charge_percent' => $good->extra_charge,
            'weight' => $good->weight,
            'volume' => ($good->width / 1000) * ($good->height / 1000) * ($good->length / 1000),
        ];

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $good;
    }

    public function actionGetGoods() {
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');

        if ($type === 'catalog') {
            $goods = CommercialProposalGood::find()
                ->where(['commercial_proposal_id' => $id])
                ->andWhere(['not', ['good_id' => null]])
                ->orderBy('index')
                ->createCommand()
                ->queryAll()
            ;
        } else if ($type === 'additional') {
            $goods = CommercialProposalGood::find()
                ->where(['commercial_proposal_id' => $id])
                ->andWhere(['good_id' => null])
                ->orderBy('index')
                ->createCommand()
                ->queryAll()
            ;
        }

        return json_encode($goods);
    }

    public function actionGetContractorInfo($contractor_id)
    {
        if (Yii::$app->request->isAjax) {
            $contractor = \common\models\Old\Contractor::findOne($contractor_id);

            $payers = [];
            $contact_persons = [];

            foreach (\common\models\Old\Organization::findAll(['contractor_id' => $contractor_id]) as $row) {
                $payers[] = ['id' => 'o_' . $row['organization_id'], 'name' => Html::decode($row['organization_name'])];
            }

            foreach (\common\models\Old\ContactPerson::findAll(['contractor_id' => $contractor_id]) as $row) {
                $payers[] = ['id' => 'cp_' . $row['contact_person_id'], 'name' => $row['contact_person_name']];
                $contact_persons[] = ['id' => $row['contact_person_id'], 'name' => $row['contact_person_name']];
            }

            return json_encode([
                'contractor' => [
                    'contractor_id' => $contractor->contractor_id,
                    'contractor_name' => Html::decode($contractor->contractor_name),
                ],
                'payers' => $payers,
                'contact_persons' => $contact_persons,
            ]);
        }
    }

    public function actionGetPdf($id, $output = 'I')
    {
        $model = $this->findModel($id);
        $templateData = $model->getExportData();
        $templateData['model'] = $model;

        \yii\helpers\FileHelper::createDirectory('files/temp/', $mode = 0775, $recursive = true);
        define('_MPDF_TTFONTDATAPATH', 'files/temp/');
        $mpdf = new \mPDF('','A4',10,'arial');
        $mpdf->SetTitle('Коммерческое предложение №' . $model->id);
        $mpdf->SetHTMLFooter('<div style="text-align: right;">{PAGENO}</div>');
        $mpdf->img_dpi = 72;

        $stylesheet = file_get_contents(Yii::getAlias('@webroot/css/commercial-proposal/pdf.css'));
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
                    array_unshift($nextPageGoods, array_pop($templateData['goods']));
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
                        array_unshift($nextPageGoods, array_pop($templateData['goods']));
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

        if (!empty($model->template->promos_filenames)) {
            $promos_filenames = unserialize($model->template->promos_filenames);
        }

        $availableHeight = $pageHeight - $mpdf->y;
        $promoFilename = false;
        if ($availableHeight > 70) {
            if (isset($promos_filenames['8'])) {
                $promoFilename = $promos_filenames['8'];
            } elseif (isset($promos_filenames['5'])) {
                $promoFilename = $promos_filenames['5'];
            } elseif (isset($promos_filenames['2'])) {
                $promoFilename = $promos_filenames['2'];
            }
        } elseif ($availableHeight > 40) {
            if (isset($promos_filenames['5'])) {
                $promoFilename = $promos_filenames['5'];
            } elseif (isset($promos_filenames['2'])) {
                $promoFilename = $promos_filenames['2'];
            }
        } elseif ($availableHeight > 10) {
            if (isset($promos_filenames['2'])) {
                $promoFilename = $promos_filenames['2'];
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

        return $mpdf->Output('', $output);
    }

    protected function findModel($id)
    {
        if (($model = CommercialProposal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
