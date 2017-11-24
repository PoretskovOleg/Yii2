<?php

namespace common\models\Production;

use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use common\models\Production\ProductionOrder;

class ProductionOrderSearch extends ProductionOrder
{
    public $orderProductNumber;
    public $orderNumber;
    public $nameGood;
    public $priorities;
    public $targets;
    public $themes;
    public $typesGood;
    public $typesOrder;
    public $responsibles;
    public $otks;
    public $statuses;
    public $stages;
    public $dateCreateFrom;
    public $dateCreateTo;
    public $dateDedlineFrom;
    public $dateDedlineTo;
    public $typeSort;

    public function rules()
    {
        return [
            [
                [
                    'orderProductNumber', 'orderNumber', 'nameGood'
                ], 'trim'
            ],
            [
                [
                    'priorities', 'targets', 'themes', 'typesGood', 'typesOrder', 'responsibles', 'otks', 'typeSort',
                    'statuses', 'stages', 'dateCreateFrom', 'dateCreateTo', 'dateDedlineFrom', 'dateDedlineTo'
                ], 'safe'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'orderProductNumber' => '№ З/Н',
            'orderNumber' => '№ счета',
            'nameGood' => 'Наименование изделия',
            'priorities' => 'Приоритет',
            'targets' => 'Назначение',
            'themes' => 'Тема',
            'typesGood' => 'Изделие',
            'typesOrder' => 'Тип',
            'responsibles' => 'Ответственный',
            'otks' => 'ОТК',
            'statuses' => 'Статус',
            'stages' => 'Этап',
            'dateCreateFrom' => 'Дата З/Н',
            'dateDedlineFrom' => 'Дедлайн',
            'typeSort' => 'Сортировка'
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        if (empty($params) && Yii::$app->request->get('page') == null) {
            $this->statuses = [1, 2, 3, 4, 5];
            Yii::$app->session->set('params', null);
        } elseif (!empty($params)) {
            Yii::$app->session->set('params', $params);
        } else {
            $params = Yii::$app->session->get('params');
            if (empty($params)) $this->statuses = [1, 2, 3, 4, 5];
        }

        $oldDbName = explode('=', Yii::$app->old_db->dsn)[2];
        $query = ProductionOrder::find()
            ->leftJoin($oldDbName.'.goods', 'goods_id = good')
            ->leftJoin($oldDbName.'.order_lab', 'order_lab_id = good');
 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'sequence' => [
                        'asc' => empty($params['ProductionOrderSearch']['typeSort']) ? 
                            [new Expression('sequence IS NULL ASC, sequence ASC')] :
                            [new Expression('posSection IS NULL ASC, posSection ASC, section IS NULL ASC, section ASC, sequence IS NULL ASC, sequence ASC')]
                    ],
                    'dedline',
                    'priority'
                ],
                'defaultOrder' => ['sequence' => SORT_ASC, 'dedline' => SORT_ASC, 'priority' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!is_numeric($this->orderProductNumber)) {
            $this->orderProductNumber = strtr($this->orderProductNumber, ['С' => 'C', 'Т' => 'T']);
        }
        if (!is_numeric($this->orderNumber)) {
            $this->orderNumber = strtr($this->orderNumber, ['С' => 'C', 'Т' => 'T']);
        }

        $query
            ->andFilterWhere(['or', ['id' => $this->orderProductNumber], ['like', 'number', $this->orderProductNumber]])
            ->andFilterWhere(['or', ['like', 'nameGood', $this->nameGood], ['good' => $this->nameGood],
                ['like', 'goods.goods_name', $this->nameGood], ['like', 'order_lab.name', $this->nameGood]])
            ->andFilterWhere(['like', 'order', $this->orderNumber])
            ->andFilterWhere(['in', 'priority', $this->priorities])
            ->andFilterWhere(['in', 'target', $this->targets])
            ->andFilterWhere(['in', 'theme', $this->themes])
            ->andFilterWhere(['in', 'typeGood', $this->typesGood])
            ->andFilterWhere(['in', 'responsible', $this->responsibles])
            ->andFilterWhere(['in', 'otk', $this->otks])
            ->andFilterWhere(['in', 'status', $this->statuses])
            ->andFilterWhere(['in', 'stage', $this->stages])
            ->andFilterWhere(['>=', 'createdAt', $this->unixFormatFrom($this->dateCreateFrom)])
            ->andFilterWhere(['<=', 'createdAt', $this->unixFormatTo($this->dateCreateTo)])
            ->andFilterWhere(['>=', 'dedline', $this->unixFormatFrom($this->dateDedlineFrom)])
            ->andFilterWhere(['<=', 'dedline', $this->unixFormatTo($this->dateDedlineTo)]);

        if (count($this->typesOrder) == 1) {
            if (in_array(1, $this->typesOrder))
                $query->andWhere(['goods.is_service' => 0]);
            elseif(in_array(2, $this->typesOrder))
                $query->andWhere(['or', ['target' => 3], ['goods.is_service' => null], ['goods.is_service' => 1]]);
        }

        return $dataProvider;
    }

    private function unixFormatFrom($date)
    {
        return !empty($date) ? date_create_from_format('d.m.Y H:i:s', $date.' 00:00:01')->format('U') : $date;
    }

    private function unixFormatTo($date)
    {
        return !empty($date) ? date_create_from_format('d.m.Y H:i:s', $date.' 23:59:59')->format('U') : $date;
    }
}
