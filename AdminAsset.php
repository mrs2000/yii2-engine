<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mrssoft/yii2-engine/assets';

    public $baseUrl = '@web';

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
