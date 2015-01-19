<?php
namespace mrssoft\engine\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Форма редактирования в админ. манели
 */
class TableForm extends Widget
{
    var $title = '';
    var $buttons = [];
    var $params = [];
    var $formParams = [];

    public static function begin($config = [])
    {
        parent::begin($config);

        $controller = Yii::$app->controller->id;

        $config['formParams']['id'] = 'command-form';

        //Открытие формы
        echo Html::beginForm('/admin/' . $controller . '/index', 'post', $config['formParams']);

        echo Html::hiddenInput('controller', $controller, ['id' => 'controller']);

        if (is_array($config['params'])) {
            foreach ($config['params'] as $name => $value) {
                echo Html::hiddenInput($name, $value);
            }
        }

        echo Html::hiddenInput('urlParams', http_build_query(Yii::$app->controller->urlParams));

        //Заголовок и кнопки
        echo Header::widget([
            'title' => $config['title'],
            'buttons' => $config['buttons']
        ]);
    }

    public static function end()
    {
        //Закрытие формы
        echo Html::endForm();
        parent::end();
    }
}