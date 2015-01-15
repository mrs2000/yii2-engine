<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/mrssoft/yii2-engine/assets';

    public $css = [
        'css/admin.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapThemeAsset'
    ];

    public function init()
    {
        $this->js = [
            'js/admin.strings.'.\Yii::$app->language.'.min.js',
            'js/admin.js',
        ];
    }
}