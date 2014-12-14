<?php

namespace mrssoft\engine;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public $authController = '\mrssoft\engine\controllers\AuthController';
    public $defaultController = '\mrssoft\engine\controllers\DefaultController';
    public $filesController = '\mrssoft\engine\controllers\FilesController';

    /**
     * ElFinder options
     */
    public $elfinderMaxImageWidth = 800;
    public $elfinderMaxImageHeight = 600;
    public $elfinderUploadMaxSize = '5M';

    public $copyright = 'MRSSOFT';

    public function init()
    {
        parent::init();

        $this->controllerMap = [
            'auth' => $this->authController,
            'default' => $this->defaultController,
            'files' => $this->filesController,
            'elfinder' => [
                'class' => 'mihaildev\elfinder\Controller',
                'access' => ['moderator'],
                'disabledCommands' => ['netmount', 'archive', 'extract', 'duplicate'],
                'roots' => [
                    [
                        'baseUrl' => '@web',
                        'basePath' => '@webroot',
                        'path' => 'content',
                        'name' => 'Корневая папка',
                        'uploadMaxSize' => $this->elfinderUploadMaxSize,
                    ]
                ],
                'bind' => [
                    'upload mkdir rename' => [
                        'class' => '\mrssoft\engine\ElFinderExt',
                        'action' => 'change',
                        'imageMaxWidth' => $this->elfinderMaxImageWidth,
                        'imageMaxHeight' => $this->elfinderMaxImageHeight,
                    ],
                ]
            ]
        ];

        Yii::$app->session->name = 'PHPSESSBACKID';
        Yii::$app->user->loginUrl = '/admin/auth/login';
        Yii::$app->errorHandler->errorAction = 'admin/default/error';

        Yii::$app->viewPath = '@app/modules/admin/views';
        Yii::$app->layoutPath =  dirname(__FILE__) . '/views/layouts';

        Yii::$app->urlManager->suffix = '';
        Yii::$app->urlManager->addRules([
            'admin/<controller:\w+>' => 'admin/<controller>/index',
        ], false);
    }
}
