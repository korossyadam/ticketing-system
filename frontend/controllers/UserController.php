<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\ProfileForm;
use yii\web\Response;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays the profile page
     * @return Response|string
     */
    public function actionProfile()
    {
        // Access is denied if user is not logged in
        $user = Yii::$app->user;
        if (!is_object($user->identity)) {
            return Yii::$app->response->redirect(['site/index']);
        }

        $profileForm = new ProfileForm();
        if ($profileForm->load(Yii::$app->request->post()) && $profileForm->validate()) {

            // Attempt to change personal data
            $tr = Yii::$app->db->beginTransaction();
            try {
                $profileForm->changeProfile();
                $tr->commit();
                Yii::$app->session->setFlash('success', 'Sikeresen megváltoztattad a felhasználói adataidat.');
                Yii::info('User update was successful.');
            } catch (\Exception $e) {
                $tr->rollBack();
                Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
                Yii::error($e);
            }

            return $this->refresh();
        }

        return $this->render('profile', ['profileForm' => $profileForm]);
    }


}
