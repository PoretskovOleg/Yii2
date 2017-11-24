<?php

namespace frontend\controllers;

use Yii;
use common\models\Brand;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class BrandController extends Controller
{
    const MODULE_NAME = 'brands';

    public static function getUserRules() {
        return [
            ['id' => 1, 'name' => 'Просматривать страницу брендов'],
            ['id' => 2, 'name' => 'Создавать/редактировать бренды'],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1)) {
            return $this->render('/site/accessDenied');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Brand::find(),
            'sort' => false,
        ]);

        $this->view->params['fullWidth'] = true;

        return $this->render('index', compact('dataProvider'));
    }

    public function actionView($id)
    {
        if (!Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1)) {
            return $this->render('/site/accessDenied');
        }
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2)) {
            return $this->render('/site/accessDenied');
        }

        $model = new Brand();
        if ($this->validateAndSave($model)) {
            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2)) {
            return $this->render('/site/accessDenied');
        }

        $model = $this->findModel($id);
        if ($this->validateAndSave($model)) {
            return $this->redirect(['index']);
        }

        return $this->render('update', compact('model'));
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2)) {
            return $this->render('/site/accessDenied');
        }
        
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function validateAndSave($model)
    {
        if ($model->load(Yii::$app->request->post())) {
            $model->logo_file = UploadedFile::getInstance($model, 'logo_file');

            if ($model->logo_file) {
                $model->upload();
            }

            return (!$model->hasErrors() && $model->save());
        }

        return false;
    }
}