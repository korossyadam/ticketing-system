<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var AdminLoginForm $model */

use backend\models\AdminLoginForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Adminisztrátor bejelentkezés';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-admin-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Felhasználónév') ?>

            <?= $form->field($model, 'password')->passwordInput()->label('Jelszó') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('Emlékezz rám') ?>

            <div class="form-group">
                <?= Html::submitButton('Bejelentkezés', ['class' => 'btn btn-primary green-bcg', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
