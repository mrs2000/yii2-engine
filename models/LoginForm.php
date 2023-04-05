<?php

namespace mrssoft\engine\models;

use app\models\User;
use yii;
use yii\base\Model;

/**
 * @property \yii\web\IdentityInterface|User|null $user
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    private User|bool|null $_user = false;

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
     * @return null|\yii\web\IdentityInterface|User
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
            if ($this->_user === null || $this->_user->status != User::STATUS_ACTIVE) {
                return null;
            }
        }

        return $this->_user;
    }
}
