<?php

namespace mrssoft\engine;

use mihaildev\elfinder\Controller;
use Yii;
use yii\helpers\Url;
use yii\i18n\PhpMessageSource;
use yii\web\JqueryAsset;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public string $authController = controllers\AuthController::class;
    public string $defaultController = controllers\DefaultController::class;
    public string $filesController = controllers\FilesController::class;

    /**
     * ElFinder options
     */
    public int $elfinderMaxImageWidth = 800;
    public int $elfinderMaxImageHeight = 600;
    public string $elfinderUploadMaxSize = '5M';

    public string $copyright = 'MRSSOFT';

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

        Yii::$app->session->name = 'PHPSESSBACKID';
        Yii::$app->user->loginUrl = Url::toRoute('/' . $this->id . '/auth/login');
        Yii::$app->errorHandler->errorAction = '/' . $this->id . '/default/error';

        Yii::$app->viewPath = '@app/modules/' . $this->id . '/views';
        Yii::$app->layoutPath = __DIR__ . '/views/layouts';

        Yii::$app->urlManager->addRules([
            '/' . $this->id . '/<controller:\w+>' => '/' . $this->id . '/<controller>/index',
        ], false);

        Yii::$app->assetManager->bundles[JqueryAsset::class]['js'] = [
            'https://code.jquery.com/jquery-1.12.4.min.js'
        ];
    }
}