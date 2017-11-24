<?php

namespace frontend\controllers;

use Yii;
use common\models\Production\ProductionOrder;
use common\models\Production\ProductionOrderSearch;
use common\models\Production\ProductionPriority;
use common\models\Production\ProductionTarget;
use common\models\Production\ProductionTheme;
use common\models\Production\ProductionTypeGood;
use common\models\Production\ProductionTypeOrder;
use common\models\Production\ProductionStatusOrder;
use common\models\Production\ProductionStageOrder;
use common\models\Production\ProductionStagePrepare;
use common\models\Production\ProductionPrepareOrder;
use common\models\Production\ProductionOrderComment;
use common\models\Production\ProductionOrderPlanning;
use common\models\Production\ProductionOrderFile;
use common\models\Production\ProductionOrderHistory;
use common\models\User;
use common\models\Old\Order;
use common\models\Old\OrderGood;
use common\models\Old\OrderLab;
use common\models\Old\Good;
use frontend\models\Driver;
use frontend\models\GoodSearch;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class ProductionOrderController extends Controller
{
    const MODULE_NAME = 'production-order';

    public static function getUserRules() {
        return [
            ['id' => 1, 'name' => 'Может просматривать реестр товаров в производстве'],
            ['id' => 2, 'name' => 'Может создавать заказ-наряд'],
            ['id' => 3, 'name' => 'Может менять дедлайн и приоритет'],
            ['id' => 4, 'name' => 'Может согласовывать заказ-наряд'],
            ['id' => 5, 'name' => 'Может делать планирование'],
            ['id' => 6, 'name' => 'Может удалять заказ-наряд'],
            ['id' => 7, 'name' => 'Может менять последовательность заказ-нарядов'],
            ['id' => 8, 'name' => 'Может утверждать этапы'],
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
                        'actions' => ['index', 'update'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest &&
                                    Yii::$app->user->identity->checkRule(self::MODULE_NAME, 6) &&
                                    Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2));
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->view->params['fullWidth'] = true;

        if (Yii::$app->request->isAjax && Yii::$app->request->post('type') == 'comment') {
            $modelComments = ProductionOrderComment::find()->where(['order' => Yii::$app->request->post('id')])->orderBy('createdAt DESC')->all();
            $comments = array();
            foreach ($modelComments as $comment) {
                $comments[] = [
                    'author' => $comment->authorComment->shortName,
                    'date' => date('d.m.Y H:i', $comment->createdAt),
                    'comment' => $comment->comment
                ];
            }
            return json_encode($comments);
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->post('type') == 'sequence') {
            $sequence = !empty(Yii::$app->request->post('sequence')) ? (int)Yii::$app->request->post('sequence') : 0;
            $id = Yii::$app->request->post('id');
            $model = $this->findModel($id);
            if (empty($model->sequence)) {
                $modelOrder = ProductionOrder::find()->where(['and', ['is not', 'sequence', null], ['>=', 'sequence', $sequence]])->all();
                foreach ($modelOrder as $order) {
                    if ($order->posSection == $order->sequence && !empty($order->section)) {
                        ProductionOrder::updateAll(['posSection' => $order->sequence + 1], ['section' => $order->section]);
                    }
                    $order->sequence = $order->sequence + 1;
                    $order->update(false);
                }
                $model->sequence = $sequence;
                if (($model->posSection == null || $sequence < $model->posSection) && !empty($model->section))
                    ProductionOrder::updateAll(['posSection' => $sequence], ['section' => $model->section]);
                $model->save(false);
            } else {
                if (!empty($sequence)) {
                    $start = ($sequence > $model->sequence) ? $model->sequence : $sequence;
                    $end = ($sequence > $model->sequence) ? $sequence : $model->sequence;
                    $modelOrder = ProductionOrder::find()->where(['and', ['is not', 'sequence', null], ['between', 'sequence', $start, $end]])->all();
                    foreach ($modelOrder as $order) {
                        if ($order->posSection == $order->sequence && !empty($order->section)) {
                            ProductionOrder::updateAll(['posSection' => ($sequence > $model->sequence) ? $order->sequence - 1 : $order->sequence + 1],
                                ['section' => $order->section]);
                        }
                        $order->sequence = ($sequence > $model->sequence) ? $order->sequence - 1 : $order->sequence + 1;
                        $order->update(false);
                    }
                    $model->sequence = $sequence;
                    $model->save(false);
                    if (!empty($model->section)) {
                        $minSequence  = ProductionOrder::find()->where(['and', ['is not', 'sequence', null], ['section' => $model->section]])->min('sequence');
                        ProductionOrder::updateAll(['posSection' => $minSequence], ['section' => $model->section]);
                    }
                } else {
                    $modelOrder = ProductionOrder::find()->where(['and', ['is not', 'sequence', null], ['>', 'sequence', $model->sequence]])->all();
                    foreach ($modelOrder as $order) {
                        if ($order->posSection == $order->sequence && !empty($order->section)) {
                            ProductionOrder::updateAll(['posSection' => $order->sequence - 1], ['section' => $order->section]);
                        }
                        $order->sequence = $order->sequence - 1;
                        $order->update(false);
                    }
                    if ($model->posSection == $model->sequence) {
                        $model->sequence = null;
                        $model->save(false);
                        if (!empty($model->section)) {
                            $minSequence = ProductionOrder::find()->where(['and', ['is not', 'sequence', null], ['section' => $model->section]])->min('sequence');
                            ProductionOrder::updateAll(['posSection' => $minSequence], ['section' => $model->section]);
                        }
                    } else {
                        $model->sequence = null;
                        $model->save(false);
                    }
                }
            }
            return json_encode(['sequence' => 'success']);
        }

        if (Yii::$app->request->isAjax) {
            $modelPrepare = ProductionPrepareOrder::findOne(Yii::$app->request->post('id'));
            $modelPrepare->isPrepare = 1;
            if ($modelPrepare->save()) {
                $this->addHistoryOrder($modelPrepare->order, 2, 'Согласовано: ' . $modelPrepare->stagePrepare->name);
                if (ProductionPrepareOrder::find()
                    ->where(['and', ['order' => $modelPrepare->order], ['isPrepare' => 1]])
                    ->count() == ProductionStagePrepare::find()->count()) {
                    $model = ProductionOrder::findOne($modelPrepare->order);
                    $model->status = 3;
                    if ($model->save()) $this->addHistoryOrder($model->id, 3, 'Установлен новый статус');
                }
                return json_encode(['prepare' => 'success', 'id' => $modelPrepare->order, 'canDo' => !empty($model)]);
            } else return json_encode(['prepare' => 'error']);
        }

        if (Yii::$app->request->post('reset') == 'reset') {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('addComment') == 'addComment') {
            $modelComment = new ProductionOrderComment();
            $modelComment->order = Yii::$app->request->post('id');
            $modelComment->createdAt = strtotime('now');
            $modelComment->author = Yii::$app->user->identity->user_id;
            $modelComment->comment = Yii::$app->request->post('comment');
            if ($modelComment->save()) $this->refresh();
        }

        $searchModel = new ProductionOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        $priority = ProductionPriority::find()->select(['name','id'])->indexBy('id')->column();
        $priority[0] = 'Все';
        ksort($priority);
        $searchModel->priorities = $searchModel->priorities ? $searchModel->priorities : array_keys($priority);

        $target = ProductionTarget::find()->select(['name','id'])->indexBy('id')->column();
        $target[0] = 'Все';
        ksort($target);
        $searchModel->targets = $searchModel->targets ? $searchModel->targets : array_keys($target);

        $theme = ProductionTheme::find()->select(['name','id'])->indexBy('id')->column();
        $theme[0] = 'Все';
        ksort($theme);
        $searchModel->themes = $searchModel->themes ? $searchModel->themes : array_keys($theme);

        $typeGood = ProductionTypeGood::find()->select(['name','id'])->indexBy('id')->column();
        $typeGood[0] = 'Все';
        ksort($typeGood);
        $searchModel->typesGood = $searchModel->typesGood ? $searchModel->typesGood : array_keys($typeGood);

        $typeOrder = [0 => 'Все', 1 => 'Товар', 2 =>'Услуга'];
        $searchModel->typesOrder = $searchModel->typesOrder ? $searchModel->typesOrder : array_keys($typeOrder);

        $responsible = $this->getUsers('responsible');
        $otk = $this->getUsers('otk');

        $status = ProductionStatusOrder::find()->select(['name','id'])->indexBy('id')->column();
        $status[0] = 'Все';
        ksort($status);
        $searchModel->statuses = $searchModel->statuses ? $searchModel->statuses : array_keys($status);

        $stage = ProductionStageOrder::find()->select(['name','id'])->indexBy('id')->column();
        $stage[0] = 'Все';
        ksort($stage);

        $sequence = ProductionOrder::find()->select(['sequence'])->where(['>', 'sequence', 0])->distinct()->indexBy('sequence')->orderBy('sequence')->column();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'priority' => $priority,
            'target' => $target,
            'theme' => $theme,
            'typeGood' => $typeGood,
            'typeOrder' => $typeOrder,
            'responsible' => $responsible,
            'otk' => $otk,
            'status' => $status,
            'stage' => $stage,
            'sequence' => $sequence
        ]);
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            $modelDriver = new Driver();
            
            $orderNumber = strtr(Yii::$app->request->post('orderNumber'), ['С' => 'C', 'Т' => 'T']);
            $result = $modelDriver->getGoodsProduction($orderNumber);

            foreach ($result['goods'] as $key => $good) {
                if (ProductionOrder::find()->where(['and', ['good' => $good['id']], ['order' => $orderNumber]])->exists())
                    unset($result['goods'][$key]);
            }
            return json_encode($result);
        }

        $model = new ProductionOrder();
        $model->scenario = 'create';
        $goodSearchModel = new GoodSearch();

        if ($model->load(Yii::$app->request->post())) {
            if (($model->target == 1 && !empty(Yii::$app->request->post('ids'))) || $model->target == 2) {
                $ids = Yii::$app->request->post('ids');
                foreach ($ids as $id) {
                    $model = new ProductionOrder();
                    $model->load(Yii::$app->request->post());
                    $model->good = $id;
                    $model->createdAt = strtotime('now');
                    $model->author = Yii::$app->user->identity->user_id;
                    $model->typeGood = (int)Yii::$app->request->post('typeGood_' . $id);
                    $model->status = 1;
                    if (!empty($model->goodOrder->goods_name)) {
                        $section = explode('-', $model->goodOrder->goods_name)[0];
                        $model->section = $section;
                        $posSection = ProductionOrder::find()->where(['section' => $section])->limit(1)->one();
                        if (!empty($posSection)) $model->posSection = $posSection->posSection;
                    }
                    if ($model->target == 1) {
                        $model->countOrder = (int)Yii::$app->request->post('countOrder_' . $id);
                        $model->number = $model->order . '-' . Yii::$app->request->post('position_' . $id);
                    } else $model->countStock = (int)Yii::$app->request->post('countStock_' . $id);
                    if ($model->save()) {
                        $this->addHistoryOrder($model->id, 1, 'Создан заказ-наряд');
                        $stagePrepare = ProductionStagePrepare::find()->all();
                        foreach ($stagePrepare as $stage) {
                            $modelPrepare = new ProductionPrepareOrder();
                            $modelPrepare->order = $model->id;
                            $modelPrepare->stage = $stage->id;
                            $modelPrepare->isPrepare = 0;
                            $modelPrepare->save();
                        }
                    };
                }
                return $this->redirect(['index']);
            }
            elseif ($model->target == 1 && empty(Yii::$app->request->post('ids'))) 
                Yii::$app->session->setFlash('error', 'Не выбрано ни одного изделия!');
            elseif ($model->target == 3) {
                $model->createdAt = strtotime('now');
                $model->author = Yii::$app->user->identity->user_id;
                $model->typeGood = 2;
                $model->status = 1;
                if ($model->save()) {
                    $this->addHistoryOrder($model->id, 1, 'Создан заказ-наряд');
                    $stagePrepare = ProductionStagePrepare::find()->all();
                    foreach ($stagePrepare as $stage) {
                        $modelPrepare = new ProductionPrepareOrder();
                        $modelPrepare->order = $model->id;
                        $modelPrepare->stage = $stage->id;
                        $modelPrepare->isPrepare = 0;
                        $modelPrepare->save();
                    }
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
                'model' => $model,
                'goodSearchModel' => $goodSearchModel
            ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $goodSearchModel = new GoodSearch();

        if (Yii::$app->request->isAjax) {
            $file = ProductionOrderFile::findOne(Yii::$app->request->post('id'));
            $comment = 'Удален файл: ' . $file->name;
            if ($this->addHistoryOrder($id, $model->status, $comment))
                $file->delete();
            return json_encode(['id' => Yii::$app->request->post('id')]);
        }

        if (Yii::$app->request->post('agreed') == 'agreed') {
            $model->status = 2;
            if ($model->save(false)) {
                $this->addHistoryOrder($model->id, $model->status, 'Установлен новый статус');
                return $this->redirect(['index']);
            }
        }

        $count = ProductionStageOrder::find()->count();

        if (Yii::$app->request->post('planning') == 'planning') {
            $model->responsible = Yii::$app->request->post('ProductionOrder')['responsible'];
            $model->save(false);

            ProductionOrderPlanning::deleteAll(['and', ['order' => $id], ['in', 'status', [1, 2]]]);
            $stagesOrder = ProductionOrderPlanning::find()->select('stage')->where(['order' => $id])->column();
            
            $stages = Yii::$app->request->post('stages');
            $stages[] = $count-1; $stages[] = $count;
            $stages = array_diff($stages, $stagesOrder);

            foreach ($stages as $stage) {
                $modelPlanning = new ProductionOrderPlanning();
                $modelPlanning->order = $id;
                $modelPlanning->stage = $stage;
                $modelPlanning->status = 1;
                $modelPlanning->save();
            }
            $this->addHistoryOrder($model->id, $model->status, 'Произвели планирование');
            return $this->refresh();
        }

        if (Yii::$app->request->post('inWork') == 'inWork') {
            $modelPlanning = ProductionOrderPlanning::find()->where(['and', ['order' => $id], ['in', 'status', [1, 2]]])->orderBy('stage ASC')->limit(1)->one();
            $modelPlanning->status = 2;
            if ($modelPlanning->save()) {
                $model->status = 4;
                if (!empty($model->sequence)) {
                    $modelOrder = ProductionOrder::find()->where(['and', ['is not', 'sequence', null], ['>', 'sequence', $model->sequence]])->all();
                    foreach ($modelOrder as $order) {
                        if ($order->posSection == $order->sequence && !empty($order->section)) {
                            ProductionOrder::updateAll(['posSection' => $order->sequence - 1], ['section' => $order->section]);
                        }
                        $order->sequence = $order->sequence - 1;
                        $order->update(false);
                    }
                }
                $model->sequence = 0;
                if (($model->posSection == null || $model->posSection > 0) && !empty($model->section))
                    ProductionOrder::updateAll(['posSection' => 0], ['section' => $model->section]);
                $model->stage = $modelPlanning->stage;
                if ($model->save(false)) {
                    $this->addHistoryOrder($model->id, $model->status, 'Установлен новый статус');
                    return $this->redirect(['index']);
                }
            }
        }

        if (Yii::$app->request->post('pause') == 'pause') {
            $model->status = 5;
            if ($model->save(false)) {
                $this->addHistoryOrder($model->id, $model->status, 'Установлен новый статус');
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->post('cancel') == 'cancel') {
            $model->status = 6;
            if ($model->save(false)) {
                $this->addHistoryOrder($model->id, $model->status, 'Установлен новый статус');
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->post('save_file') == 'save_file') {
            $model->orderFiles = UploadedFile::getInstances($model, 'orderFiles');
            if ($model->upload())
                if ($model->stage == 7) {
                    $model->otk = Yii::$app->request->post('ProductionOrder')['otk'];
                    $model->save(false);
                }
                return $this->refresh();
        }

        if (Yii::$app->request->post('finish_stage') == 'finish_stage') {
            $model->orderFiles = UploadedFile::getInstances($model, 'orderFiles');
            if ($model->upload()) {
                if ($model->stage == 7) {
                    $model->otk = Yii::$app->request->post('ProductionOrder')['otk'];
                    $model->save(false);
                }
                if (ProductionOrderFile::find()->where(['and', ['order' => $id], ['stage' => $model->stage]])->exists()) {
                    $modelPlanning = ProductionOrderPlanning::find()->where(['and', ['order' => $id], ['stage' => $model->stage]])->one();
                    $modelPlanning->status = 3;
                    if ($modelPlanning->save()) {
                        $this->addHistoryOrder($model->id, $model->status, 'Завершен этап производства: ' . $modelPlanning->nameStage->name);
                        $stage = ProductionOrderPlanning::find()->where(['and', ['order' => $id], ['in', 'status', [1, 2]]])->min('stage');
                        if (!empty($stage)) {
                            $modelPlanning = ProductionOrderPlanning::find()->where(['and', ['order' => $id], ['stage' => $stage]])->one();
                            $modelPlanning->status = 2;
                            if ($modelPlanning->save()) {
                                $model->stage = $stage;
                                if ($model->save(false)) return $this->redirect(['index']);
                            }
                        } else {
                            $model->stage = null;
                            $model->status = 7;
                            $model->finishedAt = strtotime('now');
                            if ($model->save(false)) {
                                $this->addHistoryOrder($model->id, $model->status, 'Установлен новый статус');
                                return $this->redirect(['index']);
                            }
                        }
                    }
                }
                else {
                    Yii::$app->session->setFlash('error', 'Не загружено ни одного файла');
                    return $this->refresh();
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        $model->dedline = is_numeric($model->dedline) ? date('d.m.Y', $model->dedline) : $model->dedline;

        $stages = ProductionStageOrder::find()->select(['name','id'])->indexBy('id')->limit($count-2)->column();
        $selectStages = ProductionOrderPlanning::find()->where(['order' => $id])->indexBy('stage')->orderBy('stage ASC')->all();

        $responsibles = User::getUsersParams('master', 1);
        $usersOtk = User::getUsersParams('otk', 1);

        $orderStageFiles = ProductionOrderFile::find()->where(['order' => $id])->orderBy('stage ASC')->all();
        $stagesFiles = array();
        foreach ($orderStageFiles as $file) {
            $stagesFiles[$file->stage][] = $file;
        }

        if ($model->target == 1) {
            $maxQuantity = 0;
            $order = Order::find()->where(['contract_number' => $model->order])->one();
            if (empty($order) && is_numeric($model->order))
                $order = Order::find()
                    ->where(['and', ['order_number' => $model->order], ['contract_number' => '']])
                    ->one();
            if ($order->order_type == 2)
                $maxQuantity = OrderGood::find()->where(['and', ['order_id' => $order->order_id], ['goods_id' => $model->good]])->one()->amount;
            elseif ($order->order_type == 1)
                $maxQuantity = OrderLab::find()->where(['and', ['order_id' => $order->order_id], ['order_lab_id' => $model->good]])->one()->amount;
            elseif ($order->order_type == 0)
                $maxQuantity = OrderSiz::find()->where(['and', ['order_id' => $order->order_id], ['order_siz_id' => $model->good]])->one()->amount;
        } else $maxQuantity = 0;

        $query = ProductionOrderHistory::find()->where(['order' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC, 'id' => SORT_DESC]]
        ]);

        return $this->render('update', [
                'model' => $model,
                'goodSearchModel' => $goodSearchModel,
                'stages' => $stages,
                'selectStages' => $selectStages,
                'responsibles' => $responsibles,
                'usersOtk' => $usersOtk,
                'stagesFiles' => $stagesFiles,
                'maxQuantity' => $maxQuantity,
                'dataProvider' => $dataProvider
            ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = ProductionOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function getUsers($role)
    {
        switch ($role) {
            case 'responsible':
                $column = 'responsible';
                $get = 'responsibleOrder';
                break;
            case 'otk':
                $column = 'otk';
                $get = 'otkOrder';
                break;
        }

        $users = ProductionOrder::find()->select([$column])->distinct()->indexBy($column)->all();
        $result = array();
        foreach ($users as $key => $userId) {
            $user = $userId->$get;
            if (!empty($user))
                $result[$key] = $user->shortName;
        }
        $result['0'] = 'Все';
        ksort($result);
        return $result;
    }

    private function addHistoryOrder($order, $status, $comment)
    {
        $modelHistory = new ProductionOrderHistory();
        $modelHistory->order = $order;
        $modelHistory->createdAt = strtotime('now');
        $modelHistory->author = Yii::$app->user->identity->user_id;
        $modelHistory->status = $status;
        $modelHistory->comment = $comment;
        return $modelHistory->save();
    }
}
