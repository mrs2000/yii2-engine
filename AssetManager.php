<?php

namespace mrssoft\engine;

class AssetManager extends \yii\web\AssetManager
{
    /**
     * Исключить
     * @var array
     */
    public $exlude = [];

    /**
     * Добавить дату модификации в адрес файлу
     * @param \yii\web\AssetBundle $bundle
     * @param string $asset
     * @return string
     */
    public function getAssetUrl($bundle, $asset)
    {
        $url = parent::getAssetUrl($bundle, $asset);

        $this->exlude[] = '//';

        foreach ($this->exlude as $exclude) {
            if ((mb_strpos($url, $exclude) !== false)) {
                return $url;
            }
        }

        return '/' . filemtime('.' . $url) . $url;
    }
}