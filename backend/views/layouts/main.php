<?php

/** @var View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\web\View;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Bejelentkezés', 'url' => ['site/admin-login']];
    } else {
        $menuItems[] = ['label' => 'Főoldal', 'url' => ['site/index']];
        $menuItems[] = ['label' => 'Hibajegyek', 'url' => ['ticket/admin-tickets']];
        $menuItems[] = ['label' => 'Felhasználók', 'url' => ['user/admin-users']];
        $menuItems[] = '<li>'
            . Html::beginForm(['site/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
                'Kijelentkezés (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout navbar-size']
            )
            . Html::endForm()
            . '</li>';
    }
    try {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ml-auto navbar-size'],
            'items' => $menuItems,
        ]);
    } catch (Exception $e) {
        Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
        Yii::error($e);
    }
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => Yii::t('yii', 'Főoldal'),
                'url' => Yii::$app->homeUrl,
                'style' => 'color: green',
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
