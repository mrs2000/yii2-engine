<?php

namespace mrssoft\engine;

use yii;

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

        Yii::$app->i18n->translations['admin/main'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . DIRECTORY_SEPARATOR . 'messages',
            'fileMap' => [
                'admin/main' => 'main.php',
            ],
        ];

        Yii::$app->i18n->translations['admin/menu'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/admin/messages',
            'fileMap' => [
                'admin/menu' => 'menu.php',
            ],
        ];

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
                        'name' => Yii::t('admin/main', 'Root folder'),
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
        Yii::$app->user->loginUrl = '/' . $this->id . '/auth/login';
        Yii::$app->errorHandler->errorAction = '/' . $this->id . '/default/error';

        Yii::$app->viewPath = '@app/modules/' . $this->id . '/views';
        Yii::$app->layoutPath = __DIR__ . '/views/layouts';

        Yii::$app->urlManager->suffix = '';
        Yii::$app->urlManager->addRules([
            '/' . $this->id . '/<controller:\w+>' => '/' . $this->id . '/<controller>/index',
        ], false);
    }
}