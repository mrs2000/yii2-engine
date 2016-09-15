<?php

namespace mrssoft\engine\controllers;

use mrssoft\engine\models\LoginForm;
use yii;
use yii\base\UserException;
use yii\web\MethodNotAllowedHttpException;

class AuthController extends \yii\web\Controller
{
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/' . $this->module->id);
        }

        $model->username = '';
        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        if (Yii::$app->request->isPost === false) {
            throw new MethodNotAllowedHttpException();
        }

        if (Yii::$app->user->isGuest) {
            throw new UserException('User not found.');
        }

        Yii::$app->user->logout();
        $this->redirect('/' . $this->module->id);
    }
}