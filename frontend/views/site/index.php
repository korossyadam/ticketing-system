<?php

use common\models\Ticket;
use frontend\controllers\SiteController;
use yii\grid\GridView;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var SiteController $listDataProvider */

// Set timezone
date_default_timezone_set('Europe/Budapest');

$this->title = 'Ticketing rendszer';

// Set username if user is logged in
$user = Yii::$app->user;
if (is_object($user->identity)) {
    $userName = $user->identity->username;
} else {
    $userName = "";
}

?>
<div class="site-index">

    <div class="spacing text-center bg-transparent">

        <!-- Always displays welcome text, displays username if it exists -->
        <h1 class="display-4">Üdvözlünk<span class="green"><?php if ($userName !== "") {echo ' ' . $userName;} ?></span>.</h1>

        <?php
        if ($userName !== "") {
            echo "<p class='lead'>Hozz létre új hibajegyet, vagy böngészd a korábbiakat.</p>";
        } else {
            echo "<h2 class='lead'>Kérlek jelentkezz be vagy regisztrálj a hibajegy létrehozásához!</h2>";
        }
        ?>

    </div>

    <div class="body-content">

        <?php

        // Check whether user is logged in or not
        if ($userName !== "") {

            // New Ticket button
            echo '<div>'
                . Html::beginForm(['ticket/new-ticket'])
                . Html::submitButton('Új hibajegy létrehozása (+)',
                    ['class' => 'green-bcg main-btn'])
                . Html::endForm()
                . '</div>';

            // Content of GridView-columns
            $gridColumns = [
                [
                    'attribute' => 'Státusz',
                    'value' => function (Ticket $ticket) {
                        return Yii::$app->controller->renderPartial('_ticketStatus', ['ticket' => $ticket]);
                    },
                    'header' => '',
                    'headerOptions' => ['class' => 'header'],
                    'format' => 'raw'],
                [
                    'attribute' => 'id',
                    'value' => function (Ticket $ticket) {
                        return $ticket->id;
                    },
                    'header' => 'Hibajegy ID',
                    'headerOptions' => ['class' => 'header']
                ],
                [
                    'attribute' => 'title',
                    'value' => function (Ticket $ticket) {
                        return $ticket->title;
                    },
                    'header' => 'Hibajegy cím',
                    'headerOptions' => ['class' => 'header']
                ],
                [
                    'attribute' => 'date',
                    'value' => function (Ticket $ticket) {
                        return $ticket->created_at;
                    },
                    'header' => 'Létrehozás',
                    'headerOptions' => ['class' => 'header']
                ],
                [
                    'attribute' => 'date',
                    'value' => function (Ticket $ticket) {
                        return $ticket->updated_at;
                    }, 'header' => 'Utolsó frissítés',
                    'headerOptions' => ['class' => 'header']
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'header' => 'Megtekintés',
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

            // GridView
            try {
                echo GridView::widget([
                    'dataProvider' => $listDataProvider,
                    'options' => [
                        'tag' => 'div',
                        'class' => 'table-responsive table-grid',
                        'id' => 'list-wrapper',
                        'style' => 'margin: 0;',
                    ],
                    'columns' => $gridColumns,
                    'tableOptions' => ['class' => 'table table-bordered'],

                    'layout' => "{pager}\n{items}\n{summary}",
                ]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
                Yii::error($e);
            }
        } else {

            // Login button (for non-registered user)
            echo '<div>'
                . Html::beginForm(['site/login'])
                . Html::submitButton('Bejelentkezés',
                    ['style' => 'width: 100%; padding: 12px; margin-bottom: 20px; background-color: #343a40; color: #fff; font-size: 32px'])
                . Html::endForm()
                . '</div>';

            // Signup button (for non-registered user)
            echo '<div>'
                . Html::beginForm(['site/signup'])
                . Html::submitButton('Regisztráció',
                    ['style' => 'width: 100%; padding: 12px; margin-top: 20px; margin-bottom: 20px; background-color: #343a40; color: #fff; font-size: 32px'])
                . Html::endForm()
                . '</div>';
        }

        ?>

    </div>
</div>
