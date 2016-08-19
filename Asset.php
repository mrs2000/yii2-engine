<?php

namespace mrssoft\engine;

use yii;
use yii\web\AssetBundle;

class Asset extends AssetBundle
{
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
        $this->sourcePath = YII_DEBUG ? '@app/extensions/yii2-engine/assets' : '@vendor/mrssoft/yii2-engine/assets';

        $this->js = [
            'js/admin.strings.' . Yii::$app->language . '.min.js',
            'js/admin.js',
        ];
    }
}