<?php

namespace frontend\models;

use yii\base\Model;
use common\models\Old\Order;
use common\models\Old\OrderSiz;
use common\models\Old\OrderLab;
use common\models\Old\OrderGood;
use common\models\Old\StockMovingGood;

class Driver extends Model
{
    public function getGoods($params)
    {
        $goods = array();
        if ($params['type'] == 1) {
            if (!is_numeric($params['id'])) {
                $params['id'] = strtr($params['id'], ['С' => 'C', 'Т' => 'T']);
            }
            
            $order = Order::find()->where(['contract_number' => $params['id']])->limit(1)->one();
            if (empty($order) && is_numeric($params['id'])) 
                $order = Order::find()->where(['and', ['order_number' => $params['id']], ['contract_number' => '']])->limit(1)->one();
            
            if (!empty($order)) {
                switch ($order->order_type) {
                    case '0':
                        $result = OrderSiz::find()
                        ->innerJoinWith('unit')
                        ->where(['order_id' => $order->order_id])
                        ->all();
                        $nameId = 'siz_id';
                        break;
                    case '1':
                        $result = OrderLab::find()
                        ->innerJoinWith('unit')
                        ->where(['order_id' => $order->order_id])
                        ->all();
                        $nameId = 'order_lab_id';
                        break;
                    case '2':
                        $result = OrderGood::find()
                        ->innerJoinWith('unit')
                        ->innerJoinWith('weight')
                        ->where(['order_id' => $order->order_id])
                        ->all();
                        $nameId = 'goods_id';
                        break;
                }
            }
            if ( !empty($result) ) {
                foreach ($result as $item) {
                    $goods[] = [
                        'id' => $item->$nameId,
                        'name' => $item->name,
                        'amount' => $item->amount,
                        'unit' => $item->unit->list_unit_name,
                        'weight' => ($nameId == 'goods_id') ? $item->weight->weight : 0
                    ];
                }
            }
        } elseif ($params['type'] == 3) {
            $order = StockMovingGood::find()
                ->innerJoinWith('goods')
                ->where(['stock_moving_id' => $params['id']])
                ->all();

            if (!empty($order))
                foreach ($order as $item) {
                    $goods[] = [
                        'id' => $item->goods_id,
                        'amount' => $item->amount,
                        'name' => $item->goods->goods_name,
                        'unit' => $item->goods->unit->list_unit_name,
                        'weight' => $item->goods->weight
                    ];
                }
        }

        return $goods;
    }

    public function getGoodsTrips($trips)
    {
        $goods = array();
        foreach ($trips as $trip) {
            $goods[$trip->id] = $this->getGoods(['id' => $trip->orderNumber, 'type' => $trip->typeOfTrip]);
        }

        return $goods;
    }

    public function getOrderInfo($params)
    {
        if (!is_numeric($params['id'])) {
            $params['id'] = strtr($params['id'], ['С' => 'C', 'Т' => 'T']);
        }
        $info = array(
            'consigneeName' => '',
            'consigneeInn' => '',
            'consigneePhone' => '',
            'consignerUser' => '',
            'consignerUserPhone' => '',
            'consignerName' => '',
            'consignerInn' => '',
            'consignerPhone' => '',
            'consigneeUser' =>'',
            'consigneeUserPhone' => ''
        );
        if ($params['type'] == 1) {
            $order = Order::find()->where(['contract_number' => $params['id']])->limit(1)->one();
            if (empty($order) && is_numeric($params['id'])) 
                $order = Order::find()->where(['and', ['order_number' => $params['id']], ['contract_number' => '']])->limit(1)->one();

            if (!empty($order)) {
                $info = [
                    'consigneeUser' => !empty($order->contactPerson->contact_person_name) ? $order->contactPerson->contact_person_name : '',
                    'consigneeUserPhone' => !empty($order->contactPerson->mobile_phone_number) ? $order->contactPerson->mobile_phone_number : '',
                    'consigneeName' => !empty($order->organization->organization_name) ? $order->organization->organization_name : '',
                    'consigneeInn' => !empty($order->organization->inn) ? $order->organization->inn : '',
                    'consigneePhone' => !empty($order->organization->phone_number) ? $order->organization->phone_number : '',
                    'consignerUser' => !empty($order->createUser->last_name) ?
                        $order->createUser->last_name . ' ' . $order->createUser->first_name . ' ' . $order->createUser->patronymic : '',
                    'consignerUserPhone' => !empty($order->createUser->phone_number) ? $order->createUser->phone_number : '',
                    'consignerName' => !empty($order->performer->organization_name) ? $order->performer->organization_name : '',
                    'consignerInn' => !empty($order->performer->inn) ? $order->performer->inn : '',
                    'consignerPhone' => !empty($order->performer->phone_number) ? $order->performer->phone_number : ''
                ];
            }
        }

        return $info;
    }

