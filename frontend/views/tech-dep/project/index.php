<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\TechDep\TechDepStagesProject;
use common\models\TechDep\TechDepDifficulty;
use frontend\assets\TechDep\TechDepAsset;

TechDepAsset::register($this);

$this->title = 'Реестр техотдела';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tech-dep-project-index">
    <div class="box box-info">
        <div class="box-body">
            <?= Html::a('Создать проект', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <div class="box box-info box-solid">

        <div class="box-header with-border">
            <h3 class="box-title">Поиск</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body no-padding">
            <?=$this->render('_search', [
                'model' => $searchModel,
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
            ]); ?>
        </div>

    </div>
    
    <div class="box box-info">
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'<div class="box-body no-padding table-responsive">{items}</div>
                           <div class="box-footer">{pager}</div>',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered no-padding'
        ],
        'columns' => [
            'id' => [
                'label' => 'ID',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'html',
                'value' => function($project)
                {
                    $user = $project->authorProject;
                    $author = $user->shortName;
                    $value = '<div><a href="./update?id='.$project->id.'">'.$project->id.'</a> </div>
                             <div>'.date('d.m.Y H:i', $project->createdAt).'</div>
                             <div style="white-space: nowrap;">'.$author.'</div>';
                    return $value;
                }
            ],
            'orderNumber' => [
                'label' => 'Заказ',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'headerOptions'   =>   ['style' => 'padding: 8px 3px;'],
                'value' => function($project)
                {
                    return $project->orderNumber;
                }
            ],
            'goodId' => [
                'label' => 'Наименование',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'html',
                'value' => function($project)
                {
                    $user = $project->responsibleProject;
                    $responsible = !empty($user) ? $user->shortName : '-';
                    return '<div>' . (!empty($project->goodId) ? $project->goodProject->goods_name : $project->goodName) . '</div>'
                            . '<div><b>Ответственный:</b> ' . $responsible . '</div>';
                }
            ],
            'type' => [
                'label' => 'Тип',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'raw',
                'value' => function($project)
                {
                    $value = '';
                    if ($project->priority == 1) {
                        $value .= '<div><div class="sign" data-title="ОГОНЬ!!!"><img src="/images/driver/fire.png" alt="фото"></div></div>';
                    }elseif ($project->priority == 2) {
                        $value .= '<div><div class="sign" data-title="Важно!"><img src="/images/driver/warning.png" alt="фото"></div></div>';
                    }
                    $value .= '<div>' . $project->typeProject->name . '</div>';
                    if ($project->status == 9) $value .= '<div>' . ($project->archive ? 'А' : 'Д') . '</div>';
                    return $value;
                }
            ],
            'stages' => [
                'label' => 'Даты этапов',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'html',
                'value' => function($project)
                {
                    if ($project->status != 9) {
                        $approved = $project->dedline - 24*60*60;
                        $timeNow = strtotime('now');
                        $days = (($project->dedline - $timeNow) > 0 && ($project->dedline - $timeNow) < 86400) ? 'сегодня' : 
                            (($project->dedline > $timeNow) ? floor(($project->dedline - $timeNow)/86400) : floor(($project->dedline - $timeNow)/86400)) . ' дн';
                        $approvedDays = (($approved - $timeNow) > 0 && ($approved - $timeNow) < 86400) ? 'сегодня' : 
                            (($approved > $timeNow) ? floor(($approved - $timeNow)/86400) : floor(($approved - $timeNow)/86400)) . ' дн';
                        return '<div>Готов к работе:</div><div>' . ($project->readyWork ? date('d.m.Y H:i', $project->readyWork) : '-') . '</div>
                            <div>Взят в работу:</div><div>' . ($project->inWork ? date('d.m.Y H:i', $project->inWork) : '-') . '</div>' .

                            '<div>На утвержд.:</div><div class="' .
                            ($approvedDays >= 1 ? 'green' : ($approvedDays <= -1 ? 'red' : 'pink')) . '">' . $approvedDays . '</div>
                            <div>Дедлайн:</div><div class="' . ($days >= 1 ? 'green' : ($days <= -1 ? 'red' : 'pink')) . '">' . $days . '</div>';
                    }
                    else return '<div>Готов к работе:</div><div>' . ($project->readyWork ? date('d.m.Y H:i', $project->readyWork) : '-') . '</div>
                            <div>Взят в работу:</div><div>' . ($project->inWork ? date('d.m.Y H:i', $project->inWork) : '-') . '</div>' . 
                            '<div>Утверждено:</div><div>' . ($project->timeApproved ? date('d.m.Y H:i', $project->timeApproved) : '-') . '</div>' .
                            '<div>Утвердил:</div><div>' . $project->approvedProject->shortName . '</div>' .
                            '<div>Отклонение:</div><div class="' . (($project->dedline - $project->timeApproved) < 0 ? 'red' : 'green') . '">' . 
                            floor(($project->dedline - $project->timeApproved)/86400) .
                            ' дн.</div>';
                }
            ],
            'difficulty' => [
                'label' => 'Сложн. и время',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'html',
                'value' => function($project)
                {
                    $time = 0;
                    if (!empty($project->stagesProject))
                        foreach ($project->stagesProject as $stage) {
                            $time += $stage->pureTime;
                        }
                    return '<div> Сл. ' . ($project->difficulty == 6 ? 'Индив' : $project->difficulty) . '</div>'
                            . '<div>Время:</div><div>' . ($time ? floor($time / 60) . ' ч ' . $time % 60 . ' мин' : '-') . '</div>';
                }
            ],
            'status' => [
                'label' => 'Статус',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'html',
                'value' => function($project)
                {
                    return '<div class="' . $project->statusProject->color . '">' . $project->statusProject->name . '</div>';
                }
            ],
            'notice' => [
                'label' => 'Примечания/ Комментарии',
                'contentOptions'   =>   ['style' => 'padding: 3px;'],
                'format' => 'raw',
                'value' => function($project)
                {
                    return '<div>' . $project->notice . '</div>' .
                    (!empty($project->notice) && !empty($project->commentsProject[0]) ? '<hr style="margin: 5px 0">' : '') .
                    '<div>' . (!empty($project->commentsProject[0]) ? $project->commentsProject[0]->comment .
                        '<div class="see-comment" id="project_'.$project->id.'">Показать все</div>' : '') .
                    '</div>';
                }
            ],
            'in_work' => [
                'label' => 'Этапы проекта',
                'contentOptions'   =>   ['class' => 'no-padding'],
                'format' => 'html',
                'value' => function($project)
                {
                    $table = '<table class="table no-padding no-margin table-bordered"><tr>';
                        for ($i = 1; $i <= 10; $i++) {
                            if (!empty($project->stagesProject[$i]))
                                $days = ceil((($project->timeStart + $project->stagesProject[$i]->dedlineTime * 24*60*60) - strtotime('now')) / 86400);
                            if ($i == 6) $table .= '</tr><tr>';
                            $table .= 
                                '<td style="padding: 2px; font-size: 12px; border-color: #a9c; background-color: '. (!empty($project->stagesProject[$i]) ? 'white' : 'silver') .'; ">' .
                                    (!empty($project->stagesProject[$i]) ?
                                    '<div><b>' . (( $project->responsible == Yii::$app->user->identity->user_id || $project->stagesProject[$i]->contractor == Yii::$app->user->identity->user_id) ?
                                        Html::a($project->stagesProject[$i]->stageProject->shortName, ['stage', 'project' => $project->id, 'stage' => $i]) :
                                        $project->stagesProject[$i]->stageProject->shortName) . '<div></div>' .
                                    $project->stagesProject[$i]->pureTime .
                                    ' мин</b></div><div>' .
                                    (!empty($project->stagesProject[$i]->contractorStage) ? $project->stagesProject[$i]->contractorStage->shortName : '-' ).
                                    '</div>' . ( $project->stagesProject[$i]->status != 4 ?
                                    '<div class="' . ($days < 0 ? 'red' : ($days > 0 ? 'green' : 'pink')) . '">'. ($days ? $days . ' дн.' : 'сегодня') . '</div>' : '') .
                                    '<div class="' . $project->stagesProject[$i]->statusStage->color . '">' . $project->stagesProject[$i]->statusStage->name . '</div>' :
                                    '<div><b>' . TechDepStagesProject::findOne($i)->shortName . '</b></div>') .
                                '</td>';
                        }
                    $table .= '</tr></table>';
                    return $table;
                }
            ],
        ],
    ]); ?>
    </div>

    <div class="modal fade" id="commentProject" tabindex="-1" role="dialog" aria-labelledby="commentProjectLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="commentProjectLabel">Комментарии к проекту</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-solid">
                        <div class="box-body">
                            <table class="table table-striped">
                                <thead>
                                    <th width="20%">Дата и время</th>
                                    <th width="20%">Автор</th>
                                    <th width="60%">Комментарий</th>
                                </thead>

                                <tbody id="view_comments_tbody">
                                </tbody>
                            </table>
                        </div>
                        <div id="view_notes_overlay" class="overlay" style="display: none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>
