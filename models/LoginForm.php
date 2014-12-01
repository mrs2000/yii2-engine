<?php

namespace mrssoft\engine\models;

use Yii;
use app\models\User;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors())
        {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password))
            {
                $this->addError($attribute, 'Неверное имя пользователя или пароль.');
            }
        }
    }

    public function login()
    {
        if ($this->validate())
        {
            return Yii::$app->user->login($this->getUser());
        }
        else
        {
            return false;
        }
    }

    /**
     * @return null|\yii\web\IdentityInterface|\app\models\User
     */
    public function getUser()
    {
        if ($this->_user === false)
        {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
