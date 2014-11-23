<?php

namespace mrssoft\engine;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class EditColumn extends DataColumn
{
    public $attribute = 'title';

    public $attributeID = 'id';

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $url = array_merge(
            [ Yii::$app->controller->id.'/edit', 'id' => $model->{$this->attributeID}],
            Yii::$app->controller->urlParams
        );

        return Html::a($model->{$this->attribute}, $url);
    }
}
