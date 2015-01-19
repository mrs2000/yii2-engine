<?
namespace mrssoft\engine\behaviors;

use yii\base\Behavior;

/**
 * Поведение для работы с изображениеями модели
 */
class ImageFunctions extends Behavior
{
    public $attribute = 'image';

    public $width = 800;

    public $height = 600;

    public $thumbWidth = null;

    public $thumbHeight = null;

    public $thumbQuality = 100;

    public $quality = 100;

    public $path = '@web/img/';

    public $thumbSuffix = '_thumb';

    public $nameLenght = 6;

    private $_path = null;

    public function events()
    {
        return [
            \mrssoft\engine\ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            \mrssoft\engine\ActiveRecord::EVENT_COPY => 'copy'
        ];
    }

    /**
     * Полный путь к изображению
     * @return string
     */
    public function getImage()
    {
        return $this->getImagePath() . $this->owner->{$this->attribute};
    }

    /**
     * Полный путь к эскизу изображения
     * @return string
     */
    public function getThumb()
    {
        return self::thumbPath($this->getImage(), $this->thumbSuffix);
    }

    /**
     * Ширина изображения
     * @return int
     */
    public function getImageWidth()
    {
        return $this->width;
    }

    /**
     * Высота изображения
     * @return int
     */
    public function getImageHeight()
    {
        return $this->height;
    }

    /**
     * Ширина эскиза
     * @return int
     */
    public function getImageThumbWidth()
    {
        return $this->thumbWidth;
    }

    /**
     * Высота эскиза
     * @return int
     */
    public function getImageThumbHeight()
    {
        return $this->thumbHeight;
    }

    /**
     * Качество сохранения
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Качество сохранения эскиза
     * @return int
     */
    public function getThumbQuality()
    {
        return $this->thumbQuality;
    }

    /**
     * Необходимые размеры изображения
     * @return string
     */
    public function needSize()
    {
        return $this->width . 'x' . $this->height . 'px';
    }

    /**
     * Путь к изображению
     * @return string
     */
    public function getImagePath()
    {
        if (is_null($this->_path)) {
            $this->_path = rtrim($this->path, '/') . '/';

            if (preg_match_all('#{(.*)}#Ui', $this->_path, $matches)) {
                foreach ($matches[1] as $i => $param) {
                    $this->_path = str_replace($matches[0][$i], $this->owner->{$param}, $this->_path);
                }
            }
        }

        return \Yii::getAlias($this->_path);
    }

    /**
     * Копирование модели
     */
    public function copy()
    {
        $path = '.' . $this->getImagePath();
        $copyName = $this->createFilename($path, $this->owner->{$this->attribute});
        copy($path . $this->owner->{$this->attribute}, $path . $copyName);

        if (!($this->thumbHeight === null || $this->thumbHeight == null)) {
            copy($path . self::thumbPath($this->owner->{$this->attribute}, $this->thumbSuffix), $path . self::thumbPath($copyName, $this->thumbSuffix));
        }

        $this->owner->{$this->attribute} = $copyName;
    }

    /**
     * Обработка удаления модели
     */
    public function afterDelete()
    {
        $this->deleteImages();
    }

    /**
     * Удалить связанные с моделью изображения
     */
    public function deleteImages()
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
     * @param string $filename - имя исходного файла
     * @param string $suffix - суффикс эскиза
     * @return string
     */
    public static function thumbPath($filename, $suffix = '_thumb')
    {
        $n = strripos($filename, '.');
        if ($n === false) {
            return $filename . $suffix;
        } else {
            return substr($filename, 0, $n) . $suffix . substr($filename, $n);
        }
    }

    /**
     * Создать уникальное имя файла
     * @param string $path - путь к файлу
     * @param string $filename - базовое имя
     * @return string
     */
    private function createFilename($path, $filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        do {
            $name = substr(mb_strtolower(md5(uniqid())), 0, $this->nameLenght) . '.' . $ext;
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
    public function createThumb($imageHandler = null, $adaptive = true, $proportional = true)
    {
        if ($imageHandler === null) {
            $imageHandler = new \mrssoft\image\ImageHandler();
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
     * @param null $imageHandler
     * @param bool $proportional
     * @return \mrssoft\image\ImageHandler
     * @throws \yii\base\Exception
     */
    public function resize($imageHandler = null, $proportional = true)
    {
        if ($imageHandler === null) {
            $imageHandler = new \mrssoft\image\ImageHandler();
            $imageHandler->load('.' . $this->getImage());
        }

        if ($imageHandler->getWidth() != $this->width || $imageHandler->getHeight() != $this->height) {
            $imageHandler->resize($this->width, $this->height, $proportional)->save(false, false, $this->quality);
        }

        return $imageHandler;
    }
}