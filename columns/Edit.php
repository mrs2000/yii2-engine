<?php

namespace mrssoft\engine\columns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class Edit extends DataColumn
{
    public $attribute = 'title';

    public $attributeID = 'id';

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $url = array_merge([
            Yii::$app->controller->id . '/edit', 'id' => $model->{$this->attributeID}
        ], Yii::$app->controller->urlParams);

        $title = $this->getDataCellValue($model, $key, $index);
        if (empty($title)) {
            $title = '[ отсутствует ]';
        }

        return Html::a($title, $url, ['title' => Yii::t('admin/main', 'Edit')]);
    }
}
