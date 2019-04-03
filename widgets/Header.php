<?php

namespace mrssoft\engine\widgets;

use mrssoft\engine\Controller;
use yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Вывод шапки
 */
class Header extends Widget
{
    /**
     * @var string Заголовок страницы
     */
    public $title = '';

    /**
     * @var array Кнопки управления
     */
    public $buttons = [];

    public function run()
    {
        $title = Html::tag('div', Html::tag('h2', $this->title), ['class' => 'col-md-6']);
        $buttons = Html::tag('div', $this->createButtons($this->buttons), ['class' => 'col-md-6']);
        $row =  Html::tag('div', $title . $buttons, ['class' => 'row']);

        return Html::tag('div', $row, ['class' => 'header-line']);
    }

    /**
     * Создать кнопки
     * @param array|string $list
     * @return string
     * @throws \yii\base\Exception
     */
    private function createButtons(array $list): string
    {
        $out = '';

        foreach ((array)$list as $button) {
            if (is_array($button)) {
                $out .= $this->createButton($button);
            } else {

                /** @var Controller $controller */
                $controller = Yii::$app->controller;

                switch ($button) {
                    case 'add':
                        $button = [
                            'title' => Yii::t('admin/main', 'Create'),
                            'class' => 'btn-success',
                            'icon' => 'glyphicon-plus glyphicon-white',
                            'href' => $controller->createUrl('edit')
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'cancel':
                        $button = [
                            'title' => Yii::t('admin/main', 'Close'),
                            'class' => 'btn-default',
                            'icon' => ' glyphicon-off glyphicon-white',
                            'href' => $controller->createUrl('index')
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'copy':
                        $button = [
                            'action' => $button,
                            'title' => Yii::t('admin/main', 'Copy'),
                            'class' => 'btn-success',
                            'need_items' => true,
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'delete':
                        $button = [
                            'action' => $button,
                            'title' => Yii::t('admin/main', 'Delete'),
                            'class' => 'btn-danger',
                            'need_items' => true,
                            'icon' => 'glyphicon-trash glyphicon-white',
                            'confirm' => Yii::t('admin/main', 'Are you sure you want to remove the selected items?')
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'public':
                        $button = [
                            'action' => 'state',
                            'search' => 'attribute=public&value=1',
                            'title' => Yii::t('admin/main', 'On'),
                            'need_items' => true,
                            'no-group' => true
                        ];
                        $b1 = $this->createButton($button);
                        $button = [
                            'action' => 'state',
                            'search' => 'attribute=public&value=0',
                            'title' => Yii::t('admin/main', 'Off'),
                            'need_items' => true,
                            'no-group' => true
                        ];
                        $b2 = $this->createButton($button);
                        $out .= Html::tag('div', $b1 . $b2, ['class' => 'btn-group']);
                        break;
                    case 'save':
                        $button = [
                            'action' => 'update',
                            'title' => Yii::t('admin/main', 'Save'),
                            'class' => 'btn-success',
                            'icon' => 'glyphicon-ok glyphicon-white',
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'apply':
                        $button = [
                            'action' => 'apply',
                            'title' => Yii::t('admin/main', 'Apply'),
                            'class' => 'btn-primary',
                            'icon' => 'glyphicon-ok glyphicon-white',
                        ];
                        $out .= $this->createButton($button);
                        break;
                    case 'upload':
                        $button = [
                            'title' => Yii::t('admin/main', 'Download'),
                            'class' => 'btn-primary btn-show-upload',
                            'icon' => 'glyphicon-arrow-down glyphicon-white',
                        ];
                        $out .= $this->createButton($button);
                        break;
                }
            }
        }

        return Html::tag('div', $out, ['class' => 'btn-toolbar pull-right']) . Html::tag('div', '', ['class' => 'clearfix']) . Html::hiddenInput('action', Yii::$app->controller->action->id);
    }

    /**
     * Создать коммандную кнопку
     * @param $params
     * @return string
     * @throws Exception
     */
    private function createButton(array $params): string
    {
        if (!isset($params['title'])) {
            throw new Exception('Unknown header command button.');
        }

        if (!isset($params['class'])) {
            $params['class'] = 'btn-default';
        }
        $icon = isset($params['icon']) ? '<i class="glyphicon ' . $params['icon'] . '"></i> ' : '';

        $options['class'] = 'action btn ' . $params['class'];
        if (!empty($params['action'])) {
            $options['data-action'] = $params['action'];
        }
        if (!empty($params['search'])) {
            $options['data-search'] = $params['search'];
        }
        if (!empty($params['need_items'])) {
            $options['data-need-items'] = '1';
        }
        if (!empty($params['confirm'])) {
            $options['data-confirm'] = $params['confirm'];
        }
        if (!empty($params['id'])) {
            $options['id'] = $params['id'];
        }

        if (empty($params['href'])) {
            $btn = Html::button($icon . $params['title'], $options);
        } else {
            $btn = Html::a($icon . $params['title'], $params['href'], $options);
        }

        if (empty($params['no-group'])) {
            return Html::tag('div', $btn, ['class' => 'btn-group']);
        }

        return $btn;
    }
}