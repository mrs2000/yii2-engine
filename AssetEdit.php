<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class AssetEdit extends AssetBundle
{
    public $js = [
        'js/jquery.typograf.js',
    ];

    public $depends = [
        'mrssoft\engine\Asset'
    ];

    public function init()
    {
        $this->sourcePath = YII_DEBUG ? '@app/extensions/yii2-engine/assets' : '@vendor/mrssoft/yii2-engine/assets';
    }
}