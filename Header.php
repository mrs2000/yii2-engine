<?php
namespace mrssoft\engine;

use Yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Вывод шапки
 */
class Header extends Widget
{
    /**
     * @var string Заголовок страницы
     */
    var $title = '';

    /**
     * @var array Кнопки управления
     */
    var $buttons = [];

    public function run()
    {
        $content = Html::tag('div', Html::tag('h1', $this->title), ['class' => 'col-md-6']).
            Html::tag('div', $this->createButtons($this->buttons), ['class' => 'col-md-6']);
        echo Html::tag('div', $content, ['class' => 'row']);
    }

    /**
     * Создать кнопки
     * @param array $list
     * @return string
     */
    private function createButtons($list = [])
    {
        $out = '';

        if (!is_array($list)) $list = [$list];

        foreach ($list as $button)
        {
            if (is_array($button))
            {
                $out .= $this->createButton($button);
            }
            else
            {
                switch($button)
                {
                    case 'add':
                        $button = [
                            'title' => 'Создать',
                            'class' => 'btn-success',
                            'icon' => 'glyphicon-plus glyphicon-white',
                            'href' => Yii::$app->controller->createUrl('edit')
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'cancel':
                        $button = [
                            'title' => 'Закрыть',
                            'class' => 'btn-danger',
                            'icon' => ' glyphicon-off glyphicon-white',
                            'href' => Yii::$app->controller->createUrl('index')
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'copy':
                        $button = [
                            'action' => $button,
                            'title' => 'Копия',
                            'class' => 'btn-success',
                            'need_items' => true,
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'delete':
                        $button = [
                            'action' => $button,
                            'title' => 'Удалить',
                            'class' => 'btn-danger',
                            'need_items' => true,
                            'icon' => 'glyphicon-trash glyphicon-white',
                            'confirm' => 'Вы действительно хотите удалить выделенные элементы?'
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'public':
                        $button = [
                            'action' => 'changepublic?state=on',
                            'title' => 'Вкл',
                            'need_items' => true,
                        ];
                        $out .= $this->createButton($button);
                        $button = array(
                            'action' => 'changepublic?state=off',
                            'title' => 'Выкл',
                            'need_items' => true,
                        );
                        $out .= $this->createButton($button);
                        break;
                    case 'save':
                        $button = [
                            'action' => 'update',
                            'title' => 'Сохранить',
                            'class' => 'btn-success',
                            'icon' => 'glyphicon-ok glyphicon-white',
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'apply':
                        $button = [
                            'action' => 'apply',
                            'title' => 'Применить',
                            'class' => 'btn-primary',
                            'icon' => 'glyphicon-ok glyphicon-white',
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'upload':
                        $button = [
                             'title' => 'Загрузить',
                            'class' => 'btn-primary btn-show-upload',
                            'icon' => 'glyphicon-arrow-down glyphicon-white',
                        ];
                        $out .= $this->createButton($button);
                        break;
                }
            }
        }

        return Html::tag('div', $out, ['class' => 'btn-toolbar pull-right']).
            Html::tag('div', '', ['class' => 'clearfix']).
            Html::hiddenInput('action', Yii::$app->controller->action->id);
    }

    /**
     * Создать коммандную кнопку
     * @param $params
     * @return string
     * @throws Exception
     */
    private function createButton($params)
    {
        if (!isset($params['title'])) throw new Exception('Не указан заголовок командной кнопки.');

        if (!isset($params['class'])) $params['class'] = 'btn-default';
        $icon = isset($params['icon']) ? '<i class="glyphicon '.$params['icon'].'"></i> ' : '';

        $options['class'] = 'action btn '.$params['class'];
        if (!empty($params['action'])) $options['data-action'] = $params['action'];
        if (!empty($params['need_items'])) $options['data-need-items'] = '1';
        if (!empty($params['confirm'])) $options['data-confirm'] = $params['confirm'];
        if (!empty($params['id'])) $options['id'] = $params['id'];

        if (empty($params['href']))
        {
            $params['href'] = '#';
        }

        $btn = Html::a($icon.$params['title'], $params['href'], $options);

        return Html::tag('div', $btn, ['class' => 'btn-group']);
    }
}