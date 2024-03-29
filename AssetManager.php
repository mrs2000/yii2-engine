<?php

namespace mrssoft\engine;

class AssetManager extends \yii\web\AssetManager
{
    /**
     * Исключить файлы по маске
     * @var array
     */
    public array $exclude = [];

    /**
     * Добавлять время в параметр запроса
     * Иначе в первый сегмент URI
     * @var bool
     */
    public bool $queryParam = false;

    /**
     * Добавить атрибут deffer для всех javascript файлов
     * @var bool
     */
    public bool $enableJsDeffer = false;

    public function init()
    {
        parent::init();

        $this->exclude[] = '//';
    }

    /**
     * Добавить дату модификации в адрес файла
     * @param \yii\web\AssetBundle $bundle
     * @param string $asset
     * @param null $appendTimestamp
     * @return string
     */
    public function getAssetUrl($bundle, $asset, $appendTimestamp = null)
    {
        if ($this->enableJsDeffer) {
            $bundle->jsOptions['deffer'] = true;
        }

        $url = parent::getAssetUrl($bundle, $asset);

        foreach ($this->exclude as $exclude) {
            if (str_contains($url, $exclude)) {
                return $url;
            }
        }

        $time = filemtime('.' . $url);
        if ($this->queryParam) {
            return $url . '?v' . $time;
        }
        return '/' . $time . $url;
    }
}