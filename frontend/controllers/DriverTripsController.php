<?php

namespace frontend\controllers;

use Yii;
use common\models\Driver\DriverTrip;
use common\models\Driver\DriverHistoryTrip;
use common\models\Driver\DriverTripSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Driver\DriverTypeTrip;
use common\models\Driver\DriverAddress;
use common\models\Driver\DriverPriority;
use common\models\Driver\DriverStatusTrip;
use common\models\User;
use common\models\Driver\DriverCar;
use common\models\Driver\DriverTripTicket;
use frontend\models\Driver;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class DriverTripsController extends Controller
{
    const MODULE_NAME = 'driver';

    public static function getUserRules() {
        return [
            ['id' => 1, 'name' => 'Можно видеть страницу «Реестр поездок», «Путевые листы»'],
            ['id' => 2, 'name' => 'Может ставить статус «В плане»'],
            ['id' => 3, 'name' => 'Может менять желаемую дату после ее занесения'],
            ['id' => 4, 'name' => 'Может нажать кнопу «Отменить поездку»'],
            ['id' => 5, 'name' => 'Может менять приоритет поездкам'],
            ['id' => 6, 'name' => 'Может ставить статус «Подготовлена» не своим поездкам'],
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
                        'actions' => ['index', 'create', 'update'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1));
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->view->params['fullWidth'] = true;
        
        if (Yii::$app->request->isAjax) {
            $trip = DriverTrip::findOne(Yii::$app->request->post('id'));
            $driver = new Driver();
            return json_encode(['goods' => $driver->getGoods(['id' => $trip->orderNumber, 'type' => $trip->typeOfTrip])]);
        }

        if (Yii::$app->request->post('reset') == 'reset') $this->refresh();
        if (Yii::$app->request->post('cancel') == 'cancel') $this->refresh();

        $searchModel = new DriverTripSearch();        
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        $typeOfTrip = DriverTypeTrip::find()->select(['name', 'id'])->indexBy('id')->column();
        $typeOfTrip['0'] = 'Все';
        ksort($typeOfTrip);
        $searchModel->typeOfTrip = $searchModel->typeOfTrip ? $searchModel->typeOfTrip : array_keys($typeOfTrip);

        $from = DriverAddress::find()->select(['name', 'id', 'from'])->where(['from' => 1])->indexBy('id')->column();
        unset($from[1]);
        $from['0'] = 'Все';
        ksort($from);
        $from[1] = 'Другое';
        $searchModel->from = $searchModel->from ? $searchModel->from : array_keys($from);

        $to = DriverAddress::find()->select(['name', 'id', 'to'])->where(['to' => 1])->indexBy('id')->column();
        unset($to[1]);
        $to['0'] = 'Все';
        ksort($to);
        $to[1] = 'Другое';
        $searchModel->to = $searchModel->to ? $searchModel->to : array_keys($to);

        $priority = DriverPriority::find()->select(['name', 'id'])->indexBy('id')->column();
        $priority['0'] = 'Все';
        ksort($priority);
        $searchModel->priority = $searchModel->priority ? $searchModel->priority : array_keys($priority);

        $region = ['Все', 1, 2, 3, 4, 5, 6, 7, 8];
        $searchModel->region = $searchModel->region ? $searchModel->region : array_keys($region);

        $users = DriverTrip::find()->select(['authorId'])->distinct()->indexBy('authorId')->all();
        $author = array();
        foreach ($users as $key => $userId) {
            $user = $userId->author;
            $author[$key] = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
        }
        $author['0'] = 'Все';
        ksort($author);
        $searchModel->authorId = $searchModel->authorId ? $searchModel->authorId : array_keys($author);

        $status = DriverStatusTrip::find()->select(['name', 'id'])->indexBy('id')->column();
        $status['0'] = 'Все';
        ksort($status);
        $searchModel->status =  $searchModel->status ? $searchModel->status : array_keys($status);

        $drivers = User::find()->select(['user_id', 'first_name', 'last_name', 'patronymic'])->where(['driver' => 1])->all();
        $driver = array();
        foreach ($drivers as $user) {
            $driver[$user->user_id] = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
        }
        $driver['0'] = 'Все';
        ksort($driver);
        $searchModel->driverId = $searchModel->driverId ? $searchModel->driverId : array();

        $car = DriverCar::find()->select(['name', 'id'])->indexBy('id')->column();
        $car['0'] = 'Все';
        ksort($car);
        $searchModel->carId = $searchModel->carId ? $searchModel->carId : array();

        $tripTicket = array();
        $tripTicketNew = DriverTripTicket::find()->where(['status' => 1])->all();
        if (!empty($tripTicketNew)) {
            foreach ($tripTicketNew as $item) {
                $user = $item->driverTripTicket;
                $tripTicket[$item->id] = '№ '.$item->id .', ' .
                    date('d.m.Y', $item->createdAt) .', ' .
                    $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.'.', ' .
                    $item->carTripTicket->name;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'typeOfTrip' => $typeOfTrip,
            'from' => $from,
            'to' => $to,
            'priority' => $priority,
            'region' => $region,
            'author' => $author,
            'status' => $status,
            'driver' => $driver,
            'car' => $car,
            'tripTicket' => $tripTicket
        ]);
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->post('type') != null) {
            $modelDriver = new Driver();
            return json_encode([
                'goods' => $modelDriver->getGoods(Yii::$app->request->post()),
                'info' => $modelDriver->getOrderInfo(Yii::$app->request->post())
            ]);
        }

        if (Yii::$app->request->isAjax) {
            $address = DriverAddress::find()->where(['id' => Yii::$app->request->post('id')])->limit(1)->one();
            return json_encode(['address' => $address->address, 'tk' => $address->tk, 'name' => $address->name, 'region' => $address->region]);
        }

        $model = new DriverTrip();
        $modelComment = new DriverHistoryTrip();
        $isAddressFrom = 0;
        $isAddressTo = 0;

        if ($model->load(Yii::$app->request->post()))
            if (Yii::$app->request->post('isAddressFrom') == 1 && Yii::$app->request->post('isAddressTo') == 1 ) {

                $model->status = 1;
                $model->createdAt = strtotime('now');
                $model->authorId = Yii::$app->user->identity->user_id;
                $model->dedline = Yii::$app->request->post('DriverTrip')['firstDate'];
                if ($model->save() && $this->addHistoryTrip($model->id, 'Создана поездка', 1)) {
                    return $this->redirect(['index']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Заполните правильно адреса доставок');
                $isAddressFrom = Yii::$app->request->post('isAddressFrom');
                $isAddressTo = Yii::$app->request->post('isAddressTo');
            } else {
                if (Yii::$app->request->isPost) {
                    $isAddressFrom = Yii::$app->request->post('isAddressFrom');
                    $isAddressTo = Yii::$app->request->post('isAddressTo');
                }
            }

        $model->firstDate = is_numeric($model->firstDate) ? date('d.m.Y', $model->firstDate) : $model->firstDate;

        return $this->render('create', [
            'model' => $model,
            'edit' => false,
            'modelComment' => $modelComment,
            'isAddressFrom' =>$isAddressFrom,
            'isAddressTo' => $isAddressTo
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelComment = new DriverHistoryTrip();
        $isAddressFrom = 1;
        $isAddressTo = 1;

        if (Yii::$app->request->isPost && Yii::$app->request->post('isAddressFrom') == 1 && Yii::$app->request->post('isAddressTo') == 1) {
            if (Yii::$app->request->post('save') == 'save') {

                if ( $model->status == 1 ) {
                        if ($this->saveModel($model, 'Редактировние поездки')) $this->refresh();;
                } elseif ( $model->status == 2 ) {
                    if ( (!empty(Yii::$app->request->post('DriverTrip')['desiredDateFrom']) || !empty($model->desiredDateFrom))
                        && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateTo']) || !empty($model->desiredDateTo)) ) {

                        if ($this->saveModel($model, 'Редактировние поездки')) $this->refresh();;
                    } else Yii::$app->session->setFlash('error', 'Заполните желаемые даты');

                } elseif ( $model->status > 2 ) {
                    if ( (!empty(Yii::$app->request->post('DriverTrip')['desiredDateFrom']) || !empty($model->desiredDateFrom))
                    && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateTo']) || !empty($model->desiredDateTo))
                    && (!empty(Yii::$app->request->post('DriverTrip')['dateTrip']) || !empty($model->desiredDateTo)) ) {

                        if ($this->saveModel($model, 'Редактировние поездки')) $this->refresh();;
                    } else Yii::$app->session->setFlash('error', 'Желаемые даты и дата поездки должны быть заполнены');
                }
            }

            if (Yii::$app->request->post('prepare') == 'prepare' ) {
                if ( (!empty(Yii::$app->request->post('DriverTrip')['desiredDateFrom']) || !empty($model->desiredDateFrom))
                    && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateTo']) || !empty($model->desiredDateTo)) ) {
                    
                        if ($this->saveModel($model, 'Установлен статус', 2)) $this->redirect(['index']);
                } else Yii::$app->session->setFlash('error', 'Заполните желаемые даты');
            }

            if (Yii::$app->request->post('in_plan') == 'in_plan') {
                if ( (!empty(Yii::$app->request->post('DriverTrip')['dateTrip']) || !empty($model->dateTrip))
                    && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateFrom']) || !empty($model->desiredDateFrom))
                    && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateTo']) || !empty($model->desiredDateTo)) ) {
                        if ($model->tripTicketId != null && $model->status == 5) {
                            $model->tripTicketId = null;
                            $model->position = null;
                        }
                        if ($this->saveModel($model, 'Установлен статус', 3)) $this->redirect(['index']);
                } else Yii::$app->session->setFlash('error', 'Желаемые даты и дата поездки должны быть заполнены');
            }

            if (Yii::$app->request->post('can_go') == 'can_go') {
                if ( (!empty(Yii::$app->request->post('DriverTrip')['dateTrip']) || !empty($model->dateTrip))
                    && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateFrom']) || !empty($model->desiredDateFrom))
                    && (!empty(Yii::$app->request->post('DriverTrip')['desiredDateTo']) || !empty($model->desiredDateTo)) ) {

                        if ($this->saveModel($model, 'Установлен статус', 4)) $this->redirect(['index']);
                } else Yii::$app->session->setFlash('error', 'Желаемые даты и дата поездки должны быть заполнены');
            }

            if (Yii::$app->request->post('cancel_trip') == 'cancel_trip' ) {

                $model->status = 7;
                $model->dateTrip = null;
                $model->dedline = null;

                if ($model->save(false)
                    && $this->addHistoryTrip($model->id, 'Отмена поездки', $model->status))
                        return $this->redirect(['index']);
            }

            if (Yii::$app->request->post('save_comment') == 'save_comment' 
                && $modelComment->load(Yii::$app->request->post()) ) {

                $modelComment->status = $model->status;
                $modelComment->trip = $id;
                $modelComment->author = Yii::$app->user->identity->user_id;
                $modelComment->createdAt = strtotime('now');

                if ($modelComment->save()) return $this->refresh();
            }
        } elseif (Yii::$app->request->isPost) {
            Yii::$app->session->setFlash('error', 'Адреса доставок вводите корректные');
        }

        $model->desiredDateFrom = is_numeric($model->desiredDateFrom) ? date('d.m.Y', $model->desiredDateFrom) : $model->desiredDateFrom;
        $model->desiredDateTo = is_numeric($model->desiredDateTo) ? date('d.m.Y', $model->desiredDateTo) : $model->desiredDateTo;
        $model->dateTrip = is_numeric($model->dateTrip) ? date('d.m.Y', $model->dateTrip) : $model->dateTrip;

        return $this->render('update', [
            'model' => $model,
            'modelComment' => $modelComment,
            'dataProvider' => $this->findDataProvider($id),
            'edit' => true,
            'isAddressFrom' =>$isAddressFrom,
            'isAddressTo' => $isAddressTo
        ]);
    }

    protected function findModel($id)
    {
        if (($model = DriverTrip::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

     protected function findDataProvider($id)
    {
        $query = DriverHistoryTrip::find()
            ->innerJoinWith('tripInfo')
            ->innerJoinWith('statusTrip')
            ->where(['trip' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['createdAt' => SORT_ASC]]
        ]);

        return $dataProvider;
    }

    public function saveModel($model, $comment, $status = null)
    {
        $desiredDateFrom = null;
        $desiredDateTo = null;
        $dateTrip = null;
        $tripTicketId = $model->tripTicketId;
        $position = $model->position;

        if (!empty(Yii::$app->request->post('DriverTrip')['desiredDateTo']))
            $model->dedline = Yii::$app->request->post('DriverTrip')['desiredDateTo'];
        else {
            $desiredDateTo = $model->desiredDateTo;
            if (empty($model->dedline)) $model->dedline = Yii::$app->request->post('DriverTrip')['firstDate'];
        }

        if (empty(Yii::$app->request->post('DriverTrip')['desiredDateFrom']))
            $desiredDateFrom = $model->desiredDateFrom;

        if ( empty(Yii::$app->request->post('DriverTrip')['dateTrip']))
            $dateTrip= $model->dateTrip;

        if ($status != null) $model->status = $status;

        if ( $model->load(Yii::$app->request->post()) ) {
            $model->desiredDateFrom = $desiredDateFrom ? $desiredDateFrom : $model->desiredDateFrom;
            $model->desiredDateTo = $desiredDateTo ? $desiredDateTo : $model->desiredDateTo;
            $model->dateTrip = $dateTrip ? $dateTrip : $model->dateTrip;
            $model->tripTicketId = $tripTicketId;
            $model->position = $position;

            if ($model->save()
                && $this->addHistoryTrip($model->id, $comment, $model->status))
                    return true;
            else return false;
        }
    }

    public function addHistoryTrip($trip_id, $comment, $status = null)
    {
        $modelHistory = new DriverHistoryTrip();
        $modelHistory->status = $status;
        $modelHistory->createdAt = strtotime('now');
        $modelHistory->author = Yii::$app->user->identity->user_id;
        $modelHistory->comment = $comment;
        $modelHistory->trip = $trip_id;

        return $modelHistory->save();
    }
}
