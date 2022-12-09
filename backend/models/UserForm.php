<?php

namespace backend\models;

use common\models\User;
use Exception;
use Yii;
use yii\base\Model;

class UserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $admin;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],

            ['username', 'trim'],
            ['username', 'required', 'message' => 'A felhasználónév nem lehet üres.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ez a felhasználónév már foglalt.',
                // 'when' ignores already-exists-conflict when taken username is current user's username
                'when' => function ($model) {
                    return $model->username != User::findOne(["id" => $this->id])->username;
                }],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required', 'message' => 'Az e-mail cím nem lehet üres.'],
            ['email', 'email', 'message' => 'Nem valós e-mail cím.'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ez az e-mail cím már használatban van.',
                // 'when' ignores already-exists-conflict when taken email is current user's email
                'when' => function ($model) {
                    return $model->email != User::findOne(["id" => $this->id])->email;
                }],

            ['admin', 'default', 'value' => function ($model) {
                return User::findOne($this->id)->is_admin;
            }]
        ];
    }

    /**
     * This function attempts to change a User's personal data
     * @param int $userID The ID of the User that we want to change
     * @return int The ID of the saved User
     * @throws Exception
     */
    public function changeProfile(int $userID): int
    {
        $user = User::findOne($userID);
        $user->username = $this->username;
        $user->email = $this->email;
        $user->is_admin = $this->admin;

        // Set dates
        $user->updated_at = date('m/d/Y h:i:s a', time());

        // Attempt to update current User
        // save() also works as update() when record is already present in database
        if (!$user->save()) {
            throw new Exception('Failed to update user: ' . json_encode($user->attributes) . json_encode($user->getErrors()));
        } else {
            return $user->id;
        }

    }

}
