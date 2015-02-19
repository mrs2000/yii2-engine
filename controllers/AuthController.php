<?php

namespace mrssoft\engine\controllers;

use mrssoft\engine\models\LoginForm;

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
        \Yii::$app->user->logout();
        $this->redirect('/' . $this->module->id);
    }
}