<?php

namespace mrssoft\engine;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public function init()
    {
        parent::init();

        $this->controllerMap = [
            'auth' => '\mrssoft\engine\controllers\AuthController',
            'default' => '\mrssoft\engine\controllers\DefaultController',
        ];

        Yii::$app->user->loginUrl = '/admin/auth/login';
        Yii::$app->urlManager->suffix = '';
        Yii::$app->errorHandler->errorAction = 'admin/default/error';

        Yii::$app->viewPath = '@app/modules/admin/views';
        Yii::$app->layoutPath =  dirname(__FILE__) . '/views/layouts';



        Yii::$app->urlManager->addRules([
            'admin/<controller:\w+>' => 'admin/<controller>/index',
        ], false);
    }
}
