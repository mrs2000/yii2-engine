<?php

namespace mrssoft\engine;

use yii;
use yii\web\AssetBundle;
use yii\bootstrap\BootstrapThemeAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\YiiAsset;

class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/mrssoft/yii2-engine/assets';

    public $css = [
        'css/admin.css',
    ];

    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapThemeAsset::class
    ];

    public function init()
    {
        $this->js = [
            'js/admin.strings.' . Yii::$app->language . '.min.js',
            'js/admin.js',
        ];
    }
}