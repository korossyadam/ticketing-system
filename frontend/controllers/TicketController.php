<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Ticket;
use common\models\NewCommentForm;
use frontend\models\NewTicketForm;
use yii\web\Response;
use yii\web\UploadedFile;

class TicketController extends Controller
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
     * Displays the new Ticket page.
     * @return Response|string
     */
    public function actionNewTicket()
    {
        // Access is denied if user is not logged in
        $user = Yii::$app->user;
        if (!is_object($user->identity)) {
            return Yii::$app->response->redirect(['site/index']);
        }

        $ticketForm = new NewTicketForm();
        $commentForm = new NewCommentForm();
        if ($ticketForm->load(Yii::$app->request->post()) && $commentForm->load(Yii::$app->request->post()) && $ticketForm->validate() && $commentForm->validate()) {

            // Attempt to create new Ticket
            $tr = Yii::$app->db->beginTransaction();
            $commentForm->imageFile = UploadedFile::getInstance($commentForm, 'imageFile');
            try {
                $newTicket = $ticketForm->createNewTicket();
                $commentForm->createNewComment($newTicket);
                $tr->commit();
                Yii::$app->session->setFlash('success', 'Köszönjük, hogy felkerestél minket. A lehető leghamarabb válaszolni fogunk.');
                Yii::info('Ticket submit was successful.');
            } catch (\Exception $e) {
                $tr->rollBack();
                Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
                Yii::error($e);
            }

            return $this->refresh();
        }

        return $this->render('newTicket', ['ticketForm' => $ticketForm, 'commentForm' => $commentForm]);
    }

    /**
     * Displays the Ticket page
     * Allows to create more Comments to the displayed Ticket, optionally with an uploaded file
     * @param int $id The ID of the Ticket to be displayed
     * @return Response|string
     */
    public function actionTicket(int $id)
    {
        /*
        $ticket = Ticket::findOne(["id" => $id]);

        // Access is denied if user is not logged in, or a non-admin user tries to view another user's ticket
        $user = Yii::$app->user;
        if (!is_object($user->identity) || (!$user->identity->is_admin && $user->identity->id !== $ticket->customer_id)) {
            return Yii::$app->response->redirect(['site/index']);
        }
        */

        // Access is denied if user is not logged in, or a non-admin user tries to view another user's ticket
        $user = Yii::$app->user;
        if (!is_object($user->identity)) {
            return Yii::$app->response->redirect(['site/index']);
        }

        $ticket = Ticket::find()->where(["id" => $id, "customer_id" => $user->identity->id])->one();
        if ($ticket === null) {
            return Yii::$app->response->redirect(['site/index']);
        }

        $commentForm = new NewCommentForm();
        if ($commentForm->load(Yii::$app->request->post()) && $commentForm->validate()) {

            // Attempt to create new Comment
            $tr = Yii::$app->db->beginTransaction();
            $commentForm->imageFile = UploadedFile::getInstance($commentForm, 'imageFile');
            try {
                $commentForm->createNewComment($ticket);
                $tr->commit();
                Yii::info('Comment creation was successful.');
            } catch (\Exception $e) {
                $tr->rollBack();
                Yii::error($e);
            }

            return $this->refresh();
        }

        return $this->render('ticket', ['commentForm' => $commentForm, 'ticket' => $ticket]);
    }

}
