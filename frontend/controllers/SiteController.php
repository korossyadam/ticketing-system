<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Ticket;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\web\Response;

class SiteController extends Controller
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
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Ticket::find()
                ->where(["customer_id" => Yii::$app->user->id])
                ->orderBy(["is_closed" => SORT_ASC, "updated_at" => SORT_DESC]),
            'pagination' => [
                'pageSize' => '10',
            ]]);

        return $this->render('index', ['listDataProvider' => $dataProvider]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $loginForm = new LoginForm();
        if ($loginForm->load(Yii::$app->request->post())){
            try {
                if ($loginForm->login()) {
                    return Yii::$app->response->redirect(['site/index']);
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
                Yii::error($e);
            }

        }

        $loginForm->password = '';
        return $this->render('login', ['model' => $loginForm]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $signupForm = new SignupForm();
        if ($signupForm->load(Yii::$app->request->post()) && $signupForm->signup()) {
            Yii::$app->session->setFlash('success', 'Köszönjük a regisztrációt. Most már bejelentkezhetsz.');
            Yii::info('Sign-up was successful.');
            return $this->goHome();
        }

        return $this->render('signup', ['model' => $signupForm]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $passwordResetRequestForm = new PasswordResetRequestForm();
        if ($passwordResetRequestForm->load(Yii::$app->request->post()) && $passwordResetRequestForm->validate()) {
            if ($passwordResetRequestForm->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Kövesd a levélben kapott e-mail utasításait.');
                Yii::info('Password-reset email was successful.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
            Yii::error('Password-reset email was failed.');
        }

        return $this->render('requestPasswordResetToken', ['model' => $passwordResetRequestForm]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $resetPasswordForm = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::error($e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($resetPasswordForm->load(Yii::$app->request->post()) && $resetPasswordForm->validate() && $resetPasswordForm->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Új jelszó elmentve.');
            Yii::info('Password-reset was successful.');
            return $this->goHome();
        }

        return $this->render('resetPassword', ['model' => $resetPasswordForm]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $verifyEmailForm = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::error($e);
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $verifyEmailForm->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'E-mail cím megerősítve!');
            Yii::info('Email confirmation was successful.');
            return $this->goHome();
        }

        Yii::error('Email confirmation failed.');
        Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $resendVerificationEmailForm = new ResendVerificationEmailForm();
        if ($resendVerificationEmailForm ->load(Yii::$app->request->post()) && $resendVerificationEmailForm ->validate()) {
            if ($resendVerificationEmailForm ->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Kövesd a levélben kapott e-mail utasításait.');
                Yii::info('Password-reset resend email was successful.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
            Yii::error('Password-reset resend email failed.');
        }

        return $this->render('resendVerificationEmail', ['model' => $resendVerificationEmailForm ]);
    }

}
