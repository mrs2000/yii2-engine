<?php

namespace mrssoft\engine\controllers;

use Yii;
use app\modules\admin\models\LoginForm;
use yii\web\Controller;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
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
        Yii::$app->user->logout();
        $this->redirect('/admin');
    }
}
