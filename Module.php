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
            'elfinder' => [
                'class' => 'mihaildev\elfinder\Controller',
                'access' => ['moderator'],
                'disabledCommands' => ['netmount', 'archive', 'extract', 'duplicate'],
                'roots' => [
                    [
                        'baseUrl' => '@web',
                        'basePath' => '@webroot',
                        'path' => 'content',
                        'name' => 'Корневая папка'
                    ]
                ],
                /*'uploadMaxSize' => '10M',
                'bind' => array(
                    'upload mkdir rename' => ['app\modules\admin\components\ElFinderExt', 'rename'],
                )*/
            ]
        ];

        Yii::$app->session->name = 'PHPSESSBACKID';
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
