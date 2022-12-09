<?php

use yii\bootstrap4\Html;

/** @var yii\web\View $this */

// Set timezone
date_default_timezone_set('Europe/Budapest');

$this->title = 'Admin felület';
?>
<div class="site-index">

    <div class="spacing text-center bg-transparent">
        <h1 class="display-4">Admin felület</h1>

        <p class="lead">Keress <span class="green">hibajegyek</span>, vagy <span class="yellow">felhasználók</span> között.</p>
    </div>

    <?php

        // List all Tickets button
        echo '<div>'
            . Html::beginForm(['ticket/admin-tickets'])
            . Html::submitButton('Hibajegyek listázása',
                ['class' => 'main-btn green-bcg'])
            . Html::endForm()
            . '</div>';

        // List all Users button
        echo '<div>'
            . Html::beginForm(['user/admin-users'])
            . Html::submitButton('Felhasználók listázása',
                ['class' => 'main-btn yellow-bcg'])
            . Html::endForm()
            . '</div>';

        ?>

</div>
