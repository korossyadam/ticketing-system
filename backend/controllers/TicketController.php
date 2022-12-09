<?php

namespace backend\controllers;

use backend\models\AssignToSelfForm;
use common\models\NewCommentForm;
use common\models\TicketSearch;
use common\models\Ticket;
use common\models\User;

use backend\models\AdminTicketForm;
use backend\models\DeleteCommentForm;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
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
                        'actions' => ['login', 'error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
        ];
    }

    /**
     * This function lists all Tickets based on the returned filters by adminTicketForm
     * On first-load, runs a basic between-dates query
     * @param string $id Only used when listing Tickets of a single User, otherwise needs to be set to ''
     */
    public function actionAdminTickets(string $id = '')
    {
        // Access is denied if a non-admin tries to view this page
        $currentUser = Yii::$app->user;
        if (!is_object($currentUser->identity) || (!$currentUser->identity->is_admin)) {
            return Yii::$app->response->redirect(['site/admin-login']);
        }

        $adminTicketForm = new AdminTicketForm();
        $searchModel = new TicketSearch();

        // On button submit
        if ($adminTicketForm->load(Yii::$app->request->post())) {

            // Returns an array of filters
            $formData = $adminTicketForm->getData();

            // Extending date range by +- 1 day, to include same-day Tickets
            $date_start = date('Y-m-d', strtotime($formData['date_start'] . ' - 1 days'));
            $date_end = date('Y-m-d', strtotime($formData['date_end'] . ' + 1 days'));

            $query = Ticket::find()
                ->where(["between", "created_at", $date_start, $date_end])
                ->orderBy(["is_closed" => SORT_ASC, "updated_at" => SORT_DESC]);
            $query->andFilterWhere(["=", "customer_id", $formData['user_id']]);

            // Ignore User ID in function parameter if form-based User ID filtering is used
            if ($formData['user_id'] !== '') {
                $id = '';
            }

            // Search in Comments
            if ($formData['comment_text'] !== '') {
                $query->joinWith("comments")->where(["ilike", "comment.text", $formData['comment_text']])->groupBy(["comment.created_at", "ticket.id"]);
            }

            // Creates Data Provider to be passed to view
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => '10',
                ]
            ]);

        } else if ($id !== '') {
            $query = Ticket::find()
                ->where(["=", "customer_id", $id])
                ->orderBy(["is_closed" => SORT_ASC, "updated_at" => SORT_DESC]);

            // Creates Data Provider to be passed to view
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => '10',
                ]
            ]);

        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->get());
        }

        // Gets the name of the User used to filter, if exists
        $username = '';
        if ($id !== '') {
            $username = User::findOne(["id" => $id])->username;
        }

        return $this->render('admin-tickets', ['adminTicketForm' => $adminTicketForm, 'listDataProvider' => $dataProvider, 'searchModel' => $searchModel, 'username' => $username]);
    }

    /**
     * Displays the Ticket page
     * Allows to create more Comments to the displayed Ticket, optionally with an uploaded file
     * @param string $id The ID of the Ticket to be displayed
     * @return Response|string
     */
    public function actionTicket(string $id)
    {
        // Access is denied if a non-admin tries to view this page
        $currentUser = Yii::$app->user;
        if (!is_object($currentUser->identity) || (!$currentUser->identity->is_admin)) {
            return Yii::$app->response->redirect(['site/admin-login']);
        }

        $ticket = Ticket::findOne(["id" => $id]);

        $commentForm = new NewCommentForm();
        $assignToSelfForm = new AssignToSelfForm();
        if ($commentForm->load(Yii::$app->request->post()) && $assignToSelfForm->load(Yii::$app->request->post()) && $commentForm->validate()) {

            // Attempt to create new Comment
            $tr = Yii::$app->db->beginTransaction();
            $commentForm->imageFile = UploadedFile::getInstance($commentForm, 'imageFile');
            try {
                $assignedTicket = $assignToSelfForm->assignToSelfIfNeeded($ticket);
                $commentForm->createNewComment($assignedTicket);
                $tr->commit();
                Yii::info('Comment creation was successful.');
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::error($e);
            }

            return $this->refresh();
        }

        return $this->render('ticket', ['commentForm' => $commentForm, 'assignToSelfForm' => $assignToSelfForm, 'ticket' => $ticket]);
    }

    /**
     * This function attempts to delete a Comment
     * @param int $ticketID The ID of the Ticket where we want to return to after delete
     * @param int $commentID The ID of the Comment that we want to delete
     * @return Response
     */
    public function actionDeleteComment(int $ticketID, int $commentID): Response
    {
        $tr = Yii::$app->db->beginTransaction();
        $deleteCommentForm = new DeleteCommentForm();
        try {
            $deleteCommentForm->deleteComment($ticketID, $commentID);
            $tr->commit();
            Yii::info('Comment delete was successful.');
        } catch (Exception $e) {
            $tr->rollBack();
            Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
            Yii::error($e);
        }

        return Yii::$app->response->redirect(['ticket/ticket', 'id' => $ticketID]);
    }

}
