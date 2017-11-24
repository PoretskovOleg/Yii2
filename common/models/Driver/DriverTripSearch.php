<?php

namespace common\models\Driver;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class DriverTripSearch extends DriverTrip
{
    public $tripId;
    public $trips;
    public $tripTicketId;
    public $organization;
    public $orderNumber;
    public $typeOfTrip;
    public $from;
    public $to;
    public $priority;
    public $region;
    public $authorId;
    public $status;
    public $driverId;
    public $carId;
    public $dateCreateFrom;
    public $dateCreateTo;
    public $dateFirstFrom;
    public $dateFirstTo;
    public $desiredDateBegin;
    public $desiredDateEnd;
    public $dateTripFrom;
    public $dateTripTo;

    public function rules()
    {
        return [
            [['tripId', 'tripTicketId'], 'integer'],
            [['organization', 'orderNumber'], 'string'],
            [
                [
                    'typeOfTrip', 'from','to','priority','region',
                    'authorId','status','driverId','carId','dateCreateFrom',
                    'dateCreateTo','dateFirstFrom','dateFirstTo','desiredDateBegin',
                    'desiredDateEnd','dateTripFrom','dateTripTo', 'trips', 'tripTicketId'
                ], 'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'tripId' => '№ поездки',
            'tripTicketId' => '№ пут. листа',
            'orderNumber' => '№ документа (заказ, закупка, перемещение)',
            'typeOfTrip' => 'Тип поездки',
            'from' => 'Откуда',
            'to' => 'Куда',
            'priority' => 'Приоритет',
            'region' => 'Р-он МСК',
            'authorId' => 'Автор',
            'status' => 'Статус',
            'driverId' => 'Водитель',
            'carId' => 'Автомобиль',
            'dateFirstFrom' => 'Первичная дата',
            'dateCreateFrom' => 'Дата создания',
            'desiredDateBegin' => 'Желаемая дата',
            'dateTripFrom' => 'Дата поездки',
            'organization' => 'Организация'
        ];
    }

    public function beforeValidate()
    {
        $this->dateFirstFrom = 
            (!empty($this->dateFirstFrom) && !is_numeric($this->dateFirstFrom)) ?
                date_create_from_format('d.m.Y H:i:s', $this->dateFirstFrom.' 00:00:01')->format('U') : $this->dateFirstFrom;
        $this->dateCreateFrom = 
            (!empty($this->dateCreateFrom) && !is_numeric( $this->dateCreateFrom)) ?
                date_create_from_format('d.m.Y H:i:s', $this->dateCreateFrom.' 00:00:01')->format('U') : $this->dateCreateFrom;
        $this->desiredDateBegin = 
            (!empty($this->desiredDateBegin) && !is_numeric($this->desiredDateBegin)) ?
                date_create_from_format('d.m.Y H:i:s', $this->desiredDateBegin.' 00:00:01')->format('U') : $this->desiredDateFrom;
        $this->dateTripFrom = 
            (!empty($this->dateTripFrom) && !is_numeric($this->dateTripFrom)) ? 
                date_create_from_format('d.m.Y H:i:s', $this->dateTripFrom.' 00:00:01')->format('U') : $this->dateTripFrom;
        $this->dateFirstTo = 
            (!empty($this->dateFirstTo) && !is_numeric($this->dateFirstTo)) ? 
                date_create_from_format('d.m.Y H:i:s', $this->dateFirstTo.' 23:59:59')->format('U') : $this->dateFirstTo;
        $this->dateCreateTo = 
            (!empty($this->dateCreateTo) && !is_numeric($this->dateCreateTo)) ? 
                date_create_from_format('d.m.Y H:i:s', $this->dateCreateTo.' 23:59:59')->format('U') : $this->dateCreateTo;
        $this->desiredDateEnd = 
            (!empty($this->desiredDateEnd) && !is_numeric($this->desiredDateEnd)) ? 
                date_create_from_format('d.m.Y H:i:s', $this->desiredDateEnd.' 23:59:59')->format('U') : $this->desiredDateTo;
        $this->dateTripTo = 
            (!empty($this->dateTripTo) && !is_numeric($this->dateTripTo)) ? 
                date_create_from_format('d.m.Y H:i:s', $this->dateTripTo.' 23:59:59')->format('U') : $this->dateTripTo;
        return parent::beforeValidate();
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $edit = false)
    {
        if (empty($params) && Yii::$app->request->get('page') == null) {
            $this->status = [1, 2, 3, 4, 5];
            Yii::$app->session->set('params', null);
        } elseif (!empty($params)) {
            Yii::$app->session->set('params', $params);
        } else {
            $params = Yii::$app->session->get('params');
            if (empty($params)) $this->status = [1, 2, 3, 4, 5];
        }
        
        $query = DriverTrip::find()
            ->innerJoinWith('typeTrip')
            ->innerJoinWith('statusTrip');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
            ],
            'sort' => ['defaultOrder' => $edit ? ['position' => SORT_ASC] : ['dedline' => SORT_ASC, 'priority' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->orderNumber) && !is_numeric($this->orderNumber)) {
            $number = explode('-', $this->orderNumber);
            if ( $number[0] === 'Т' ) {
                $number[0] = 'T';
                $this->orderNumber = implode('-', $number);
            }
            if ( $number[0] === 'С' ) {
                $number[0] = 'C';
                $this->orderNumber = implode('-', $number);
            }
        }

        $query->andFilterWhere([
            'driver_trips.id' => $this->tripId,
            'driver_trips.tripTicketId' => $this->tripTicketId,
            'driver_trips.orderNumber' => $this->orderNumber
        ]);

        $query
            ->andFilterWhere(['or', ['like', 'driver_trips.consignerName', $this->organization], ['like', 'driver_trips.consigneeName', $this->organization]])
            ->andFilterWhere(['in', 'driver_trips.id', $this->trips])
            ->andFilterWhere(['in', 'driver_trips.typeOfTrip', $this->typeOfTrip])
            ->andFilterWhere(['in', 'driver_trips.from', $this->from])
            ->andFilterWhere(['in', 'driver_trips.to', $this->to])
            ->andFilterWhere(['in', 'driver_trips.priority', $this->priority])
            ->andFilterWhere(['or', ['in', 'driver_trips.zoneFrom', $this->region], ['in', 'driver_trips.zoneTo', $this->region]])
            ->andFilterWhere(['in', 'driver_trips.authorId', $this->authorId])
            ->andFilterWhere(['in', 'driver_trips.status', $this->status])
            ->andFilterWhere(['>=', 'driver_trips.createdAt', $this->dateCreateFrom])
            ->andFilterWhere(['<=', 'driver_trips.createdAt', $this->dateCreateTo])
            ->andFilterWhere(['>=', 'driver_trips.firstDate', $this->dateFirstFrom])
            ->andFilterWhere(['<=', 'driver_trips.firstDate', $this->dateFirstTo])
            ->andFilterWhere(['>=', 'driver_trips.desiredDateFrom', $this->desiredDateBegin])
            ->andFilterWhere(['<=', 'driver_trips.desiredDateTo', $this->desiredDateEnd])
            ->andFilterWhere(['>=', 'driver_trips.dateTrip', $this->dateTripFrom])
            ->andFilterWhere(['<=', 'driver_trips.dateTrip', $this->dateTripTo]);

        $this->dateCreateFrom = (!empty($this->dateCreateFrom) && is_numeric($this->dateCreateFrom)) ?
            date('d.m.Y', $this->dateCreateFrom) : $this->dateCreateFrom;
        $this->dateCreateTo = (!empty($this->dateCreateTo) && is_numeric($this->dateCreateTo)) ?
            date('d.m.Y', $this->dateCreateTo) : $this->dateCreateTo;
        $this->dateFirstFrom = (!empty($this->dateFirstFrom) && is_numeric($this->dateFirstFrom)) ?
            date('d.m.Y', $this->dateFirstFrom) : $this->dateFirstFrom;
        $this->dateFirstTo = (!empty($this->dateFirstTo) && is_numeric($this->dateFirstTo)) ?
            date('d.m.Y', $this->dateFirstTo) : $this->dateFirstTo;
        $this->desiredDateBegin = (!empty($this->desiredDateBegin) && is_numeric($this->desiredDateBegin)) ?
            date('d.m.Y', $this->desiredDateBegin) : $this->desiredDateBegin;
        $this->desiredDateEnd = (!empty($this->desiredDateEnd) && is_numeric($this->desiredDateEnd)) ?
            date('d.m.Y', $this->desiredDateEnd) : $this->desiredDateEnd;
        $this->dateTripFrom = (!empty($this->dateTripFrom) && is_numeric($this->dateTripFrom)) ?
            date('d.m.Y', $this->dateTripFrom) : $this->dateTripFrom;
        $this->dateTripTo = (!empty($this->dateTripTo) && is_numeric($this->dateTripTo)) ?
            date('d.m.Y', $this->dateTripTo) : $this->dateTripTo;

        return $dataProvider;
    }
}