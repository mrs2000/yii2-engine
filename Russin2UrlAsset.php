<?php

namespace mrssoft\engine;

use yii\web\AssetBundle;

class Russin2UrlAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mrssoft/yii2-engine/assets';

    public $js = [
        'js/russian2url.js',
    ];

    public function init()
    {
        $this->sourcePath = YII_DEBUG ? '@app/extensions/yii2-engine/assets' : '@vendor/mrssoft/yii2-engine/assets';
    }
}
