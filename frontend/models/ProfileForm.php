<?php

namespace frontend\models;

use common\models\User;
use Exception;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ProfileForm extends Model
{
    public $username;
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => 'A felhasználónév nem lehet üres.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ez a felhasználónév már foglalt.',
                // 'when' ignores already-exists-conflict when taken username is current user's username
                'when' => function ($model) {
                    return $model->username != Yii::$app->user->identity->username;
                }],
            ['username', 'string', 'min' => 3, 'max' => 14, 'tooShort' => 'A felhasználónév minimum 3 karakterből kell álljon.', 'tooLong' => 'A felhasználónév maximum 14 karakterből állhat.'],

            ['email', 'trim'],
            ['email', 'required', 'message' => 'Az e-mail cím nem lehet üres.'],
            ['email', 'email', 'message' => 'Nem valós e-mail cím.'],
            ['email', 'string', 'min' => 3, 'max' => 40, 'tooShort' => 'Az e-mail cím minimum 3 karakterből kell álljon.', 'tooLong' => 'Az e-mail cím maximum 40 karakterből állhat.'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ez az e-mail cím már használatban van.',
                // 'when' ignores already-exists-conflict when taken email is current user's email
                'when' => function ($model) {
                    return $model->email != Yii::$app->user->identity->email;
                }],
        ];
    }

    /**
     * This function attempts to change the personal data of the currently logged in User
     * @return int The currently logged in User's ID
     * @throws Exception
     */
    public function changeProfile(): int
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->username = $this->username;
        $user->email = $this->email;

        // Set dates
        $user->updated_at = date('m/d/Y h:i:s a', time());

        // Attempting to update current User
        // save() also works as update() when record is already present in database
        if (!$user->save()) {
            throw new Exception('Failed to update user: ' . json_encode($user->attributes) . json_encode($user->getErrors()));
        } else {
            return $user->id;
        }

    }

}
