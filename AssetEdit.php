<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class AssetEdit extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'js/jquery.typograf.js',
    ];

    public $depends = [
        Asset::class
    ];
}