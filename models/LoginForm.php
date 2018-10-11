<?php

namespace mrssoft\engine\models;

use yii;
use app\models\User;
use yii\base\Model;

/**
 * @property \yii\web\IdentityInterface|\app\models\User|null $user
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    /**
     * @var bool User
     */
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
            'username' => Yii::t('admin/main', 'Login'),
            'password' => Yii::t('admin/main', 'Password'),
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('admin/main', 'The login or password you entered is incorrect.'));
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        return false;
    }

    /**
     * @return null|\yii\web\IdentityInterface|\app\models\User
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
            if ($this->_user->status != User::STATUS_ACTIVE) {
                return null;
            }
        }

        return $this->_user;
    }
}
