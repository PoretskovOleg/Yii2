<?php

namespace common\models\Old;

use Yii;

class Good extends \yii\db\ActiveRecord
{
    public $freeAmounts = [
        'che' => 0,
        'avm' => 0,
    ];

    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'goods';
    }

    public function getUnit() {
        return $this->hasOne(Unit::classname(), ['list_unit_id' => 'unit_id']);
    }

    public static function getConnectedGoods($id) {
        $row = self::getDb()->createCommand("
          SELECT t1.goods_id1, t1.goods_id2, IFNULL(t2.goods_name, t3.goods_name) as goods_name
            FROM connection_goods t1
            LEFT JOIN goods t2 on t2.goods_id=t1.goods_id1
            LEFT JOIN goods t3 on t3.goods_id=t1.goods_id2
            WHERE t1.goods_id2=" . $id . " or t1.goods_id1=" . $id
        )->queryAll();

        if ($key = array_search($id, $row)) $row[$key] = '';

        return $row;
    }

    public function getStocksBalance() {
        $goods = ['goods_id' => $this->goods_id];
        if (!defined('ORDER_STATUS_SHIPMENT')) define("ORDER_STATUS_SHIPMENT", 110);
        if (!defined('ORDER_STATUS_FINISHED')) define("ORDER_STATUS_FINISHED", 113);
        if (!defined('ORDER_STATUS_CANCELED')) define("ORDER_STATUS_CANCELED", 111);
        if (!defined('ORDER_STATUS_NO_PAYED')) define("ORDER_STATUS_NO_PAYED", 148);

        $array = array();
        $goods['goods_id1'] = '';
        $goods['goods_id2'] = '';
        $array[$goods['goods_id']] = 0;
        // узнаем есть ли основной товар у указанного

        // возвращать все о товаре надо помимо цены, названия и категории с типом
        $goods1 = self::getConnectedGoods($goods['goods_id']);
        if (!empty($goods1)) {
            $id = isset($goods1['goods_id1']) ? $goods1['goods_id1'] : isset($goods1['goods_id2']) ? $goods1['goods_id2'] : false;
            if ($id) {
                $array[$id] = 0;
                $goods['goods_id1'] = $goods1['goods_id1'];
                $goods['goods_id2'] = $goods1['goods_id2'];
                // если товар второстепенный то обновлять по нему всю инфу
            }
        }

        $goods_stock[1] = $array;
        $goods_stock[2] = $array;

        $rows = self::getDb()->createCommand("
			SELECT 
				goods.goods_id,
				stock_section.stock_id,
				IFNULL(SUM(stock_products.amount),0) as amount
			FROM goods 
			left join stock_products on stock_products.goods_id=goods.goods_id 
			LEFT JOIN stock_section ON stock_section.stock_section_id=stock_products.stock_section_id
			WHERE goods.goods_id IN(" . implode(',', array_keys($array)) . ") and stock_section.stock_id in(1,2) GROUP BY stock_section.stock_id	
    	")->queryAll();
        $goods['free_amount'][$goods['goods_id']] = 0;

        foreach ($rows as $row) {
            $goods_stock[$row['stock_id']][$row['goods_id']] = $row['amount'];
            $goods['free_amount'][$row['goods_id']] += $row['amount'];
        }
        $goods['stocks'] = $goods_stock;

        $reserved['amount'][1] = $array;
        $reserved['amount'][2] = $array;
        $reserved['orders'][1] = array();
        $reserved['orders'][2] = array();

        $rows = self::getDb()->createCommand("
			SELECT  orders.order_id,
			      orders.order_status, 
			      order_goods.amount, 
			      orders.stock_id,
			      order_goods.goods_id, 
			      (SELECT order_pay_id FROM order_pay where orders.order_id=order_pay.order_id limit 1) as order_pay_id
			FROM order_goods 
			LEFT JOIN orders ON orders.order_id = order_goods.order_id 
			WHERE order_goods.goods_id IN (" . implode(',', array_keys($array)) . ")  
			AND orders.order_status != " . ORDER_STATUS_CANCELED . " 
			AND orders.order_status != " . ORDER_STATUS_FINISHED . " 
			AND orders.order_status != " . ORDER_STATUS_SHIPMENT . " 
			AND stock_id in(1,2) 
			HAVING (
				(
				order_pay_id IS NOT NULL
				AND order_pay_id > 0
				)
				OR orders.order_status =" . ORDER_STATUS_NO_PAYED . "
			)
		")->queryAll();

        foreach ($rows as $row) {
            $reserved['amount'][$row['stock_id']][$row['goods_id']] += $row['amount'];
            $reserved['orders'][$row['stock_id']][$row['goods_id']][$row['order_id']] = array('order_id' => $row['order_id']);
            $goods['free_amount'][$row['goods_id']] -= $row['amount'];
        }

        $rows = self::getDb()->createCommand("
          SELECT 
            T1.amount,
            T1.base_reserved_id,
            T2.status,
            T1.goods_id,
            T2.job_ticket_id
            FROM stock_reserved T1
             LEFT JOIN register_job_ticket T2 ON T1.base_reserved_id = T2.job_ticket_id
            WHERE T1.goods_id IN (" . implode(',', array_keys($array)) . ")
        ")->queryAll();

        if (!empty($rows)) {
            $row_base = array_shift($rows);
            $reserved['amount'][1][$row_base['goods_id']] += $row_base['amount'];
            $reserved['job_ticket'][1][$row_base['goods_id']][$row_base['job_ticket_id']] = array('job_ticket_id' => $row_base['job_ticket_id']);
            $goods['free_amount'][$row_base['goods_id']] -= $row_base['amount'];
        }

        $goods['reserved'] = $reserved;

        return $goods;
    }
}