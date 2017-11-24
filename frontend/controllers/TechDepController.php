<?php

namespace frontend\controllers;

use Yii;
use common\models\TechDep\TechDepDifficulty;
use common\models\TechDep\TechDepProject;
use common\models\TechDep\TechDepProjectSearch;
use common\models\TechDep\TechDepTypeProject;
use common\models\TechDep\TechDepStatusProject;
use common\models\TechDep\TechDepStagesProject;
use common\models\TechDep\TechDepPriorityProject;
use common\models\TechDep\TechDepFile;
use common\models\TechDep\TechDepStageFile;
use common\models\TechDep\TechDepPlanning;
use common\models\TechDep\TechDepComment;
use common\models\TechDep\TechDepHistoryStage;
use common\models\TechDep\TechDepHistoryProject;
use common\models\TechDep\TechDepTypeFileStage;
use common\models\TechDep\TechDepMaterialsProject;
use common\models\Old\Good;
use frontend\models\Driver;
use frontend\models\GoodSearch;
use common\models\User;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class TechDepController extends Controller
{
    const MODULE_NAME = 'tech-dep';

    public static function getUserRules() {
        return [
            ['id' => 1, 'name' => 'Может просматривать реестр техотдела'],
            ['id' => 2, 'name' => 'Может делать планирование'],
            ['id' => 3, 'name' => 'Может редактировать проект'],
            ['id' => 4, 'name' => 'Может менять ответственного'],
            ['id' => 5, 'name' => 'Может утверждать тех документацию'],
            ['id' => 6, 'name' => 'Может удалять загруженные файлы'],
            ['id' => 7, 'name' => 'Может редактировать таблицу настроек'],
            ['id' => 8, 'name' => 'Может удалять проект'],
            ['id' => 9, 'name' => 'Может создавать проект']
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
                'denyCallback' => function() {
                    echo $this->render('/site/accessDenied');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'stage', 'reference'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 1));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 9));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['planning'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 2));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['difficulty'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 7));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest && Yii::$app->user->identity->checkRule(self::MODULE_NAME, 8));
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            $modelComments = TechDepComment::find()->where(['project' => Yii::$app->request->post('id')])->orderBy('createdAt DESC')->all();
            $comments = array();
            foreach ($modelComments as $comment) {
                $comments[] = [
                    'author' => $comment->authorComment->shortName,
                    'date' => date('d.m.Y H:i', $comment->createdAt),
                    'comment' => $comment->comment
                ];
            }
            return json_encode($comments);
        }

        if (Yii::$app->request->post('reset') == 'reset') {
            return $this->redirect(['index']);
        }
        $post = array();
        if (Yii::$app->request->post('i_contractor') == 'i_contractor') {
            $post = Yii::$app->request->post();
            $post['TechDepProjectSearch']['contractors'] = array(Yii::$app->user->identity->user_id);
        }
        if (Yii::$app->request->post('i_responsible') == 'i_responsible') {
            $post = Yii::$app->request->post();
            $post['TechDepProjectSearch']['responsibles'] = array(Yii::$app->user->identity->user_id);
        }
        if (Yii::$app->request->post('need_approved') == 'need_approved') {
            $post = Yii::$app->request->post();
            $post['TechDepProjectSearch']['statuses'] = array(8);
        }


        $this->view->params['fullWidth'] = true;
        $searchModel = new TechDepProjectSearch();
        $dataProvider = $searchModel->search(empty($post) ? Yii::$app->request->post() : $post);

        $type = TechDepTypeProject::find()->select(['name','id'])->indexBy('id')->column();
        $type[0] = 'Все';
        ksort($type);
        $searchModel->types = $searchModel->types ? $searchModel->types : array_keys($type);

        $isArchive = [
            '0' => 'Все',
            '1' => 'В архиве',
            '2' => 'Действующие'
        ];
        $searchModel->isArchive = $searchModel->isArchive ? $searchModel->isArchive : array_keys($isArchive);

        $status = TechDepStatusProject::find()->select(['name','id'])->indexBy('id')->column();
        $status[0] = 'Все';
        ksort($status);
        $searchModel->statuses = $searchModel->statuses ? $searchModel->statuses : array_keys($status);

        $stages = TechDepStagesProject::find()->select(['name','id'])->indexBy('id')->column();
        $stages[0] = 'Все';
        ksort($stages);

        $priority = TechDepPriorityProject::find()->select(['name','id'])->indexBy('id')->column();
        $priority[0] = 'Все';
        ksort($priority);
        $searchModel->priorities = $searchModel->priorities ? $searchModel->priorities : array_keys($priority);

        $difficulty = [
            '0' => 'Все',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => 'Индив.'
        ];
        $searchModel->difficulties = $searchModel->difficulties ? $searchModel->difficulties : array_keys($difficulty);

        $author = $this->getUsers('author');
        $searchModel->authors = $searchModel->authors ? $searchModel->authors : array_keys($author);

        $responsible = $this->getUsers('responsible');
        $approved = $this->getUsers('approved');

        $contractor = array();
        $modelContractor = TechDepPlanning::find()->select('contractor')->distinct()->all();
        foreach ($modelContractor as $user) {
            if (!empty($user->contractor))
                $contractor[$user->contractor] = $user->contractorStage->shortName;
        }
        $contractor['0'] = 'Все';
        ksort($contractor);

        return $this->render('project/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type,
            'status' => $status,
            'stages' => $stages,
            'priority' => $priority,
            'difficulty' => $difficulty,
            'author' => $author,
            'responsible' => $responsible,
            'contractor' => $contractor,
            'approved' => $approved,
            'isArchive' => $isArchive
        ]);
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->post('id') != null) {
            $model = new Driver();
            $goods = $model->getGoodsTechDep(['id' => Yii::$app->request->post('id')]);
            if (Yii::$app->request->post('type') == 1)
                foreach ($goods as $key => $good) {
                    if ( TechDepProject::find()->where(['goodId' => $good['id'], 'archive' => 0])->exists() && 
                        !in_array(Yii::$app->request->post('project'), TechDepProject::find()->select('id')->where(['goodId' => $good['id'], 'archive' => 0])->column())) {
                        unset($goods[$key]);
                    }
                }
            elseif (Yii::$app->request->post('type') == 2 || Yii::$app->request->post('type') == 3)
                foreach ($goods as $key => $good) {
                    if ( TechDepProject::find()->where(['goodId' => $good['id'], 'archive' => 0, 'orderNumber' => Yii::$app->request->post('id')])->exists() &&
                        !in_array(Yii::$app->request->post('project'),
                            TechDepProject::find()->select('id')->where(['goodId' => $good['id'], 'archive' => 0, 'orderNumber' => Yii::$app->request->post('id')])->column())) {
                        unset($goods[$key]);
                    }
                }
            $goods = array_values($goods);
            return json_encode($goods);
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->post('difficulty') != null) {
            $difficulty = Yii::$app->request->post('difficulty');
            $dedlineTime = $this->getDifficulty($difficulty, 'дедлайн');
            $pureTime = $this->getDifficulty($difficulty, 'чистое');
            if (Yii::$app->request->post('type') == 4) 
                $dedline = (int)TechDepDifficulty::find()->where(['and', ['difficulty' => $difficulty], ['stageName' => 'дедлайн']])->one()->calc1 + 1;
            else
                $dedline = TechDepDifficulty::find()->where(['and', ['difficulty' => $difficulty], ['stageName' => 'дедлайн']])->one()->project;

            return json_encode([
                'date' => date('d.m.Y', $dedline * 24 * 3600 + strtotime('now')), 
                'dedlineTime' => $dedlineTime,
                'pureTime' => $pureTime,
            ]);
        }

        $modelTechDep = new TechDepProject();
        $modelTechDep->scenario = 'form';

        if ( $modelTechDep->load(Yii::$app->request->post())) {
            $modelTechDep->createdAt = strtotime('now');
            $modelTechDep->archive = 0;
            $modelTechDep->authorId = Yii::$app->user->identity->user_id;
            $modelTechDep->status = 1;
            if ($modelTechDep->type == 4) $modelTechDep->goodId = null;
            else $modelTechDep->goodName = null;

            $modelTechDep->projectFiles = UploadedFile::getInstances($modelTechDep, 'projectFiles');
            if ($modelTechDep->save() && $this->addHistoryProject($modelTechDep->id, 1, 'Создан проект') && $modelTechDep->upload()) {
                $this->addCommentProject($modelTechDep->id, Yii::$app->request->post('commentProject'));
                return $this->redirect(['index']);
            }
        }

        $difficulty = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => 'Индив.'
        ];

        return $this->render('project/create', [
            'model' => $modelTechDep,
            'difficulty' => $difficulty
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'form';

        if (Yii::$app->request->isAjax) {
            $file = TechDepFile::findOne(Yii::$app->request->post('id'));
            $comment = 'Удален файл: ' . $file->name;
            if ($this->addHistoryProject($id, $model->status, $comment))
                $file->delete();
            return json_encode(['id' => Yii::$app->request->post('id')]);
        }

        if (Yii::$app->request->post('calc_1') == 'calc_1') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->status = 2;
            if ($model->save())
                return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('calc_2') == 'calc_2') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->load(Yii::$app->request->post());
            if ($model->type != 4) {
                $model->status = 3;
                $model->timeApproved = null;
                $model->timeStart = null;
                $model->readyWork = null;
                $model->inWork = null;
                $model->goodName = null;
                $model->projectFiles = UploadedFile::getInstances($model, 'projectFiles');
                if ($model->save() && $model->upload())
                    return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Измените тип проекта');
                return $this->refresh();
            }
        }

        if (Yii::$app->request->post('in_work') == 'in_work') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->status = 7;
            $model->inWork = empty($model->inWork) ? strtotime('now') : $model->inWork;
            if ($model->save())
                return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('on_approved') == 'on_approved') {
            if (TechDepPlanning::find()->where(['and', ['project' => $id], ['<', 'status', 4]])->count() > 0) {
                Yii::$app->session->setFlash('error', 'Не все этапы утверждены');
                return $this->refresh();
            } else {
                $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
                $model->status = 8;
                if ($model->save()) return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->post('approved') == 'approved') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->status = 9;
            $model->approved = Yii::$app->user->identity->user_id;
            $model->timeApproved = strtotime('now');
            if ($model->save(false))
                return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('archive') == 'archive') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->archive = 1;
            if ($model->save() && $this->addHistoryProject($model->id, $model->status, 'Проект перенесен в архив'))
                return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('reversion') == 'reversion') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->status = 7;
            if ($model->save())
                return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('manager') == 'manager') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->status = 4;
            $model->timeStart = null;
            $model->readyWork = null;
            $model->inWork = null;
            if ($model->save(false))
                return $this->redirect(['index']);
        }

        if (Yii::$app->request->post('in_plan') == 'in_plan') {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->status = 5;

            if (!empty($model->difficulty) && $model->difficulty < 6) {
                $newDedline = (int)TechDepDifficulty::find()
                    ->where(['and', ['difficulty' => $model->difficulty], ['stageName' => 'дедлайн']])
                    ->one()->project * 24*3600 + strtotime('now');
            }
            if ($model->dedline < $newDedline) $model->dedline = $newDedline;

            $model->projectFiles = UploadedFile::getInstances($model, 'projectFiles');
            if ($model->save() && $model->upload())
                return $this->redirect(['index']);
        }

        if ( $model->load(Yii::$app->request->post())) {
            $this->addCommentProject($id, Yii::$app->request->post('commentProject'));
            $model->projectFiles = UploadedFile::getInstances($model, 'projectFiles');
            if ($model->type == 4) $model->goodId = null;
            else $model->goodName = null;
            if ($model->save() && $model->upload())
                return $this->refresh();
        }

        $projectFiles = TechDepFile::find()->where(['project' => $id])->all();
        $projectStageFiles = TechDepStageFile::find()->where(['project' => $id])->all();
        $stagesFiles = array();
        foreach ($projectStageFiles as $file) {
            $stagesFiles[$file->type][] = $file;
        }

        $model->dedline = (!empty($model->dedline) && is_numeric($model->dedline)) ? date('d.m.Y', $model->dedline) : $model->dedline;
        $difficulty = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => 'Индив.'
        ];

        $isStageInWork = TechDepPlanning::find()->where(['and', ['project' => $id], ['in', 'status', [2, 3] ]])->count() > 0;

        $query = TechDepHistoryProject::find()->where(['project' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC, 'id' => SORT_DESC]]
        ]);

        $modelComment = TechDepComment::find()->where(['project' => $id])->orderBy('createdAt DESC')->all();

        return $this->render('project/update', [
            'model' => $model,
            'difficulty' => $difficulty,
            'projectFiles' => $projectFiles,
            'isStageInWork' => $isStageInWork,
            'stagesFiles' => $stagesFiles,
            'dataProvider' => $dataProvider,
            'modelComment' => $modelComment
        ]);
    }

    public function actionPlanning($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'planning';

        if (Yii::$app->request->isAjax) {
            $comment = 'Изменена сложность с '. $model->difficulty .
                ' на ' . Yii::$app->request->post('difficulty') . ': ' . Yii::$app->request->post('comment');
            if ($this->addHistoryProject($id, $model->status, $comment)) return json_encode('success');
        }

        if ( $model->load(Yii::$app->request->post())) {
            $model->status = 6;
            $model->readyWork = $model->readyWork ? $model->readyWork : strtotime('now');
            if ($model->save()) {
                $this->addHistoryProject($id, $model->status, 'Выполнено планирование');
                TechDepPlanning::deleteAll(['and', ['project' => $id], ['<', 'status', 4]]);
                $stageProject = TechDepPlanning::find()->select('stage')->where(['and', ['project' => $id], ['status' => 4]])->column();
                $stages = array_diff(Yii::$app->request->post('stages'), $stageProject);
                foreach ($stages as $stage) {
                    $modelPlanning = new TechDepPlanning();
                    $modelPlanning->project = $id;
                    $modelPlanning->stage = $stage;
                    $modelPlanning->status = 1;
                    if (!empty(Yii::$app->request->post('contractor_'.$stage)))
                        $modelPlanning->contractor = Yii::$app->request->post('contractor_'.$stage);
                    else {
                        Yii::$app->session->setFlash('error', 'Заполните иcполнителей для выбранных этапов');
                    }
                    $modelPlanning->dedlineTime = Yii::$app->request->post('dedline_'.$stage);
                    $modelPlanning->pureTime = Yii::$app->request->post('pure_'.$stage);
                    if ($modelPlanning->save()) $this->addHistoryStage($id, $stage, 1, 'Создан этап');
                }
                $dedlinePlan = TechDepPlanning::find()->where(['project' => $id])->max('dedlineTime');
                if (($model->timeStart + ($dedlinePlan + 1)*24*60*60) > $model->dedline) Yii::$app->session->setFlash('error', 'Проверьте расчет времени');
                if (!Yii::$app->session->hasFlash('error'))
                    $this->redirect(['index']);
            }
        }

        $projectFiles = TechDepFile::find()->where(['project' => $id])->all();
        $model->dedline = date('d.m.Y', $model->dedline);
        $model->timeStart = (!empty($model->timeStart) && is_numeric($model->timeStart)) ? date('d.m.Y', $model->timeStart) : $model->timeStart;
        $stages = TechDepStagesProject::find()->select(['name','id'])->indexBy('id')->column();
        $difficulty = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => 'Индив.'
        ];

        if ($model->difficulty != 6) {
            $dedline = $this->getDifficulty($model->difficulty, 'дедлайн');
            $pure = $this->getDifficulty($model->difficulty, 'чистое');
        } else {
            $dedline = array();
            $pure = array();
        }

        $users = User::getUsersDepartment('Техотдел');

        return $this->render('project/planning', [
            'model' => $model,
            'difficulty' => $difficulty,
            'projectFiles' => $projectFiles,
            'users' => $users,
            'stages' => $stages,
            'dedline' => $dedline,
            'pure' => $pure,
        ]);
    }

    public function actionStage($project, $stage)
    {
        $model = $this->findModel($project);

        $modelStage = $this->findModelStage($project, $stage);
        $modelComment = TechDepComment::find()->where(['project' => $project])->orderBy('createdAt DESC')->all();
        $typeFiles = TechDepTypeFileStage::find()->where(['stage' => $stage])->all();

        if (Yii::$app->request->isAjax) {
            $file = TechDepStageFile::findOne(Yii::$app->request->post('id'));
            $comment = 'Удален файл: ' . $file->name;
            if ($this->addHistoryStage($file->project, $file->stage, $modelStage->status, $comment))
                $file->delete();
            return json_encode(['id' => Yii::$app->request->post('id')]);
        }

        if (Yii::$app->request->post('save') == 'save') {
            $this->addCommentProject($project, Yii::$app->request->post('commentStage'));
            $error = 0;
            foreach ($typeFiles as $type) {
                $typeFile = 'typeFile_' . $type->id;
                $modelStage->$typeFile = UploadedFile::getInstances($modelStage, $typeFile);
                if (!$modelStage->upload($typeFile)) $error = 1;
            }
            if (!$error) return $this->refresh();
        }

        if (Yii::$app->request->post('in_work') == 'in_work') {
            $this->addCommentProject($project, Yii::$app->request->post('commentStage'));
            $modelStage->status = 2;
            $comment = 'Смена статуса';
            if ($modelStage->save() && $this->addHistoryStage($modelStage->project, $modelStage->stage, $modelStage->status, $comment))
                return $this->refresh();
        }

        if (Yii::$app->request->post('on_approved') == 'on_approved') {
            $this->addCommentProject($project, Yii::$app->request->post('commentStage'));
            $error = 0;
            foreach ($typeFiles as $type) {
                $typeFile = 'typeFile_' . $type->id;
                $modelStage->$typeFile = UploadedFile::getInstances($modelStage, $typeFile);
                if (!$modelStage->upload($typeFile)) $error = 1;
            }
            if (!$error) {
                $files = true;
                foreach ($typeFiles as $type) {
                    if (TechDepStageFile::find()->where(['and', ['project' => $project], ['stage' => $stage], ['type' => $type->id]])->count() < $type->countFiles) {
                        $files = false;
                        break;
                    }
                }
                if ($files) {
                    $modelStage->status = 3;
                    $comment = 'Смена статуса';
                    if ($modelStage->save() && $this->addHistoryStage($modelStage->project, $modelStage->stage, $modelStage->status, $comment))
                        return $this->refresh();
                } else Yii::$app->session->setFlash('error', 'Загрузите все файлы для данного этапа');
            }
        }

        if (Yii::$app->request->post('approved') == 'approved') {
            $this->addCommentProject($project, Yii::$app->request->post('commentStage'));
            $modelStage->status = 4;
            $comment = 'Смена статуса';
            if ($modelStage->save() && $this->addHistoryStage($modelStage->project, $modelStage->stage, $modelStage->status, $comment))
                return $this->refresh();
        }

        if (Yii::$app->request->post('reversion') == 'reversion') {
            $this->addCommentProject($project, Yii::$app->request->post('commentStage'));
            $modelStage->status = 2;
            $comment = 'Смена статуса';
            if ($modelStage->save() && $this->addHistoryStage($modelStage->project, $modelStage->stage, $modelStage->status, $comment))
                return $this->refresh();
        }

        $projectFiles = TechDepFile::find()->where(['project' => $project])->all();
        $projectStageFiles = TechDepStageFile::find()->where(['project' => $project])->all();
        $stagesFiles = array();
        foreach ($projectStageFiles as $file) {
            $stagesFiles[$file->type][] = $file;
        }

        $query = TechDepHistoryStage::find()->where(['and', ['project' => $project], ['stage' => $stage]]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC, 'id' => SORT_DESC]]
        ]);

        return $this->render('project/stage', [
            'model' => $model,
            'modelStage' => $modelStage,
            'projectFiles' => $projectFiles,
            'stagesFiles' => $stagesFiles,
            'modelComment' => $modelComment,
            'typeFiles' => $typeFiles,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionReference($project)
    {
        if (Yii::$app->request->isAjax) {
            $materials = Yii::$app->session->get('material');

            if (Yii::$app->request->post('type') == 'remove') {
                unset($materials[(int)Yii::$app->request->post('id')]);
                foreach ($materials as $id => $material) {
                    if ((int)$material['position'] > (int)Yii::$app->request->post('position'))
                        $materials[$id]['position'] = (int)$material['position'] - 1;
                }
                Yii::$app->session->set('material', $materials);
                return json_encode(['remove' => 'success']);
            }

            if (Yii::$app->request->post('type') == 'up') {
                if ((int)Yii::$app->request->post('position') == 1) return json_encode(['up' => 'error']);
                else {
                    $position = (int)Yii::$app->request->post('position');
                    foreach ($materials as $id => $material) {
                        if ((int)$material['position'] == $position - 1)
                            $materials[$id]['position'] = $position;
                        if ((int)$material['position'] == $position)
                            $materials[$id]['position'] = $position - 1;
                    }
                    Yii::$app->session->set('material', $materials);
                    return json_encode(['up' => 'success']);
                }
            }

            if (Yii::$app->request->post('type') == 'down') {
                if ((int)Yii::$app->request->post('position') == count($materials)) return json_encode(['down' => 'error']);
                else {
                    $position = (int)Yii::$app->request->post('position');
                    foreach ($materials as $id => $material) {
                        if ((int)$material['position'] == $position)
                            $materials[$id]['position'] = $position + 1;
                        if ((int)$material['position'] == $position + 1)
                            $materials[$id]['position'] = $position;
                    }
                    Yii::$app->session->set('material', $materials);
                    return json_encode(['down' => 'success']);
                }
            }

            if (Yii::$app->request->post('type') == 'edit') {
                $materials[(int)Yii::$app->request->post('id')]['quantity'] = (float)Yii::$app->request->post('q');
                Yii::$app->session->set('material', $materials);
                return json_encode([
                    'edit' => 'success',
                    'quantity' => (float)Yii::$app->request->post('q'),
                    'price' => $materials[(int)Yii::$app->request->post('id')]['price']
                ]);
            }

            if (empty($materials[(int)Yii::$app->request->post('id')])) {
                $count = count($materials) + 1;
                $materials[(int)Yii::$app->request->post('id')] = [
                    'quantity' => (float)Yii::$app->request->post('q'),
                    'position' => $count,
                    'id' => (int)Yii::$app->request->post('id'),
                    'name' => Yii::$app->request->post('name'),
                    'unit' => Yii::$app->request->post('unit'),
                    'price' => Good::findOne(Yii::$app->request->post('id'))->price
                ];
                Yii::$app->session->set('material', $materials);
                return json_encode(['type' => 'add', 'material' => $materials[(int)Yii::$app->request->post('id')]]);
            } else {
                $materials[(int)Yii::$app->request->post('id')]['quantity'] = (float)Yii::$app->request->post('q');
                Yii::$app->session->set('material', $materials);
                return json_encode([
                    'type' => 'edit',
                    'id' => (int)Yii::$app->request->post('id'),
                    'quantity' => (float)Yii::$app->request->post('q'),
                    'price' => $materials[(int)Yii::$app->request->post('id')]['price']
                ]);
            }
        }

        $goodSearchModel = new GoodSearch();

        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('submit_button') == 'submit_button' && !empty($materials = Yii::$app->session->get('material'))) {
                TechDepMaterialsProject::deleteAll(['and', ['project' => $project], ['not in', 'material', array_keys($materials)]]);

                foreach ($materials as $id => $material) {
                    if (!empty($materialsModel = TechDepMaterialsProject::find()->where(['and', ['project' => $project], ['material' => $id]])->one())) {
                        $materialsModel->quantity = (float)$material['quantity'];
                        $materialsModel->position = (int)$material['position'];
                        $materialsModel->save();
                    } else {
                        $materialsModel = new TechDepMaterialsProject();
                        $materialsModel->project = (int)$project;
                        $materialsModel->material = (int)$id;
                        $materialsModel->quantity = (float)$material['quantity'];
                        $materialsModel->position = (int)$material['position'];
                        $materialsModel->save();
                    }
                }
                Yii::$app->session->set('material', null);
                return $this->redirect(['stage', 'project' => $project, 'stage' => 4]);
            }
        }

        $materials = array();
        $materialsModel = TechDepMaterialsProject::find()->where(['project' => $project])->orderBy('position')->all();
        $total = 0;
        foreach ($materialsModel as $material) {
            $materials[$material->material] = [
                'position' => $material->position,
                'id' => $material->material,
                'name' => !empty($material->materialProject) ? $material->materialProject->goods_name : '-',
                'unit' => !empty($material->materialProject->unit) ? $material->materialProject->unit->list_unit_name : '-',
                'quantity' => $material->quantity,
                'price' => !empty($material->materialProject) ? $material->materialProject->price : 0
            ];
            $total += $material->quantity * (!empty($material->materialProject) ? $material->materialProject->price : 0);
        }
        Yii::$app->session->set('material', $materials);
        $canEdit = TechDepPlanning::find()->select('status')->where(['and', ['project' => $project], ['stage' => 4]])->scalar() == 2
            || Yii::$app->user->identity->checkRule('tech-dep', 3);

        return $this->render('project/reference', [
            'goodSearchModel' => $goodSearchModel,
            'materials' => $materials,
            'total' => (float)$total,
            'project' => $project,
            'canEdit' => $canEdit
        ]);
    }

    public function actionDelete($id)
    {
        TechDepComment::deleteAll(['project' => $id]);
        TechDepFile::deleteAll(['project' => $id]);
        TechDepStageFile::deleteAll(['project' => $id]);
        TechDepHistoryProject::deleteAll(['project' => $id]);
        TechDepHistoryStage::deleteAll(['project' => $id]);
        TechDepMaterialsProject::deleteAll(['project' => $id]);
        TechDepPlanning::deleteAll(['project' => $id]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDifficulty()
    {
        $this->view->params['fullWidth'] = true;

        if (Yii::$app->request->isPost) {
            $q = count(Yii::$app->request->post('techTask'));
            for ($i=0; $i < $q ; $i++) {
                $model = TechDepDifficulty::findOne($i+1);
                $model->project = (int)Yii::$app->request->post('project')[$i];
                $model->techTask = (int)Yii::$app->request->post('techTask')[$i];
                $model->calc1 = (int)Yii::$app->request->post('calc1')[$i];
                $model->calc2 = (int)Yii::$app->request->post('calc2')[$i];
                $model->plan = (int)Yii::$app->request->post('plan')[$i];
                $model->calcTech = (int)Yii::$app->request->post('calcTech')[$i];
                $model->model = (int)Yii::$app->request->post('model')[$i];
                $model->draw = (int)Yii::$app->request->post('draw')[$i];
                $model->spec = (int)Yii::$app->request->post('spec')[$i];
                $model->materials = (int)Yii::$app->request->post('materials')[$i];
                $model->tools = (int)Yii::$app->request->post('tools')[$i];
                $model->techMap = (int)Yii::$app->request->post('techMap')[$i];
                $model->passport = (int)Yii::$app->request->post('passport')[$i];
                $model->save();
            }

            return $this->refresh();
        }

        $model = TechDepDifficulty::find()->all();

        return $this->render('difficulty', ['model' => $model]);
    }

    protected function findModel($id)
    {
        if (($model = TechDepProject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelStage($project, $stage)
    {
        if (($model = TechDepPlanning::find()->where(['and', ['project' => $project], ['stage' => $stage]])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function getUsers($role)
    {
        switch ($role) {
            case 'author':
                $column = 'authorId';
                $get = 'authorProject';
                break;
            case 'responsible':
                $column = 'responsible';
                $get = 'responsibleProject';
                break;
            case 'approved':
                $column = 'approved';
                $get = 'approvedProject';
                break;
        }

        $users = TechDepProject::find()->select([$column])->distinct()->indexBy($column)->all();
        $result = array();
        foreach ($users as $key => $userId) {
            $user = $userId->$get;
            if (!empty($user))
                $result[$key] = $user->shortName;
        }
        $result['0'] = 'Все';
        ksort($result);
        return $result;
    }

    private function getDifficulty($difficulty, $stageName)
    {
        $modelDifficulty =  TechDepDifficulty::find()->where(['and', ['difficulty' => $difficulty], ['stageName' => $stageName]])->one();
        $result = [
            '1' => $modelDifficulty->calc1,
            '2' => $modelDifficulty->calc2,
            '3' => $modelDifficulty->calcTech,
            '4' => $modelDifficulty->materials,
            '5' => $modelDifficulty->model,
            '6' => $modelDifficulty->draw,
            '7' => $modelDifficulty->spec,
            '8' => $modelDifficulty->tools,
            '9' => $modelDifficulty->techMap,
            '10' => $modelDifficulty->passport,
        ];

        return $result;
    }

    private function addCommentProject($project, $comment)
    {
        if (!empty($comment)) {
            $model = new TechDepComment();
            $model->project = $project;
            $model->author = Yii::$app->user->identity->user_id;
            $model->createdAt = strtotime('now');
            $model->comment = $comment;
            $model->save();
        }
    }

    private function addHistoryStage($project, $stage, $status, $comment)
    {
        $modelHistory = new TechDepHistoryStage();
        $modelHistory->project = $project;
        $modelHistory->stage = $stage;
        $modelHistory->createdAt = strtotime('now');
        $modelHistory->author = Yii::$app->user->identity->user_id;
        $modelHistory->status = $status;
        $modelHistory->comment = $comment;
        return $modelHistory->save();
    }

    private function addHistoryProject($project, $status, $comment)
    {
        $modelHistory = new TechDepHistoryProject();
        $modelHistory->project = $project;
        $modelHistory->createdAt = strtotime('now');
        $modelHistory->author = Yii::$app->user->identity->user_id;
        $modelHistory->status = $status;
        $modelHistory->comment = $comment;
        return $modelHistory->save();
    }
}
