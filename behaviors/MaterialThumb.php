<?
namespace mrssoft\engine\behaviors;

use mrssoft\image\ImageHandler;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Поведение создаёт эскиз из первого изображения в указанном тексте
 */
class MaterialThumb extends Behavior
{
    /**
     * Поле с исходным текстом
     * @var string
     */
    public $attributeText = 'text';

    /**
     * Поле изображения
     * @var string
     */
    public $attributeImage = 'image';

    /**
     * Папка для изображений
     * @var string
     */
    public $path = '/img/news/';

    /**
     * Ширина эскиза
     * @var int|bool
     */
    public $thumbWidth = 100;

    /**
     * Высота эскиза
     * @var int|bool
     */
    public $thumbHeight = 100;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave'
        ];
    }

    public function beforeSave()
    {
        $this->path = \Yii::getAlias('@webroot/') . trim($this->path, '/') . '/';

        if (!empty($this->owner->{$this->attributeImage})) {
            $this->delete('.' . $this->path . $this->owner->{$this->attributeImage});
            $this->owner->{$this->attributeImage} = '';
        }

        $pattern = "#img.*src=(?:\"|')(.*)(?:\"|')#isU";
        preg_match_all($pattern, $this->owner->{$this->attributeText}, $matches);

        if (isset($matches[1][0])) {
            $src = '.' . $matches[1][0];
            if (mb_strpos($src, 'http') === false && file_exists($src)) {
                $this->owner->{$this->attributeImage} = md5($src) . '.jpg';

                $ih = new ImageHandler();
                $ih->load($src)
                   ->resize($this->thumbWidth, $this->thumbHeight)
                   ->save($this->path . $this->owner->{$this->attributeImage}, false, 100);
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
    public function getThumbnail()
    {
        $image = $this->owner->{$this->attributeImage};
        if (empty($image)) {
            return null;
        }

        return $this->path . $image;
    }
}