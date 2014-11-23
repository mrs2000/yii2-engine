<?php
namespace mrssoft\engine;

use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * Форма редактирования в админ. манели
 * @var $model ActiveRecord
 */

class EditForm extends Widget
{
    public $title;
    public $model;
    public $buttons;
    public $enctype;

    /**
     * @param array $config
     * @return ActiveForm
     */
    public static function begin($config = [])
    {
        parent::begin($config);

        //Открытие формы
        $formConfig = [
            'action' => '/admin/'.Yii::$app->controller->id.'/index',
            'id' => 'command-form',
        ];
        if (isset($config['enctype']))
        {
            $formConfig['options']['enctype'] = $config['enctype'];
        }
        $formConfig['enableClientValidation'] = false;
        $form = ActiveForm::begin($formConfig);

        if (!empty($config['model']->id))
        {
            echo Html::hiddenInput('id', $config['model']->id);
        }

        echo Html::hiddenInput('urlParams', http_build_query(Yii::$app->controller->urlParams));

        //Заголовок и кнопки
        echo Header::widget([
            'title' => $config['title'],
            'buttons' => $config['buttons']
        ]);

        //Ошибки
        if (!is_array($config['model']) && !empty($config['model']))
        {
            //echo CAdmin::formatModelErrors($this->model, true);
            echo Html::errorSummary($config['model'], ['class' => 'alert alert-danger', 'header' => false]);
        }

        return $form;
    }

    public static function end()
    {
        ActiveForm::end();
        parent::end();
    }
}