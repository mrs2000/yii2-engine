<?php

namespace mrssoft\engine\behaviors;

use mrssoft\image\ImageHandler;
use yii;
use yii\base\Behavior;

/**
 * Поведение создаёт эскиз из первого изображения в указанном тексте
 *
 * @property null|string $thumbnail
 */
class MaterialThumb extends Behavior
{
    /**
     * Поле с исходным текстом
     */
    public string $attributeText = 'text';

    /**
     * Поле изображения
     */
    public string $attributeImage = 'image';

    /**
     * Папка для изображений
     */
    public string $path = '/img/news/';

    /**
     * Ширина эскиза
     */
    public int|bool $thumbWidth = 100;

    /**
     * Высота эскиза
     */
    public int|bool $thumbHeight = 100;

    public function events()
    {
        return [
            yii\db\BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            yii\db\BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            yii\db\BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave'
        ];
    }

    public function beforeSave()
    {
        $this->path = Yii::getAlias('@webroot/') . trim($this->path, '/') . '/';

        if (!empty($this->owner->{$this->attributeImage})) {
            $this->delete('.' . $this->path . $this->owner->{$this->attributeImage});
            $this->owner->{$this->attributeImage} = '';
        }

        $pattern = "#img.*src=[\"'](.*)[\"']#isU";
        preg_match_all($pattern, $this->owner->{$this->attributeText}, $matches);

        if (isset($matches[1][0])) {
            $src = '.' . $matches[1][0];
            if (!str_contains($src, 'http') && file_exists($src)) {
                $this->owner->{$this->attributeImage} = md5($src) . '.jpg';

                $ih = new ImageHandler();
                $ih->load($src);
                if ($this->thumbWidth === false || $this->thumbHeight === false) {
                    $ih->resize($this->thumbWidth, $this->thumbHeight);
                } else {
                    $ih->adaptiveThumb($this->thumbWidth, $this->thumbHeight);
                }
                $ih->save($this->path . $this->owner->{$this->attributeImage}, false, 100);
            }
        }

        return true;
    }

    public function afterDelete()
    {
        $this->delete('.' . $this->path . $this->owner->{$this->attributeImage});
    }

    private function delete($path)
    {
        if (@is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Путь к эскизу
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        $image = $this->owner->{$this->attributeImage};
        if (empty($image)) {
            return null;
        }

        return $this->path . $image;
    }
}