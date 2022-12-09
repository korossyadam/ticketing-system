<?php

namespace backend\controllers;

use common\models\User;

use backend\models\AdminUserForm;
use backend\models\UserForm;
use backend\models\DeleteUserForm;
use common\models\UserSearch;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
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
     * This function lists all Users based on the returned filters by adminUserForm
     * On first-load, runs a basic between-dates query
     */
    public function actionAdminUsers()
    {
        // Access is denied if a non-admin tries to view this page
        $currentUser = Yii::$app->user;
        if (!is_object($currentUser->identity) || (!$currentUser->identity->is_admin)) {
            return Yii::$app->response->redirect(['site/admin-login']);
        }

        $adminUserForm = new AdminUserForm();
        $searchModel = new UserSearch();

        // On button submit
        if ($adminUserForm->load(Yii::$app->request->post())) {

            // Returns an array of filters
            $formData = $adminUserForm->getData();

            // Extending date range by +- 1 day, to include same-day Tickets
            $date_start = date('Y-m-d', strtotime($formData['date_start'] . ' - 1 days'));
            $date_end = date('Y-m-d', strtotime($formData['date_end'] . ' + 1 days'));

            $query = User::find()
                ->where(["between", "created_at", $date_start, $date_end])
                ->orderBy(["username" => SORT_ASC]);

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

        return $this->render('admin-users', ['adminUserForm' => $adminUserForm, 'listDataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    /**
     * This function finds one User and renders its details
     * On form-submit, it attempts to change the User's data
     * @param int $id The ID of the User to be rendered
     * @return Response|string
     */
    public function actionUser(int $id)
    {
        // Access is denied if a non-admin tries to view this page
        $currentUser = Yii::$app->user;
        if (!is_object($currentUser->identity) || (!$currentUser->identity->is_admin)) {
            return Yii::$app->response->redirect(['site/admin-login']);
        }

        $userForm = new UserForm();
        $user = User::findOne(["id" => $id]);

        $tr = Yii::$app->db->beginTransaction();
        if ($userForm->load(Yii::$app->request->post()) && $userForm->validate()) {
            try {
                $userForm->changeProfile($id);
                $tr->commit();
                Yii::$app->session->setFlash('success', 'Sikeresen megváltoztattad a felhasználó adatait!');
                Yii::info('User update was successful.');
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
                Yii::error($e);
            }

            return $this->refresh();
        }

        return $this->render('user', ['userForm' => $userForm, 'user' => $user]);
    }

    /**
     * This function attempts to delete a User
     * @param int $id The ID of the User that we want to delete
     * @return Response
     */
    public function actionDeleteUser(int $id): Response
    {
        // Access is denied if a non-admin tries to view this page
        $currentUser = Yii::$app->user;
        if (!is_object($currentUser->identity) || (!$currentUser->identity->is_admin)) {
            return Yii::$app->response->redirect(['site/admin-login']);
        }

        $tr = Yii::$app->db->beginTransaction();
        $deleteUserForm = new DeleteUserForm();
        try {
            $deleteUserForm->deleteUser($id);
            $tr->commit();
            Yii::info('User delete was successful.');
        } catch (Exception $e) {
            $tr->rollBack();
            Yii::$app->session->setFlash('error', 'Belső hiba történt, kérjük próbáld újra később.');
            Yii::error($e);
        }

        return Yii::$app->response->redirect(['user/admin-users']);
    }

}
