<?php

namespace mrssoft\engine\helpers;

use yii;
use yii\db\ActiveRecord;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use mrssoft\engine\columns\Edit;
use mrssoft\engine\columns\EditCategory;
use mrssoft\engine\columns\Position;
use mrssoft\engine\columns\Switcher;

class Admin
{
    /**
     * Вывод сообщения об успешном выполнении операции
     * @param string $message
     */
    public static function success($message)
    {
        Yii::$app->session->setFlash('msg-success', $message);
    }

    /**
     * Вывод сообщения об ошибке
     * @param string $message
     */
    public static function error($message)
    {
        if ($message instanceof ActiveRecord) {
            $message = self::formatModelErrors($message);
        }
        Yii::$app->session->setFlash('msg-error', $message);
    }

    public function getMessage()
    {
        Yii::$app->session->getFlash('msg-error');
    }

    /**
     * @param yii\db\ActiveRecord $model
     * @param bool $applyAlert
     * @return string
     */
    public static function formatModelErrors($model, $applyAlert = false)
    {
        $errors = $model->getErrors();
        $result = '';
        if (!empty($errors)) {
            if ($applyAlert) {
                $result .= '<div class="alert alert-error">';
            }

            $result .= '<ul style="margin-bottom: 0;">';
            foreach ($errors as $error) {
                $result .= '<li>' . $error[0] . '</li>';
            }
            $result .= '</ul>';
            if ($applyAlert) {
                $result .= '</div>';
            }
        }

        return $result;
    }

    /**
     * Колонка с ссылкой на редактирвование записи
     * @param string $attribute
     * @param string $attributeID
     * @return array
     */
    public static function columnEdit($attribute = 'title', $attributeID = 'id')
    {
        return [
            'class' => Edit::class,
            'attribute' => $attribute,
            'attributeID' => $attributeID
        ];
    }

    /**
     * Колонка с ссылками на редактирвование записи и переходя к дочерним элементам
     * @param string $attribute
     * @param string $attributeID
     * @param string $attributeParentID
     * @return array
     */
    public static function columnEditCategory($attribute = 'title', $attributeID = 'id', $attributeParentID = 'parent_id')
    {
        return [
            'class' => EditCategory::class,
            'attribute' => $attribute,
            'attributeID' => $attributeID,
            'attributeParentID' => $attributeParentID
        ];
    }

    /**
     * Колонка с порядковым номером
     * @return array
     */
    public static function columnSerial()
    {
        return [
            'class' => SerialColumn::class,
            'header' => '№',
            'headerOptions' => ['class' => 'column-small'],
            'contentOptions' => ['class' => 'center column-small'],
        ];
    }

    /**
     * Колонка с чекбоксом
     * @return array
     */
    public static function columnCheckbox()
    {
        return [
            'class' => CheckboxColumn::class,
            'checkboxOptions' => ['class' => 'select-on-check'],
            'contentOptions' => ['class' => 'center'],
            'headerOptions' => ['class' => 'center column-small'],
        ];
    }

    /**
     * Колонка с ID записи
     * @param string $attribute
     * @return array
     */
    public static function columnID($attribute = 'id')
    {
        return [
            'attribute' => $attribute,
            'contentOptions' => ['class' => 'center'],
            'headerOptions' => ['class' => 'center column-small'],
        ];
    }

    /**
     * Колонка "Опубликовано"
     * @return array
     */
    public static function columnPublic()
    {
        return [
            'class' => Switcher::class,
            'attribute' => 'public',
            'encodeLabel' => false,
            'headerOptions' => ['class' => 'text-center'],
            'label' => yii\bootstrap\Html::icon('ok-sign', ['title' => 'Опубликовано']),
        ];
    }

    /**
     * Колонка с датой
     * @param string $attribute
     * @return array
     */
    public static function columnDate($attribute = 'date')
    {
        return [
            'attribute' => $attribute,
            'format' => 'date',
            'filter' => false,
            'contentOptions' => ['class' => 'center'],
        ];
    }

    /**
     * Колонка с датой и временем
     * @param string $attribute
     * @return array
     */
    public static function columnDateTime($attribute = 'date')
    {
        return [
            'attribute' => $attribute,
            'format' => 'datetime',
            'filter' => false,
            'contentOptions' => ['class' => 'center'],
        ];
    }

    /**
     * Описание колонки "Позиция" для таблицы
     * @param string $attribute
     * @return array
     */
    public static function columnPosition($attribute = 'position')
    {
        return [
            'class' => Position::class,
            'attribute' => $attribute
        ];
    }

    /**
     * Путь к отображению
     * Вначале ищет файл в @app/modules/admin/views/
     * Если файла нет возвращает /layouts/_xxxxx
     * @param $view
     * @return string
     */
    public static function getView($view)
    {
        if (strpos($view, '//') === 0) {
            return $view;
        }

        $alias = '@app/modules/' . Yii::$app->controller->module->id . '/views/' . Yii::$app->controller->id . '/' . $view;
        $pathTableView = Yii::getAlias($alias . '.php');
        return is_file($pathTableView) ? $alias : '/layouts/_' . $view;
    }
}