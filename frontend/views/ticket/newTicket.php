<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var NewTicketForm $ticketForm */
/** @var NewCommentForm $commentForm */

use frontend\models\NewCommentForm;
use frontend\models\NewTicketForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Új hibajegy';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-new-ticket">
    <h1 class="big-title"><?= Html::encode($this->title) ?></h1>

    <p class="subtitle">Ha bármilyen észrevételed vagy problémád akadt, kérlek töltsd ki az alábbi mezőket. Köszönjük.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'new-ticket-form']); ?>

            <?= $form->field($ticketForm, 'title')->textInput(['autofocus' => true])->label('Cím') ?>

            <?= $form->field($commentForm, 'text')->textarea(['rows' => 6])->label('Szövegtörzs') ?>

            <?= $form->field($commentForm, 'imageFile')->fileInput()->label('Kép feltöltése') ?>

            <?= $form->field($ticketForm, 'verifyCode')->widget(Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ])->label('Kérlek add meg az alábbi karaktereket!') ?>

            <div class="form-group">
                <?= Html::submitButton('Hibajegy létrehozása', ['class' => 'btn btn-primary green-bcg', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>