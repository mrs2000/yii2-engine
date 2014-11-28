<?
namespace mrssoft\engine\behaviors;

use yii\helpers\Html;

/**
 * Требования к изображениям на основе правил модели
 * @property bool $multiple
 * @property string $demands
 */
class ImageDemands extends \yii\base\Behavior
{
    /** @var  \yii\base\Model */
    public $owner;

    private $_multiple = false;

    /**
     * Получить требования на основе правил валидации
     * @return string
     */
    public function getDemands()
    {
        $demands = $this->getList();
        if (empty($demands)) return '';
        return Html::tag('ul', implode('', $demands));
    }

    public function getList($attribute = null)
    {
        $demands = [];

        foreach ($this->owner->validators as $validator)
        {
            if ($validator instanceof \yii\validators\ImageValidator && (is_null($attribute) || in_array($attribute, $validator->attributes)))
            {
                if (!empty($validator->extensions))
                {
                    $demands[] = Html::tag('li', 'Доступные форматы: '.implode(', ',$validator->extensions));
                }
                if (!empty($validator->maxSize))
                {
                    $demands[] = Html::tag('li', 'Максимальный размер файла: '.\Yii::$app->formatter->asShortSize($validator->maxSize));
                }
                if (!empty($validator->maxFiles))
                {
                    $demands[] = Html::tag('li', 'Одновременно можно выбрать: '.$validator->maxFiles.' файлов');
                }
                if (!(empty($validator->minWidth) && empty($validator->minHeight)))
                {
                    $demands[] = Html::tag('li', 'Минимальные размеры изображения: '.$validator->minWidth.'x'.$validator->minHeight.'px');
                }
                if (!(empty($validator->maxWidth) && empty($validator->maxHeight)))
                {
                    $demands[] = Html::tag('li', 'Максимальные размеры изображения: '.$validator->minWidth.'x'.$validator->minHeight.'px');
                }

                $this->_multiple = $validator->maxFiles > 1;
            }
        }

        return $demands;
    }

    public function getMultiple()
    {
        return $this->_multiple;
    }
}