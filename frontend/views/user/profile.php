<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var ProfileForm $profileForm */

use frontend\models\ProfileForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Személyes adatok';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile">
    <h1 class="big-title"><?= Html::encode($this->title) ?></h1>

    <p class="subtitle">
        Regisztráció ideje: <?= Yii::$app->user->identity->created_at ?>
        <br>
        Utolsó bejelentkezés ideje: <?= Yii::$app->user->identity->last_login ?>
    </p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>

            <?= $form->field($profileForm, 'username')->textInput(['autofocus' => true, 'value'=>Yii::$app->user->identity->username])->label('Felhasználónév') ?>

            <?= $form->field($profileForm, 'email')->textInput(['value'=>Yii::$app->user->identity->email])->label('E-mail cím') ?>

            <div class="form-group">
                <?= Html::submitButton('Adatok módosítása', ['class' => 'btn btn-primary green-bcg', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>