<?php

use backend\models\AdminTicketForm;
use common\models\Ticket;
use common\models\TicketSearch;
use yii\bootstrap4\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var AdminTicketForm $adminTicketForm */
/** @var ActiveDataProvider $listDataProvider */
/** @var TicketSearch $searchModel */
/** @var string $username */

if ($username === '') {
    $this->title = 'Hibajegyek listája';
} else {
    $this->title = $username . ' hibajegyeinek listája';
    $this->params['breadcrumbs'][] = ['label' => 'Hibajegyek listája', 'url' => ['admin-tickets'], 'class' => 'green'];
}

$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'admin-ticket-form']); ?>

                <?= $form->field($adminTicketForm, 'date_start')->textInput(['value' => '1970-01-01'])->label('Létrehozva: Ettől a naptól') ?>

                <?= $form->field($adminTicketForm, 'date_end')->textInput(['value' => date('Y-m-d')])->label('Létrehozva: Eddig a napig') ?>

                <?= $form->field($adminTicketForm, 'user_id')->textInput([])->label('Felhasználó ID (Üres, ha mindegy)') ?>

                <?= $form->field($adminTicketForm, 'comment_text')->textInput([])->label('Keresés hozzászólásban (Üres, ha mindegy)') ?>

                <div class="form-group">
                    <?= Html::submitButton('Hibajegyek listázása', ['class' => 'btn btn-primary green-bcg', 'name' => 'contact-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php

// Content of GridView-columns
$gridColumns = [
    [
        'attribute' => 'is_closed',
        'value' => function (Ticket $ticket) {
            return Yii::$app->controller->renderPartial('_ticketStatus', ['ticket' => $ticket]);
        },
        'header' => 'Státusz',
        'headerOptions' => ['class' => 'header'],
        'format' => 'raw',
        'filter' => Html::activeDropDownList($searchModel, 'is_closed', ['Összes', 'Nyitott', 'Zárt'],['class'=>'form-control']),
        'filterInputOptions' => [
            'class' => 'form-control'
        ]
    ],
    [
        'attribute' => 'id',
        'value' => function (Ticket $ticket) {
            return $ticket->id;
        },
        'header' => 'Hibajegy ID',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => Yii::$app->user->id
        ]
    ],
    [
        'attribute' => 'customerEmail',
        'value' => function (Ticket $ticket) {
            // Clickable User email, redirects to User's profile page
            $url = Url::to(['user/user', 'id' => $ticket->customer_id]);
            return "<a href='$url'>" . $ticket->customer->email . '</a>';
        },
        'header' => 'Szerző',
        'headerOptions' => ['class' => 'header'],
        'format' => 'raw',
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => Yii::$app->user->identity->email
        ]
    ],
    [
        'attribute' => 'title',
        'value' => function (Ticket $ticket) {
            return $ticket->title;
        },
        'header' => 'Hibajegy címe',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => 'Példa cím'
        ]
    ],
    [
        'attribute' => 'created_at',
        'value' => function (Ticket $ticket) {
            return $ticket->created_at;
        },
        'header' => 'Létrehozás',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => date('Y-m-d')
        ]
    ],
    [
        'attribute' => 'last_comment_at',
        'value' => function (Ticket $ticket) {
            return $ticket->last_comment_at;
        },
        'header' => 'Utolsó hozzászólás',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => date('Y-m-d')
        ]
    ],
    [
        'attribute' => 'adminEmail',
        'value' => function (Ticket $ticket) {
        // Check whether Ticket has an admin assigned to it
        if ($ticket->admin_id !== -1) {
            // Check if assigned admin is the current admin
            if ($ticket->admin_id === Yii::$app->user->id) {
                return '<p class="green" style="font-weight: bold">' . $ticket->admin->email . '</p>';
            } else {
                return $ticket->admin->email;
            }
        } else {
            return '<p class="yellow" style="font-weight: bold">Nincs</p>';
        }
        },
        'header' => 'Hozzárendelt Admin',
        'headerOptions' => ['class' => 'header'],
        'format' => 'raw',
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => Yii::$app->user->identity->email
        ]
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}',
        'header' => 'Lehetőségek',
        'headerOptions' => ['class' => 'header'],
        'buttons' => [
            'view' => function($url, $model)   {
                return Html::a('<button class="btn btn-primary green-bcg">Megnyitás<i class="glyphicon glyphicon-eye-open"></i></button>', $url);
            }],
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to(['ticket/ticket', 'id' => $model->id]);
        }
    ],
];

// GridView Widget
echo GridView::widget([
    'dataProvider' => $listDataProvider,
    'filterModel' => $searchModel,
    'options' => [
        'tag' => 'div',
        'class' => 'table-responsive table-grid',
        'id' => 'list-wrapper',
    ],
    'columns' => $gridColumns,
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => "{pager}\n{items}\n{summary}",
]);

