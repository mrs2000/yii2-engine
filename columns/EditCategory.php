<?php

namespace mrssoft\engine\columns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class EditCategory extends DataColumn
{
    public $attribute = 'title';

    public $attributeID = 'id';

    public $attributeParentID = 'parent_id';

    public $condition;

    /**
     * Enable open subcategories
     * @var bool|callable
     */
    public $enableOpen = true;

    protected function renderDataCellContent($model, $key, $index)
    {
        $urlEdit = array_merge([
            Yii::$app->controller->id . '/edit', 'id' => $model->{$this->attributeID}
        ], Yii::$app->controller->urlParams);

        if (is_array($this->condition) && $model->{key($this->condition)} != reset($this->condition)) {
            return Html::a($this->getDataCellValue($model, $key, $index), $urlEdit, ['title' => Yii::t('admin/main', 'Edit')]);
        }

        $urlParams = Yii::$app->controller->urlParams;
        $urlParams[$this->attributeParentID] = $model->id;
        $urlChildren = array_merge([Yii::$app->controller->id . '/index'], $urlParams);

        $enableOpen = is_callable($this->enableOpen) ?
            call_user_func($this->enableOpen, $model, $key, $index) :
            (bool)$this->enableOpen;

        $text = $enableOpen ?
            Html::a($this->getDataCellValue($model, $key, $index), $urlChildren, ['title' => Yii::t('admin/main', 'Open')]) :
            $this->getDataCellValue($model, $key, $index);

        return Html::a(Html::tag('span', '', [
            'class' => 'glyphicon glyphicon-pencil', 'title' => Yii::t('admin/main', 'Edit')
        ]), $urlEdit) . '&nbsp;&nbsp;' . '[ ' . $text . ' ]';
    }
}
