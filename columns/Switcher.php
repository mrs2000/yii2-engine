<?php

namespace mrssoft\engine\columns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class Switcher extends DataColumn
{
    public $attribute = 'public';

    public $headerOptions = ['class' => 'column-small'];

    public $contentOptions = ['class' => 'center'];

    public $filter = [
        0 => 'Нет',
        1 => 'Да'
    ];

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::checkbox(
            $this->attribute,
            (bool)$model->{$this->attribute},
            [
                'class' => 'state',
                'value' => (bool)$model->{$this->attribute} ? '0' : '1',
                'data-id' => $key
            ]
        );
    }
}
