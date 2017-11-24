<?php

namespace frontend\controllers;

use Yii;
use common\models\Driver\DriverTripTicket;
use common\models\Driver\DriverTripTicketSearch;
use common\models\Driver\DriverTrip;
use common\models\Driver\DriverTripSearch;
use common\models\Driver\DriverHistoryTrip;
use common\models\Driver\DriverTraffic;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Driver\DriverStatusTripTicket;
use common\models\User;
use common\models\Driver\DriverCar;
use common\models\Driver\DriverAddress;
use frontend\models\Driver;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class DriverTripTicketsController extends Controller
{
    const MODULE_NAME = 'driver';

    public static function getUserRules() {
        return [
            ['id' => 7, 'name' => 'Может создавать путевой лист'],
            ['id' => 8, 'name' => 'Может редактировать путевой лист'],
            ['id' => 9, 'name' => 'Может ставить статус «Утвержден»'],
            ['id' => 10, 'name' => 'Может удалять путевой лист'],
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
                        'actions' => ['index'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'add-trips', 'print'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 7));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 8));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 10));
                        },
                    ]
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->post('reset') == 'reset') $this->refresh();
        
        $drivers = User::find()->select(['user_id', 'first_name', 'last_name', 'patronymic'])->where(['driver' => 1])->all();
        $driver = array();
        foreach ($drivers as $user) {
            $driver[$user->user_id] = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
        }
        $is_driver = in_array(Yii::$app->user->identity->user_id, array_keys($driver));

        $searchModel = new DriverTripTicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post(), $is_driver);

        $status = DriverStatusTripTicket::find()->select(['name', 'id'])->indexBy('id')->column();
        $status['0'] = 'Все';
        ksort($status);
        $searchModel->statusTripTicket =  $searchModel->statusTripTicket ? $searchModel->statusTripTicket : array_keys($status);

        $drivers = User::find()->select(['user_id', 'first_name', 'last_name', 'patronymic'])->where(['driver' => 1])->all();
        $driver = array();
        foreach ($drivers as $user) {
            $driver[$user->user_id] = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
        }
        $driver['0'] = 'Все';
        ksort($driver);
        $searchModel->driverTripTicket = $searchModel->driverTripTicket ? $searchModel->driverTripTicket : array_keys($driver);

        // Если заходит водитель, то галочка только у водителя
        if ( !Yii::$app->request->isPost && $is_driver ) {
             $searchModel->driverTripTicket = [Yii::$app->user->identity->user_id];
         } 

        $car = DriverCar::find()->select(['name', 'id'])->indexBy('id')->column();
        $car['0'] = 'Все';
        ksort($car);
        $searchModel->carTripTicket = $searchModel->carTripTicket ? $searchModel->carTripTicket : array_keys($car);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'driver' => $driver,
            'car' => $car
        ]);
    }

    public function actionCreate()
    {
        $this->view->params['fullWidth'] = true;

        $session = Yii::$app->session;

        if (Yii::$app->request->isAjax) {
            if ((int)Yii::$app->request->post('type') == 0) {
                $trips = $session->get('trips');
                $position = $trips[(int)Yii::$app->request->post('id')]['position'];
                unset($trips[(int)Yii::$app->request->post('id')]);
                foreach ($trips as $key => $trip) {
                    if ($trip['position'] > $position) $trips[$key]['position'] = $trip['position'] - 1;
                }
                $session->set('trips', $trips);
                $modelTrip = DriverTrip::findOne(Yii::$app->request->post('id'));
                if ($modelTrip->tripTicketId != null) {
                    $this->addHistoryTrip($modelTrip, 'Поездка удалена из путевого листа № '.$modelTrip->tripTicketId);
                    $modelTrip->tripTicketId = null;
                    $modelTrip->position = null;
                    if ($modelTrip->status == 5) $modelTrip->status = 3;
                    $modelTrip->save(false);
                }
                return json_encode(['remove' => 'success']);
            }
            if ((int)Yii::$app->request->post('type') == 1) {
                $trips = $session->get('trips');
                
                $oldPosition = $trips[(int)Yii::$app->request->post('id')]['position'];
                if ($oldPosition != 1) {
                    foreach ($trips as $key => $trip) {
                        if ($trip['position'] == $oldPosition - 1) {
                            $trips[$key]['position'] = $oldPosition;
                            break;
                        }
                    }
                    $trips[(int)Yii::$app->request->post('id')]['position'] = $oldPosition - 1;
                    $session->set('trips', $trips);
                    return json_encode(['up' => 'success']);
                } else return json_encode(['up' => 'error']);
            }
            if ((int)Yii::$app->request->post('type') == 2) {
                $trips = $session->get('trips');
                $oldPosition = $trips[(int)Yii::$app->request->post('id')]['position'];
                if ($oldPosition != count($trips)) {
                    foreach ($trips as $key => $trip) {
                        if ($trip['position'] == $oldPosition + 1) {
                            $trips[$key]['position'] = $oldPosition;
                            break;
                        }
                    }
                    $trips[(int)Yii::$app->request->post('id')]['position'] = $oldPosition + 1;
                    $session->set('trips', $trips);
                    return json_encode(['down' => 'success']);
                } else return json_encode(['down' => 'error']);
            }
        }

        $model = new DriverTripTicket();
        $searchModel = new DriverTripSearch();

        if (Yii::$app->request->post('create') == 'create' && $model->load( Yii::$app->request->post() ) ) {
            $model->createdAt = strtotime('now');
            $model->status = 1;
            $model->author = Yii::$app->user->identity->user_id;

            if ($model->save()) {
                $trips = $session->get('trips');

                foreach ($trips as $id => $trip) {
                    $modelTrip = DriverTrip::findOne($id);
                    $modelTrip->tripTicketId = $model->id;
                    $modelTrip->position = $trip['position'];
                    if ($modelTrip->save()) $this->addHistoryTrip($modelTrip, 'Поздка включена в путевой лист № '.$modelTrip->tripTicketId);
                }

                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('create_trip_ticket') == 'create_trip_ticket') {

            $trips = Yii::$app->request->post('DriverTripSearch')['trips'];
            $newArr = array();
            $position = 1;
            foreach ($trips as $id) {
                if ($id != 0) $newArr[$id] = ['id' => $id, 'position' => $position++];
            }
            $session->set('trips', $newArr);
            $this->refresh();
        }

        $trips = $session->get('trips');
        $tripsId = array();
        foreach ($trips as $trip) {
            $tripsId[] = $trip['id'];
        }

        $dataProvider = $searchModel->search( ['DriverTripSearch' => ['trips' => $tripsId]] );

        $driver = User::find()->select(['user_id', 'first_name', 'last_name', 'patronymic'])->where(['driver' => 1])->all();
        $drivers = array();
        foreach ($driver as $user) {
            $drivers[$user->user_id] = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
        }

        $car = DriverCar::find()->select(['name', 'id'])->indexBy('id')->column();

        $from = DriverAddress::find()->select(['name', 'id', 'from'])->where(['from' => 1])->indexBy('id')->column();
        unset($from[1]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'drivers' => $drivers,
                'car' => $car,
                'from' => $from
            ]);
        }
    }

    public function actionAddTrips()
    {
        if (Yii::$app->request->post('add_trips') == 'add_trips') {
            if (!empty(Yii::$app->request->post('DriverTripSearch')['tripTicketId'])) {
                $tripTicketId = (int)Yii::$app->request->post('DriverTripSearch')['tripTicketId'];
                $arrTripId = Yii::$app->request->post('DriverTripSearch')['trips'];
                $position = (int)DriverTrip::find()->select('position')->where(['tripTicketId' => $tripTicketId])->max('position');
                foreach ($arrTripId as $tripId) {
                    if ($tripId != 0) {
                        $modelTrip = DriverTrip::findOne($tripId);
                        $modelTrip->tripTicketId = $tripTicketId;
                        $modelTrip->position = ++$position;
                        $modelTrip->save(false);
                    }
                }
                return $this->actionUpdate($tripTicketId);
            } else {
                Yii::$app->session->setFlash('error', 'Необходимо выбрать путевой лист');
                return $this->redirect(['/driver-trips/index']);
            }
        }
    }

    public function actionUpdate($id)
    {
        $this->view->params['fullWidth'] = true;
        $session = Yii::$app->session;

        $model = $this->findModel($id);
        $modelComment = new DriverHistoryTrip();

        if (Yii::$app->request->post('yes_success') != null) {

            $modelTrip = DriverTrip::findOne(Yii::$app->request->post('yes_success'));
            $modelTrip->status = 6;

            if ($modelTrip->save(false) &&
                $this->addHistoryTrip($modelTrip, 'Поездка успешна') &&
                $this->isTripTicketDone()) {

                    $model->status = 4;
                    if ($model->save(false)) return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->post('no_success') != null) {
            $modelTrip = DriverTrip::findOne(Yii::$app->request->post('no_success'));
            $modelTrip->status = 3;
            $modelTrip->dateTrip += 24*60*60;
            $modelTrip->tripTicketId = null;
            $modelTrip->position = null;

            if ($modelTrip->save(false) && $modelComment->load(Yii::$app->request->post()) 
                && $this->addHistoryTrip($modelTrip, 'Поездка не удалась. Причина: '. $modelComment->comment)
                && $this->isTripTicketDone()) {

                    $model->status = 4;
                    if ($model->save(false)) return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->post('save') == 'save') {
            if ($model->load( Yii::$app->request->post()) && $model->save()) {
                DriverTrip::updateAll(['tripTicketId' => null, 'position' => null], ['=', 'tripTicketId', $id]);
                $trips = $session->get('trips');
                foreach ($trips as $idTrip => $trip) {
                    $modelTrip = DriverTrip::findOne($idTrip);
                    $modelTrip->tripTicketId = $model->id;
                    $modelTrip->position = $trip['position'];
                    $modelTrip->save();
                }
                return $this->refresh();
            }
        }
        if (Yii::$app->request->post('approve') == 'approve') {
            if ($this->isTripsReady()) {
                $model->status = 2;
                if ($model->load( Yii::$app->request->post()) && $model->save()) {
                    DriverTrip::updateAll(['tripTicketId' => null, 'position' => null], ['=', 'tripTicketId', $id]);
                    $trips = $session->get('trips');
                    foreach ($trips as $idTrip => $trip) {
                        $modelTrip = DriverTrip::findOne($idTrip);
                        $modelTrip->tripTicketId = $model->id;
                        $modelTrip->position = $trip['position'];
                        $modelTrip->status = 5;
                        $modelTrip->dateTrip = $model->departureDate;
                        if ($modelTrip->save()) $this->addHistoryTrip($modelTrip, 'Путевой лист № '.$modelTrip->tripTicketId.' с этой поездкой утвержден');
                    }
                    return $this->redirect(['index']);
                }
            } else $session->setFlash('error', 'Не все поездки можно везти');
            
        }
        if (Yii::$app->request->post('familiar') == 'familiar') {
            $model->status = 3;
            if ($model->save()) return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('new') == 'new') {
            $model->status = 1;
            if ($model->save()) {

                return $this->redirect(['index']);
            }
        }

        $modelForSession = DriverTrip::find()->where(['tripTicketId' => (int)$id])->select(['id', 'position'])->all();
        $arrTripId = array();
        foreach ($modelForSession as $trip) {
            $arrTripId[$trip->id] = ['id' => $trip->id, 'position' => $trip->position];
        }
        $session->set('trips', $arrTripId);

        $searchModel = new DriverTripSearch();
        $dataProvider = $searchModel->search( ['DriverTripSearch' => ['tripTicketId' => (int)$id]], true );

        $driver = User::find()->select(['user_id', 'first_name', 'last_name', 'patronymic'])->where(['driver' => 1])->all();
        $drivers = array();
        foreach ($driver as $user) {
            $drivers[$user->user_id] = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
        }

        $car = DriverCar::find()->select(['name', 'id'])->indexBy('id')->column();

        $from = DriverAddress::find()->select(['name', 'id', 'from'])->where(['from' => 1])->indexBy('id')->column();
        unset($from[1]);

        $model->departureDate = is_numeric($model->departureDate) ? date('d.m.Y H:i', $model->departureDate) : $model->departureDate;

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'modelComment' => $modelComment,
            'drivers' => $drivers,
            'car' => $car,
            'from' => $from
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        DriverTrip::updateAll(['tripTicketId' => null, 'position' => null], ['=', 'tripTicketId', $id]);
        return $this->redirect(['index']);
    }

    public function actionPrint($id)
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post('action') == 'change') {
                $model = DriverTraffic::find()
                    ->where(['trip_ticket' => Yii::$app->request->get('id'), 'number' => Yii::$app->request->post('num')])->one();
                $model->duration = (int)Yii::$app->request->post('duration');
                if ($model->save()) return json_encode(['save' => 'success']);
                else json_encode(['save' => 'error']); 
            } else {
               $route = Yii::$app->request->post();
                foreach ($route as $path) {
                    $model = new DriverTraffic();
                    $model->trip_ticket = Yii::$app->request->get('id');
                    $model->number = $path['num'];
                    $model->duration = $path['duration'];
                    $model->distance = $path['distance'];
                    $model->address_start = $path['addressStart'];
                    $model->address_finish = $path['addressFinish'];
                    $model->save();
                }
                return json_encode(['save' => 'success']); 
            }
        }

        $this->view->params['fullWidth'] = true;
        $tripTicket = $this->findModel($id);
        $trips = DriverTrip::find()->where(['tripTicketId' => $id])->orderBy('position ASC')->all();
        $points = DriverAddress::find()->select('id')->where(['and', ['from' => 1], ['to' => 1]])->indexBy('id')->column();
        unset($points[1]);

        $traffic = DriverTraffic::find()->where(['trip_ticket' => $id])->orderBy('number ASC')->indexBy('number')->all();
        $goods = new Driver();

        return $this->render('print', [
            'trips' => $trips,
            'tripTicket' => $tripTicket,
            'points' => $points,
            'traffic' => $traffic,
            'goods' => $goods->getGoodsTrips($trips)
        ]);
    }

    protected function findModel($id)
    {
        if (($model = DriverTripTicket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateTrips($id, $model, $approve = false)
    {
        DriverTrip::updateAll(['tripTicketId' => null, 'position' => null], ['=', 'tripTicketId', $id]);
        $trips = Yii::$app->session->get('trips');
        foreach ($trips as $idTrip => $trip) {
            $modelTrip = DriverTrip::findOne($idTrip);
            $modelTrip->tripTicketId = $model->id;
            $modelTrip->position = $trip['position'];
            if ($approve) $modelTrip->status = 5;
            $modelTrip->save();
        }
    }

    protected function isTripsReady()
    {
        $tripsId = array_keys(Yii::$app->session->get('trips'));
        $countTrips = DriverTrip::find()->where(['in', 'id', $tripsId])->andWhere(['in', 'status', [4, 5]])->count();
        if (count($tripsId) == $countTrips) return true;
        else return false;
    }

    protected function isTripTicketDone()
    {
        $tripsId = array_keys(Yii::$app->session->get('trips'));
        $countTrips = DriverTrip::find()->where(['in', 'id', $tripsId])->andWhere(['in', 'status', [3, 6]])->count();
        if (count($tripsId) == $countTrips) return true;
        else return false;
    }

    protected function addHistoryTrip($model, $comment)
    {
        $modelComment = new DriverHistoryTrip();

        $modelComment->status = $model->status;
        $modelComment->trip = $model->id;
        $modelComment->author = Yii::$app->user->identity->user_id;
        $modelComment->createdAt = strtotime('now');
        $modelComment->comment = $comment;

        return $modelComment->save();
    }
}
