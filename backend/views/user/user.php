<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var UserForm $userForm */
/** @var User $user */

use backend\models\UserForm;
use common\models\User;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = $user->username . ' felhasználói adatai';
$this->params['breadcrumbs'][] = ['label' => 'Felhasználók listája', 'url' => ['admin-users'], 'class' => 'green'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Felhasználó ID: <?= $user->id ?>
        <br>
        Regisztráció ideje: <?= $user->created_at ?>
        <br>
        Utolsó bejelentkezés ideje: <?= $user->last_login ?>
    </p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>

            <!-- This form field is hidden and only used to pass users ID to form -->
            <?= $form->field($userForm, 'id')->textInput(['autofocus' => true, 'value' => $user->id, 'hidden' => true])->label(false) ?>

            <?= $form->field($userForm, 'username')->textInput(['autofocus' => true, 'value' => $user->username])->label('Felhasználónév') ?>

            <?= $form->field($userForm, 'email')->textInput(['value' => $user->email])->label('E-mail cím') ?>

            <?= $form->field($userForm, 'admin')->checkbox(['value' => !$user->is_admin, 'selected' => $userForm->admin, 'disabled' => $user->id === Yii::$app->user->id])->label('Admin jogosultság'); ?>

            <div class="form-group">
                <?= Html::submitButton('Adatok módosítása', ['class' => 'btn btn-primary green-bcg', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php
            echo '<div>'
                . Html::beginForm(['user/delete-user', 'id' => $user->id])
                . Html::submitButton('Felhasználó törlése', ['class' => 'delete-btn'])
                . Html::endForm()
                . '</div>';
            ?>
        </div>
    </div>

</div>