<?php

namespace backend\models;

use common\models\User;
use Exception;
use Yii;
use yii\base\Model;

class DeleteUserForm extends Model
{

    /**
     * This function attempts to delete a User
     * @param int $userID The ID of the User we want to delete
     * @throws Exception
     */
    public function deleteUser(int $userID)
    {
        $user = User::findOne($userID);
        if (!$user->delete()) {
            throw new Exception('Failed to delete user: ' . json_encode($user->attributes) . json_encode($user->getErrors()));
        }
    }
}
