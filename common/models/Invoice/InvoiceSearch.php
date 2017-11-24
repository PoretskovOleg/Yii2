<?php
namespace common\models\Invoice;

use yii\base\Model;
use yii\data\ActiveDataProvider;


class InvoiceSearch extends Model
{
    public $id;
    public $order_id;
    public $contractor_name;
    public $total;
    public $organization_id;
    public $subject_id;
    public $status_id;
    public $primary;
    public $manager_id;
    public $created_from;
    public $created_to;

    public function rules()
    {
        return [
            [['id', 'order_id'], 'integer'],
            [['contractor_name'], 'string', 'max' => 255],
            [['total'], 'number'],
            [['created_from', 'created_to', 'organization_id', 'subject_id', 'status_id', 'manager_id', 'primary'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '№',
            'order_id' => '№ заказа',
            'contractor_name' => 'Контрагент',
            'total' => 'Сумма',
            'organization_id' => 'От кого',
            'subject_id' => 'Тема',
        ];
    }

    public function search($params)
    {

        preg_match('/dbname=([^;]*)/', \Yii::$app->old_db->dsn, $match);
        $old_db_name =  $match[1];

        $query = Invoice::find()
            ->from('invoices i')
            ->join('LEFT JOIN', $old_db_name . '.contractor contractor', 'contractor.contractor_id = i.contractor_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 30,
            ],
            'sort' => false,
        ]);

        if (!($this->load(\Yii::$app->request->post()) && $this->validate())) {
            return $dataProvider;
        }

        if (!empty($this->order_id)) {
            $query->andWhere(['order_id' => $this->order_id]);
        }

        if (!empty($this->contractor_name)) {
            $query->andFilterWhere(['like', 'contractor.contractor_name', $this->contractor_name]);
        }

        if (!empty($this->total)) {
            $query->andWhere(['i.total' => $this->total]);
        }

        if (!empty($this->organization_id)) {
            $query->andWhere(['i.organization_id' => $this->organization_id]);
        }

        if (!empty($this->subject_id)) {
            $query->andWhere(['i.subject_id' => $this->subject_id]);
        }

        if (!empty($this->status_id)) {
            $query->andWhere(['i.status_id' => $this->status_id]);
        }

        if (!empty($this->primary)) {
            $query->andWhere(['i.primary' => $this->primary]);
        }

        if (!empty($this->manager_id)) {
            $query->andWhere(['i.manager_id' => $this->manager_id]);
        }

        if (!empty($this->created_from)) {
            $query->andWhere([
                '>=',
                'created',
                (new \DateTime($this->created_from))->setTime(0,0,0)->format('Y-m-d H:i:s'),
            ]);
        }

        if (!empty($this->created_to)) {
            $query->andWhere([
                '<=',
                'created',
                (new \DateTime($this->created_to))->setTime(23,59,59)->format('Y-m-d H:i:s'),
            ]);
        }

        if (!empty($this->id)) {
            $query->andWhere(['id' => $this->id]);
        }

        return $dataProvider;
    }
}
