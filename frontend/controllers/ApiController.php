<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use frontend\models\GoodSearch;


class ApiController extends Controller
{
    public function actionSearchGoods() {
        $goodSearchModel = new GoodSearch();
        $query = $goodSearchModel->search(Yii::$app->request->post());
        if ($query) {
            $pages = new Pagination([
                'totalCount' => $query->count(),
            ]);
            $pages->setPage($goodSearchModel->page);
            $goods = $query->offset($pages->offset)->limit($pages->limit)->all();
        } else {
            $goods = [];
            $pages = new Pagination(['totalCount' => 0]);
        }

        foreach ($goods as $good) {
            $balance = $good->getStocksBalance();
            $good->freeAmounts = [
                'che' => $balance['stocks'][1][$good->goods_id] - $balance['reserved']['amount'][1][$good->goods_id],
                'avm' => $balance['stocks'][2][$good->goods_id] - $balance['reserved']['amount'][2][$good->goods_id],
            ];
        }

        return $this->renderPartial('/common/pjax/_good_search_form', [
            'goodSearchModel' => $goodSearchModel,
            'goods' => $goods,
            'pages' => $pages,
        ]);
    }
}