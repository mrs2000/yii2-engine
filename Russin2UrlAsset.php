<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class Russin2UrlAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'js/russian2url.js',
    ];
}
