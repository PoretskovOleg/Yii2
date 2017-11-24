<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use dmstr\web\AdminLteAsset;
use common\widgets\Alert;

AdminLteAsset::register($this);
AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="skin-blue layout-top-nav">
<?php $this->beginBody() ?>

<div class="wrap">
    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand"><b>А</b>Завод</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <?php
                        $menuItems = [
                            'sales' => [
                                'label' => 'Продажи',
                                'items' => [
                                    ['label' => 'Прикрепляемые файлы', 'url' => Url::toRoute('attachment/index')],
                                ],
                            ],
                        ];
                        if (!Yii::$app->user->isGuest) {
                            if (Yii::$app->user->identity->checkRule('brands', 1)) {
                                $menuItems['sales']['items'][] = ['label' => 'Бренды', 'url' => Url::toRoute('brand/index')];
                            }

                            if (Yii::$app->user->identity->checkRule('invoices', 1)) {
                                $menuItems['sales']['items'][] = [
                                    'label' => 'Счета',
                                    'url' => Url::toRoute('invoice/index')
                                ];
                            }

                            if (Yii::$app->user->identity->checkRule('invoice-templates', 1)) {
                                $menuItems['sales']['items'][] = [
                                    'label' => 'Шаблоны счетов',
                                    'url' => Url::toRoute('invoice-template/index')
                                ];
                            }

                            if (Yii::$app->user->identity->checkRule('commercial-proposals', 1)) {
                                $menuItems['sales']['items'][] = [
                                    'label' => 'Коммерческие предложения',
                                    'url' => Url::toRoute('commercial-proposal/index')
                                ];
                            }

                            if (Yii::$app->user->identity->checkRule('commercial-proposal-templates', 1)) {
                                $menuItems['sales']['items'][] = [
                                    'label' => 'Шаблоны коммерческих предложений',
                                    'url' => Url::toRoute('commercial-proposal-template/index')
                                ];
                            }

                            if (Yii::$app->user->identity->checkRule('driver', 1)) {
                                $menuItems[] = [
                                    'label' => 'Водитель',
                                    'items' => [
                                        ['label' => 'Реестр поездок', 'url' => Url::toRoute('driver-trips/index')],
                                        ['label' => 'Реестр путевых листов', 'url' => Url::toRoute('driver-trip-tickets/index')],
                                        ['label' => 'Реестр адресов', 'url' => Url::toRoute('driver-address/index')],
                                        ['label' => 'Реестр автомобилей', 'url' => Url::toRoute('driver-car/index')]
                                    ]
                                ];
                            }
                            if (Yii::$app->user->identity->checkRule('tech-dep', 1)) {
                                $menuItems['techDep'] = [
                                        'label' => 'Тех. отдел',
                                        'items' => [
                                            ['label' => 'Реестр техотдела', 'url' => Url::toRoute('tech-dep/index')],
                                        ]
                                    ];
                            }

                            if (Yii::$app->user->identity->checkRule('tech-dep', 7)) {
                                if (Yii::$app->user->identity->checkRule('tech-dep', 1))
                                    $menuItems['techDep']['items'][] = ['label' => 'Настройка сложности', 'url' => Url::toRoute('tech-dep/difficulty')];
                                else $menuItems['techDep'] = [
                                        'label' => 'Тех. отдел',
                                        'items' => [
                                            ['label' => 'Настройка сложности', 'url' => Url::toRoute('tech-dep/difficulty')],
                                        ]
                                    ];
                            }

                            if (Yii::$app->user->identity->checkRule('production-order', 1)) {
                                $menuItems[] = [
                                    'label' => 'Производство',
                                    'items' => [
                                        ['label' => 'Реестр заказ-нарядов', 'url' => Url::toRoute('production-order/index')],
                                    ]
                                ];
                            }

                            if (Yii::$app->user->identity->checkRule('purchase-good-subjects', 1)) {
                                $menuItems[] = [
                                    'label' => 'Закупки',
                                    'items' => [
                                        [
                                            'label' => 'Темы закупок',
                                            'url' => Url::toRoute('purchase-good-subject/index')
                                        ],
                                    ]
                                ];
                            }

                            echo Nav::widget([
                                'options' => ['class' => 'navbar-nav navbar-left'],
                                'items' => $menuItems,
                            ]);
                        }

                        $loginMenuItems = [];
                        if (Yii::$app->user->isGuest) {
                            $loginMenuItems[] = ['label' => 'Вход', 'url' => ['/site/login']];
                        } else {
                            $loginMenuItems = [
                                'user' => [
                                    'label' => Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name,
                                    'items' => [
                                        [
                                            'label' => 'Выход',
                                            'url' => Url::toRoute('site/logout'),
                                            'linkOptions' => ['data-method' => 'post'],
                                        ],
                                    ]
                                ]
                            ];
                        }
                        echo Nav::widget([
                            'options' => ['class' => 'navbar-nav navbar-right'],
                            'items' => $loginMenuItems,
                        ]);
                    ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="content-wrapper">
        <div class="<?= isset($this->params['fullWidth']) && $this->params['fullWidth'] ? 'container-fluid' : 'container' ?>">
            <section class="content-header">
                <h1><?= Html::encode($this->title) ?></h1>
                <?= Breadcrumbs::widget([
                    'homeLink' => ['label' => 'Главная', 'url' => '/'],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>
            </section>

            <section class="content">
                <?= $content ?>
            </section>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
