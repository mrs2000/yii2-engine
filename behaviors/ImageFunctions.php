<?php

namespace mrssoft\engine\behaviors;

use yii;
use yii\base\Behavior;
use mrssoft\engine\ActiveRecord;
use mrssoft\image\ImageHandler;

/**
 * Поведение для работы с изображениеями модели
 *
 * @property int $imageWidth
 * @property string $image
 * @property string $thumb
 * @property string $imagePath
 * @property int $imageThumbHeight
 * @property int $imageHeight
 * @property int $imageThumbWidth
 */
class ImageFunctions extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'image';

    /**
     * @var int
     */
    public $width = 800;

    /**
     * @var int
     */
    public $height = 600;

    /**
     * @var int
     */
    public $thumbWidth;

    /**
     * @var int
     */
    public $thumbHeight;

    /**
     * @var int
     */
    public $thumbQuality = 100;

    /**
     * @var int
     */
    public $quality = 100;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $thumbSuffix = '_thumb';

    /**
     * @var int
     */
    public $nameLenght = 6;

    /**
     * @var bool
     */
    public $enableWebp = false;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $baseUrl;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_COPY => 'copy'
        ];
    }

    /**
     * Полный путь к изображению
     * @return string
     */
    public function getImage(): ?string
    {
        if ($this->owner->{$this->attribute}) {
            $this->initPath();
            return $this->baseUrl . $this->owner->{$this->attribute};
        }
        return null;
    }

    /**
     * Полный путь к эскизу изображения
     * @return string
     */
    public function getThumb(): ?string
    {
        if ($this->owner->{$this->attribute}) {
            $this->initPath();
            return self::thumbPath($this->getImage(), $this->thumbSuffix);
        }
        return null;
    }

    /**
     * Ширина изображения
     * @return int
     */
    public function getImageWidth(): int
    {
        return $this->width;
    }

    /**
     * Высота изображения
     * @return int
     */
    public function getImageHeight(): int
    {
        return $this->height;
    }

    /**
     * Ширина эскиза
     * @return int
     */
    public function getImageThumbWidth(): ?int
    {
        return $this->thumbWidth;
    }

    /**
     * Высота эскиза
     * @return int
     */
    public function getImageThumbHeight(): ?int
    {
        return $this->thumbHeight;
    }

    /**
     * Качество сохранения
     * @return int
     */
    public function getQuality(): int
    {
        return $this->quality;
    }

    /**
     * Качество сохранения эскиза
     * @return int
     */
    public function getThumbQuality(): int
    {
        return $this->thumbQuality;
    }

    /**
     * Необходимые размеры изображения
     * @return string
     */
    public function needSize(): string
    {
        if ($this->width && $this->height) {
            return $this->width . 'x' . $this->height . 'px';
        }

        if ($this->width) {
            return Yii::t('admin/main', 'width') . ': ' . $this->width . ' px';
        }

        if ($this->height) {
            return Yii::t('admin/main', 'height') . ': ' . $this->height . ' px';
        }
        return '';
    }

    private function getImagePath(): ?string
    {
        if ($this->owner->{$this->attribute}) {
            $this->initPath();
            return $this->baseUrl . $this->owner->{$this->attribute};
        }
        return null;
    }

    private function initPath(): void
    {
        if ($this->baseUrl === null) {

            $path = rtrim($this->path, '/') . '/';

            if (mb_strpos($path, '@') === 0) {
                $n = mb_strpos($path, '/');
                if ($n) {
                    $path = mb_substr($path, $n);
                }
            }

            if (preg_match_all('#{(.*)}#U', $path, $matches)) {
                foreach ($matches[1] as $i => $param) {
                    $path = str_replace($matches[0][$i], $this->owner->{$param}, $path);
                }
            }

            $this->baseUrl = Yii::getAlias('@web') . $path;
            $this->basePath = Yii::getAlias('@webroot') . $path;
        }
    }

    /**
     * Копирование модели
     */
    public function copy(): void
    {
        $this->initPath();

        $path = $this->basePath;
        $copyName = $this->createFilename($path, $this->owner->{$this->attribute});
        @copy($path . $this->owner->{$this->attribute}, $path . $copyName);

        if ($this->thumbWidth || $this->thumbHeight) {
            @copy($path . self::thumbPath($this->owner->{$this->attribute}, $this->thumbSuffix), $path . self::thumbPath($copyName, $this->thumbSuffix));
        }

        $this->owner->{$this->attribute} = $copyName;
    }

    /**
     * Обработка удаления модели
     */
    public function afterDelete(): void
    {
        $this->deleteImages();
    }

    /**
     * Удалить связанные с моделью изображения
     */
    public function deleteImages(): void
    {
        $path = '.' . $this->getImage();
        if (is_file($path)) {
            @unlink($path);
        }

        $path = self::thumbPath($path, $this->thumbSuffix);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Сформировать путь к эскизу
     * @param string|null $filename - имя исходного файла
     * @param string $suffix - суффикс эскиза
     * @return string
     */
    public static function thumbPath(?string $filename, string $suffix = '_thumb'): ?string
    {
        if ($filename) {
            $n = strrpos($filename, '.');
            if ($n === false) {
                return $filename . $suffix;
            }

            return substr_replace($filename, $suffix, $n, 0);
        }

        return null;
    }

    /**
     * Создать уникальное имя файла
     * @param string $path - путь к файлу
     * @param string $filename - базовое имя
     * @return string
     */
    private function createFilename(string $path, string $filename): string
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        do {
            $name = mb_strtolower(substr(md5(uniqid(mt_rand(), true)), 0, $this->nameLenght)) . '.' . $ext;
        } while (is_file($path . $name));

        return $name;
    }

    /**
     * Создать эскиз изображения
     * @param null|\mrssoft\image\ImageHandler $imageHandler
     * @param bool $adaptive
     * @param bool $proportional
     * @return \mrssoft\image\ImageHandler
     * @throws \yii\base\Exception
     */
    public function createThumb(ImageHandler $imageHandler = null, bool $adaptive = true, bool $proportional = true): ImageHandler
    {
        if ($imageHandler === null) {
            $imageHandler = new ImageHandler();
            $imageHandler->load('.' . $this->getImage());
        }

        if ($this->thumbWidth !== null && $this->thumbHeight !== null) {
            if ($adaptive) {
                $imageHandler->adaptiveThumb($this->thumbWidth, $this->thumbHeight);
            } else {
                $imageHandler->thumb($this->thumbWidth, $this->thumbHeight, $proportional);
            }

            $imageHandler->save('.' . $this->getThumb(), false, $this->thumbQuality);
        }

        return $imageHandler;
    }

    /**
     * Изменение размера изображения
     * @param null|\mrssoft\image\ImageHandler $imageHandler
     * @param bool $proportional
     * @return \mrssoft\image\ImageHandler
     * @throws \yii\base\Exception
     */
    public function resize(ImageHandler $imageHandler = null, bool $proportional = true): ImageHandler
    {
        if ($imageHandler === null) {
            $imageHandler = new ImageHandler();
            $imageHandler->load($this->getImagePath());
        }

        if ($imageHandler->getWidth() != $this->width || $imageHandler->getHeight() != $this->height) {
            $imageHandler->resize($this->width, $this->height, $proportional)
                         ->save(false, false, $this->quality);
        }

        return $imageHandler;
    }
}