<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class AssetEdit extends AssetBundle
{
    public $sourcePath = '@vendor/mrssoft/yii2-engine/assets';

    public $js = [
        'js/jquery.typograf.js',
    ];

    public $depends = [
        Asset::class
    ];
}