<?php

namespace mrssoft\engine\controllers;

use mrssoft\engine\models\LoginForm;
use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

/**
 * Авторизация в ПУ
 */
class AuthController extends \yii\web\Controller
{
    /**
     * Вход
     * @return string|Response
     */
    public function actionLogin()
    {
        if (Yii::$app->user->can('cp')) {
            return $this->redirect(['/' . $this->module->id]);
        }

        $model = new LoginForm();
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/' . $this->module->id]);
        }

        $model->username = '';
        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Выход
     * @return Response
     * @throws MethodNotAllowedHttpException
     */
    public function actionLogout(): Response
    {
        if (Yii::$app->request->isPost === false) {
            throw new MethodNotAllowedHttpException();
        }
        if (Yii::$app->user->isGuest === false) {
            Yii::$app->user->logout();
        }

        return $this->redirect(['/' . $this->module->id]);
    }
}