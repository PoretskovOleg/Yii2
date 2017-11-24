<?php

namespace common\models\Driver;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class DriverTripTicketSearch extends DriverTripTicket
{
    public $statusTripTicket;
    public $driverTripTicket;
    public $carTripTicket;
    public $createdFrom;
    public $createdTo;
    public $departureFrom;
    public $departureTo;

    public function rules()
    {
        return [
            [['id', 'createdAt', 'status', 'driver', 'car', 'author', 'departureDate'], 'integer'],
            [
                [
                    'createdFrom', 'createdTo', 'departureFrom', 'departureTo',
                    'statusTripTicket', 'driverTripTicket', 'carTripTicket'
                ], 'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№ пут. листа',
            'statusTripTicket' => 'Статус',
            'driverTripTicket' => 'Водитель',
            'carTripTicket' => 'Автомобиль',
            'departureFrom' => 'Дата выезда',
            'createdFrom' => 'Дата создания',
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $is_driver)
    {
        if (empty($params) && Yii::$app->request->get('page') == null) {
            $this->statusTripTicket = [1, 2, 3];
            Yii::$app->session->set('params', null);
        } elseif (!empty($params)) {
            Yii::$app->session->set('params', $params);
        } else {
            $params = Yii::$app->session->get('params');
            if (empty($params)) $this->statusTripTicket = [1, 2, 3];
        }

        $query = DriverTripTicket::find()
            ->innerJoinWith('carTripTicket')
            ->innerJoinWith('statusTripTicket');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['driver_trip_tickets.id' => $this->id]);

        $drivers = $this->driverTripTicket;
        if ($is_driver)
            $drivers = $this->driverTripTicket ? $this->driverTripTicket : [Yii::$app->user->identity->user_id];

        $query
            ->andFilterWhere(['in', 'driver_trip_tickets.status', $this->statusTripTicket])
            ->andFilterWhere(['in', 'driver_trip_tickets.driver', $drivers])
            ->andFilterWhere(['in', 'driver_trip_tickets.car', $this->carTripTicket])
            ->andFilterWhere(['>=', 'driver_trip_tickets.createdAt', $this->unixFormatFrom($this->createdFrom)])
            ->andFilterWhere(['<=', 'driver_trip_tickets.createdAt', $this->unixFormatTo($this->createdTo)])
            ->andFilterWhere(['>=', 'driver_trip_tickets.departureDate', $this->unixFormatFrom($this->departureFrom)])
            ->andFilterWhere(['<=', 'driver_trip_tickets.departureDate', $this->unixFormatTo($this->departureTo)]);

        return $dataProvider;
    }

    public function unixFormatFrom($date)
    {
        return $date ? date_create_from_format('d.m.Y H:i:s', $date.' 00:00:01')->format('U') : $date;
    }

    public function unixFormatTo($date)
    {
        return $date ? date_create_from_format('d.m.Y H:i:s', $date.' 23:59:59')->format('U') : $date;
    }
}
