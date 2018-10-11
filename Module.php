<?php

namespace mrssoft\engine;

use Yii;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Url;
use mihaildev\elfinder\Controller;
use yii\i18n\PhpMessageSource;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public $authController = controllers\AuthController::class;
    public $defaultController = controllers\DefaultController::class;
    public $filesController = controllers\FilesController::class;

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
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . DIRECTORY_SEPARATOR . 'messages',
            'fileMap' => [
                'admin/main' => 'main.php',
            ],
        ];

        Yii::$app->i18n->translations['admin/menu'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => Yii::getAlias('@app') . '/modules/' . $this->id . '/messages',
            'fileMap' => [
                'admin/menu' => 'menu.php',
            ],
        ];

        $this->controllerMap = [
            'auth' => $this->authController,
            'default' => $this->defaultController,
            'files' => $this->filesController,
            'elfinder' => [
                'class' => Controller::class,
                'access' => ['moderator'],
                'commands' => [
                    'back',
                    'copy',
                    'cut',
                    'getfile',
                    'info',
                    'mkdir',
                    'paste',
                    'rename',
                    'rm',
                    'search',
                    'sort',
                    'upload',
                    'up',
                ],
                'roots' => [
                    [
                        'baseUrl' => '@web',
                        'basePath' => '@webroot',
                        'path' => 'content',
                        'name' => 'Content',
                        'uploadMaxSize' => $this->elfinderUploadMaxSize,
                    ]
                ],
                'bind' => [
                    'upload mkdir rename' => [
                        'class' => ElFinderExt::class,
                        'action' => 'change',
                        'imageMaxWidth' => $this->elfinderMaxImageWidth,
                        'imageMaxHeight' => $this->elfinderMaxImageHeight,
                    ],
                ]
            ]
        ];

        Yii::$app->assetManager->bundles[BootstrapAsset::class]['css'] = ['css/bootstrap.min.css'];

        Yii::$app->session->name = 'PHPSESSBACKID';
        Yii::$app->user->loginUrl = Url::toRoute('/' . $this->id . '/auth/login');
        Yii::$app->errorHandler->errorAction = '/' . $this->id . '/default/error';

        Yii::$app->viewPath = '@app/modules/' . $this->id . '/views';
        Yii::$app->layoutPath = __DIR__ . '/views/layouts';

        Yii::$app->urlManager->addRules([
            '/' . $this->id . '/<controller:\w+>' => '/' . $this->id . '/<controller>/index',
        ], false);
    }
}