<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/mrssoft/yii2-engine/assets';

    public $css = [
        'css/admin.css',
    ];

    public $js = [
        'js/admin.strings.ru.min.js',
        'js/admin.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
