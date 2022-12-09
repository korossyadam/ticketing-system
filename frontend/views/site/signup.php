<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var SignupForm $model */

use frontend\models\SignupForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Regisztráció';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Kérlek töltsd ki a következő mezőket a regisztrációhoz:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Felhasználónév') ?>

                <?= $form->field($model, 'email')->label('E-mail cím') ?>

                <?= $form->field($model, 'password')->passwordInput()->label('Jelszó') ?>

                <div class="form-group">
                    <?= Html::submitButton('Regisztráció', ['class' => 'btn btn-primary green-bcg', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
