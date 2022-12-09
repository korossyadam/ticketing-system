<?php

use backend\models\AdminUserForm;
use common\models\User;
use common\models\UserSearch;
use yii\bootstrap4\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var AdminUserForm $adminUserForm */
/** @var ActiveDataProvider $listDataProvider */
/** @var UserSearch $searchModel */

$this->title = 'Felhasználók listája';
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'admin-user-form']); ?>

            <?= $form->field($adminUserForm, 'date_start')->textInput(['value' => '1970-01-01'])->label('Regisztrált: Ettől a naptól') ?>

            <?= $form->field($adminUserForm, 'date_end')->textInput(['value' => date('Y-m-d')])->label('Regisztrált: Eddig a napig') ?>

            <div class="form-group">
                <?= Html::submitButton('Felhasználók listázása', ['class' => 'btn btn-primary green-bcg', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php

// Content of GridView-columns
$gridColumns = [
    [
        'attribute' => 'id',
        'value' => function (User $user) {
            return $user->id;
        },
        'header' => 'Felhasználó ID',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => Yii::$app->user->id
        ]
    ],
    [
        'attribute' => 'username',
        'value' => function (User $user) {
            return $user->username;
        },
        'header' => 'Felhasználónév',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => Yii::$app->user->identity->username
        ]
    ],
    [
        'attribute' => 'email',
        'value' => function (User $user) {
            return $user->email;
        },
        'header' => 'E-mail cím',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => Yii::$app->user->identity->email
        ]
    ],
    [
        'attribute' => 'created_at',
        'value' => function (User $user) {
            return $user->created_at;
        },
        'header' => 'Regisztrált',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => date('Y-m-d')
        ]
    ],
    [
        'attribute' => 'updated_at',
        'value' => function (User $user) {
            return $user->updated_at;
        },
        'header' => 'Utolsó frissítés',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => date('Y-m-d')
        ]
    ],
    [
        'attribute' => 'last_login',
        'value' => function (User $user) {
            return $user->last_login;
        },
        'header' => 'Utolsó bejelentkezés',
        'headerOptions' => ['class' => 'header'],
        'filterInputOptions' => [
            'class' => 'form-control',
            'placeholder' => date('Y-m-d')
        ]
    ],
    [
        'attribute' => 'is_admin',
        'value' => function (User $user) {
            if ($user->is_admin) {
                return '<p class="green" style="font-weight: bold">Igen</p>';
            } else {
                return 'Nem';
            }
        },
        'header' => 'Admin?',
        'headerOptions' => ['class' => 'header'],
        'format' => 'raw',
        'filter' => Html::activeDropDownList($searchModel, 'is_admin', ['Összes', 'Igen', 'Nem'],['class'=>'form-control']),],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update}',
        'header' => 'Lehetőségek',
        'headerOptions' => ['class' => 'header'],
        'buttons' => [
            'view' => function($url, $model) {
                return Html::a('<button class="btn btn-primary green-bcg btn-smaller">Felhasználó hibajegyei<i class="glyphicon glyphicon-eye-open"></i></button>', $url);
            },
            'update' => function($url, $model) {
                return Html::a('<button class="btn btn-primary yellow-bcg btn-smaller">Felhasználó módosítása<i class="glyphicon glyphicon-eye-open"></i></button>', $url);
            }],
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                return Url::to(['ticket/admin-tickets', 'id' => $model->id]);
            } else {
                return Url::to(['user/user', 'id' => $model->id]);
            }
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

