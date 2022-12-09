<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        A fent olvasható hiba történt a kérésed feldolgozása alatt.
    </p>
    <p>
        Kérjük vedd fel a kapcsolatot velünk, ha ezt szerveroldali hibának gondolod.
    </p>

</div>
