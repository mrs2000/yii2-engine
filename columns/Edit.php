<?php

namespace mrssoft\engine\columns;

use yii;
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
        /** @var \mrssoft\engine\Controller $controller */
        $controller = Yii::$app->controller;

        $url = [$controller->id . '/edit', 'id' => $model->{$this->attributeID}];
        $url = array_merge($url, $controller->urlParams);

        $title = $this->getDataCellValue($model, $key, $index);
        if ($title === '' || $title === null) {
            $title = '[&nbsp;' . Yii::t('admin/main', 'missing') . '&nbsp;]';
        }

        return Html::a($title, $url, ['title' => Yii::t('admin/main', 'Edit')]);
    }
}
