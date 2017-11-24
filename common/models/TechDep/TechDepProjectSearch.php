<?php

namespace common\models\TechDep;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TechDep\TechDepProject;

class TechDepProjectSearch extends TechDepProject
{
    public $nameProduct;
    public $dateCreateFrom;
    public $dateCreateTo;
    public $dateDedlineFrom;
    public $dateDedlineTo;
    public $stages;
    public $types;
    public $statuses;
    public $priorities;
    public $authors;
    public $difficulties;
    public $responsibles;
    public $contractors;
    public $approveds;
    public $isArchive;

    public function rules()
    {
        return [
            ['id', 'integer'],
            [
                ['orderNumber', 'nameProduct', 'notice'], 'trim'],
            [
                [
                    'types', 'statuses', 'priorities', 'authors',
                    'difficulties', 'responsibles', 'contractors',
                    'approveds', 'dateCreateFrom', 'dateCreateTo',
                    'dateDedlineFrom', 'dateDedlineTo', 'stages', 'isArchive'
                ], 'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№ проекта',
            'authors' => 'Автор',
            'orderNumber' => '№ заказа',
            'types' => 'Тип проекта',
            'statuses' => 'Статус проекта',
            'stages' => 'В работе',
            'priorities' => 'Приоритет',
            'difficulties' => 'Сложность',
            'responsibles' => 'Ответственный',
            'contractors' => 'Исполнитель',
            'approveds' => 'Утвердил',
            'nameProduct' => 'Наименование изделия',
            'dateCreateFrom' => 'Дата создания',
            'dateDedlineFrom' => 'Дата дедлайна',
            'isArchive' => 'Архив'
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }
    
    public function search($params)
    {
        if (empty($params) && Yii::$app->request->get('page') == null) {
            $this->statuses = [1, 2, 3, 4, 5, 6, 7, 8];
            Yii::$app->session->set('params', null);
        } elseif (!empty($params)) {
            Yii::$app->session->set('params', $params);
        } else {
            $params = Yii::$app->session->get('params');
            if (empty($params)) $this->statuses = [1, 2, 3, 4, 5, 6, 7, 8];
        }

        $oldDbName = explode('=', Yii::$app->old_db->dsn)[2];
        $query = TechDepProject::find()
            ->leftJoin($oldDbName.'.goods', 'goods_id = goodId');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
            ],
            'sort' => ['defaultOrder' => ['dedline' => SORT_ASC, 'priority' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'orderNumber' => $this->orderNumber
        ]);

        if (!empty($this->stages)) {
            $idProjects = TechDepPlanning::find()
                ->select('project')
                ->distinct()
                ->where(['and', ['in', 'stage', $this->stages], ['status' => 2]])
                ->column();
            if (empty($idProjects)) $idProjects = array(0);
        } else $idProjects = null;

        if (Yii::$app->request->post('need_approved') == 'need_approved') {
            $onApproved = TechDepPlanning::find()
                ->select('project')
                ->distinct()
                ->where(['status' => 3])
                ->column();
            if (empty($onApproved)) $onApproved = array(0);
        } else $onApproved = null;

        if (!empty($this->contractors)) {
            $contractor = TechDepPlanning::find()
                ->select('project')
                ->distinct()
                ->where(['in', 'contractor', $this->contractors])
                ->column();
            if (empty($contractor)) $contractor = array(0);
        } else $contractor = null;

        $archive = $this->isArchive;
        if (!empty($this->isArchive) && in_array(2, $this->isArchive) && !in_array(0, $this->isArchive)) {
            $archive[] = 0;
        }

        $query
            ->andFilterWhere(['or', ['like', 'goods.goods_name', $this->nameProduct], ['like', 'goodName', $this->nameProduct], ['goodId' => $this->nameProduct]])
            ->andFilterWhere(['in', 'type', $this->types])
            ->andFilterWhere(['or', ['in', 'status', $this->statuses], ['in', 'id', $onApproved]])
            ->andFilterWhere(['in', 'priority', $this->priorities])
            ->andFilterWhere(['in', 'authorId', $this->authors])
            ->andFilterWhere(['in', 'difficulty', $this->difficulties])
            ->andFilterWhere(['in', 'responsible', $this->responsibles])
            ->andFilterWhere(['in', 'id', $contractor])
            ->andFilterWhere(['in', 'id', $idProjects])
            ->andFilterWhere(['in', 'approved', $this->approveds])
            ->andFilterWhere(['>=', 'createdAt', $this->unixFormatFrom($this->dateCreateFrom)])
            ->andFilterWhere(['<=', 'createdAt', $this->unixFormatTo($this->dateCreateTo)])
            ->andFilterWhere(['>=', 'dedline', $this->unixFormatFrom($this->dateDedlineFrom)])
            ->andFilterWhere(['<=', 'dedline', $this->unixFormatTo($this->dateDedlineTo)])
            ->andFilterWhere(['in', 'archive', $archive]);

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
