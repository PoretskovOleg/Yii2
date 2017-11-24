<?php
namespace frontend\models;

use common\models\Old\Good;
use yii\base\Model;
use common\models\Old\Contractor;


class GoodSearch extends Model
{
    public $id;
    public $name;
    public $page;

    public function rules()
    {
        return [
            [['id', 'name', 'page'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'Артикул',
            'name' => 'Наименование',
        ];
    }

    public function search($params)
    {
        $query = Good::find();

        $this->load($params);
        if (!$this->validate() || (empty($this->id) && empty($this->name))) {
            return false;
        }

        if (!empty($this->name)) {
            $query->andWhere(['like', 'goods_name', $this->name]);
        }

        if (!empty($this->id)) {
            $query->andWhere(['like', 'goods_id', $this->id]);
        }

        return $query;
    }
    
    public function indentKeywords($goods) {
        foreach ($goods as $good) {
            if (!empty($this->id)) {
                $good->goods_id = preg_replace(
                    "/(" . $this->id . ")/ui",
                    '<span class="keyword">${1}</span>',
                    $good->goods_id
                );
            }

            if (!empty($this->name)) {
                $good->goods_name = preg_replace(
                    "/(" . $this->name . ")/ui",
                    '<span class="keyword">${1}</span>',
                    $good->goods_name
                );
            }
        }
    }
}
