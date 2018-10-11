<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class AssetAuthManager extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'css/auth-manager-widget.css',
    ];

    public $js = [
        'js/auth-manager-widget.js',
    ];

    public $depends = [
        Asset::class,
    ];
}