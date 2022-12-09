<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => 'A felhasználónév nem lehet üres.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ez a felhasználónév már foglalt.'],
            ['username', 'string', 'min' => 3, 'max' => 14, 'tooShort' => 'A felhasználónév minimum 3 karakterből kell álljon.', 'tooLong' => 'A felhasználónév maximum 14 karakterből állhat.'],

            ['email', 'trim'],
            ['email', 'required', 'message' => 'Az e-mail cím nem lehet üres.'],
            ['email', 'email', 'message' => 'Nem valós e-mail cím.'],
            ['email', 'string', 'min' => 3, 'max' => 40, 'tooShort' => 'Az e-mail cím minimum 3 karakterből kell álljon.', 'tooLong' => 'Az e-mail cím maximum 40 karakterből állhat.'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ez az email-cím már használatban van.'],

            ['password', 'required', 'message' => 'A jelszó nem lehet üres.'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        // Create new User
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->is_admin = false;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->status = USER::STATUS_ACTIVE;

        // Set dates
        $user->last_login = date('m/d/Y h:i:s a', time());
        $user->created_at = date('m/d/Y h:i:s a', time());
        $user->updated_at = date('m/d/Y h:i:s a', time());

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
