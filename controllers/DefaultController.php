<?php

namespace mrssoft\engine\controllers;

use Yii;
use mrssoft\engine\AdminController;

class DefaultController extends AdminController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