    public function getGoodsTechDep($params)
    {
        $goods = array();
        if (!is_numeric($params['id'])) {
            $params['id'] = strtr($params['id'], ['С' => 'C', 'Т' => 'T']);
        }

        $order = Order::find()->where(['contract_number' => $params['id']])->limit(1)->one();
        if (empty($order) && is_numeric($params['id'])) 
            $order = Order::find()->where(['and', ['order_number' => $params['id']], ['contract_number' => '']])->limit(1)->one();

        if (!empty($order)) {
            switch ($order->order_type) {
                case '0':
                    $result = OrderSiz::find()
                    ->where(['order_id' => $order->order_id])
                    ->all();
                    $nameId = 'order_siz_id';
                    break;
                case '1':
                    $result = OrderLab::find()
                    ->where(['order_id' => $order->order_id])
                    ->all();
                    $nameId = 'order_lab_id';
                    break;
                case '2':
                    $result = OrderGood::find()
                    ->where(['order_id' => $order->order_id])
                    ->all();
                    $nameId = 'goods_id';
                    break;
            }
        }

        if ( !empty($result) ) {
            foreach ($result as $item) {
                $goods[] = [
                    'id' => $item->$nameId,
                    'name' => $item->name
                ];
            }
        }
        return $goods;
    }

    public function getGoodsProduction($orderNumber)
    {
        $goods = array();

        $order = Order::find()->where(['contract_number' => $orderNumber])->limit(1)->one();
        if (empty($order) && is_numeric($orderNumber))
            $order = Order::find()->where(['and', ['order_number' => $orderNumber], ['contract_number' => '']])->limit(1)->one();

        if (!empty($order)) {
            switch ($order->order_type) {
                case '0':
                    $result = OrderSiz::find()
                    ->innerJoinWith('unit')
                    ->where(['order_id' => $order->order_id])
                    ->all();
                    $nameId = 'order_siz_id';
                    break;
                case '1':
                    $result = OrderLab::find()
                    ->innerJoinWith('unit')
                    ->where(['order_id' => $order->order_id])
                    ->all();
                    $nameId = 'order_lab_id';
                    break;
                case '2':
                    $result = OrderGood::find()
                    ->innerJoinWith('unit')
                    ->where(['order_id' => $order->order_id])
                    ->all();
                    $nameId = 'goods_id';
                    break;
            }
        }

        if ( !empty($result) ) {
            foreach ($result as $key => $item) {
                $goods[] = [
                    'id' => $item->$nameId,
                    'position' => $key + 1,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'unit' => !empty($item->unit) ? $item->unit->list_unit_name : '',
                ];
            }
        }
        $dedline = (!empty($order->deadline) && !empty(strtotime($order->deadline))) ? strtotime($order->deadline) - 2*24*60*60 : 0;
        return array('goods' => $goods, 'dedline' => !empty($dedline) ? date('d.m.Y', $dedline) : '');
    }
}
?>