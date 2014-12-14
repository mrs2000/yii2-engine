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

    public $condition = null;

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $urlEdit = array_merge(
            [Yii::$app->controller->id.'/edit', 'id' => $model->{$this->attributeID}],
            Yii::$app->controller->urlParams
        );

        if (is_array($this->condition) && $model->{key($this->condition)} != reset($this->condition))
        {
            return Html::a($this->getDataCellValue($model, $key, $index), $urlEdit, ['title' => 'Редактировать']);
        }

        $urlParams = Yii::$app->controller->urlParams;
        $urlParams[$this->attributeParentID] = $model->id;
        $urlChildren = array_merge([Yii::$app->controller->id.'/index'], $urlParams);

        return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil', 'title' => 'Редактировать']), $urlEdit).'&nbsp;&nbsp;'.
        '[ '.Html::a($this->getDataCellValue($model, $key, $index), $urlChildren, ['title' => 'Открыть раздел']).' ]';
    }
}
