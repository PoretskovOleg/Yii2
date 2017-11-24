<?php

namespace frontend\widgets;

use frontend\widgets\assets\MultiSelectAsset;
use kartik\select2\Select2;

class MultiSelect extends Select2
{
    public function registerAssetBundle() {
        $this->theme = '';
        parent::registerAssetBundle();
        MultiSelectAsset::register($this->getView());
    }

    public function registerAssets() {
        parent::registerAssets();
        $this->getView()->registerJs("
            $('#{$this->options['id']}').select2();
        ");
    }
}