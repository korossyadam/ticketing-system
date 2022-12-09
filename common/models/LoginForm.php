<?php

namespace common\models;

use Exception;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'message' => 'Ez a mező nem lehet üres.'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, 'Ez a felhasználónév nem létezik.');
            } else if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Nem megfelelő jelszó.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     * @throws Exception
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            // Save Login time whenever we log in
            $user->last_login = date('m/d/Y h:i:s a', time());

            // Also attempt to update database record of user
            if (!$user->save()) {
                throw new Exception('Failed to update user: ' . json_encode($user->attributes) . json_encode($user->getErrors()));
            } else {
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
