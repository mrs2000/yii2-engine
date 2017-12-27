<?php

namespace mrssoft\engine\controllers;

use yii\web\ErrorAction;

class DefaultController extends \mrssoft\engine\Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        /** @noinspection MissedViewInspection */
        return $this->render('index');
    }
}
