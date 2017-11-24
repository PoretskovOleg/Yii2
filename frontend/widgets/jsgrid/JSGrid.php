<?php

namespace frontend\widgets\jsgrid;

use yii\base\Widget;
use yii\helpers\Html;


class JSGrid extends Widget
{
    public $id;
    public $options;
    public $datasetName;

    public function init() {
        parent::init();

        if (empty($this->id)) {
            $this->id = $this->getId(true);
        }

        JSGridAsset::register($this->getView());
    }

    public function run() {
        echo Html::beginTag('div', ['id' => $this->id]);
        echo Html::endTag('div');

        $options = json_encode($this->options);

        $this->view->registerJs("

        ");

        $this->getView()->registerJs("
            $('#{$this->id}').jsGrid($options);
        ");


    }
}