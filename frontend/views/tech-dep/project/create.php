<?php

use yii\helpers\Html;
use frontend\assets\TechDep\TechDepAsset;

TechDepAsset::register($this);

$this->title = 'Создание проекта';
$this->params['breadcrumbs'][] = ['label' => 'Реестр проектов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tech-dep-project-create">

    <?= $this->render('_form', [
        'model' => $model,
        'difficulty' => $difficulty,
        'projectFiles' => array(),
        'isStageInWork' => false,
        'stagesFiles' => array(),
        'modelComment' => array()
    ]) ?>

</div>
