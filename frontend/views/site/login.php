<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var LoginForm $model */

use common\models\LoginForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Bejelentkezés';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Kérlek töltsd ki a következő mezőket a bejelentkezéshez:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Felhasználónév') ?>

                <?= $form->field($model, 'password')->passwordInput()->label('Jelszó') ?>

                <?= $form->field($model, 'rememberMe')->checkbox()->label('Emlékezz rám') ?>

                <div style="color:#999;margin:1em 0">
                    Ha elfelejtetted a jelszavadat <?= Html::a('reseteld', ['site/request-password-reset']) ?>.
                    <br>
                    Nem érkezett meg a megerősítő e-mail? <?= Html::a('Újraküldés', ['site/resend-verification-email']) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Bejelentkezés', ['class' => 'btn btn-primary green-bcg', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
