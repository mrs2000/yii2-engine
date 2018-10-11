<?php

namespace mrssoft\engine;

use yii;
use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;
use yii\web\YiiAsset;

class Asset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'css/bootstrap.min.css',
        'css/admin.css',
    ];

    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];

    public function init()
    {
        $this->js = [
            'js/admin.strings.' . Yii::$app->language . '.min.js',
            'js/admin.js',
        ];

        parent::init();
    }
}