<?php

namespace mrssoft\engine\controllers;

class AuthController extends \yii\web\Controller
{
    public function actionLogin()
    {
        $model = new \mrssoft\engine\models\LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login())
        {
            return $this->redirect('/admin');
        }

        $this->layout = null;

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        $this->redirect('/admin');
    }
}
