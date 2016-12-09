<?
namespace mrssoft\engine\behaviors;

use yii;
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

    public function init()
    {
        Yii::$app->i18n->translations['image-demands'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .   'messages',
            'fileMap' => [
                'image-demands' => 'image-demands.php',
            ],
        ];
    }

    /**
     * Получить требования на основе правил валидации
     * @return string
     */
    public function getDemands()
    {
        $demands = $this->getList();
        if (empty($demands)) {
            return '';
        }

        return Html::tag('ul', implode('', $demands));
    }

    public function getList($attribute = null)
    {
        $demands = [];

        foreach ($this->owner->validators as $validator) {
            if ($validator instanceof \yii\validators\ImageValidator && ($attribute === null || in_array($attribute, $validator->attributes))) {
                if (!empty($validator->extensions)) {
                    $demands[] = Html::tag('li', Yii::t('image-demands', 'Available formats: ') . implode(', ', $validator->extensions));
                }
                if (!empty($validator->maxSize)) {
                    $demands[] = Html::tag('li', Yii::t('image-demands', 'Maximum file size: ') . \Yii::$app->formatter->asShortSize($validator->maxSize));
                }
                if (!empty($validator->maxFiles)) {
                    $demands[] = Html::tag('li', Yii::t('image-demands', 'At the same time, you can choose {0} files', $validator->maxFiles));
                }
                if (!(empty($validator->minWidth) && empty($validator->minHeight))) {
                    $demands[] = Html::tag('li', Yii::t('image-demands', 'Minimum image size: ') . $validator->minWidth . 'x' . $validator->minHeight . 'px');
                }
                if (!(empty($validator->maxWidth) && empty($validator->maxHeight))) {
                    $demands[] = Html::tag('li', Yii::t('image-demands', 'Maximum image size: ') . $validator->minWidth . 'x' . $validator->minHeight . 'px');
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