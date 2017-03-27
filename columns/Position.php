<?php

namespace mrssoft\engine\columns;

use yii\grid\DataColumn;

class Position extends DataColumn
{
    public $attribute = 'position';

    public $headerOptions = ['class' => 'column-small'];

    public $contentOptions = ['class' => 'center column-small'];

    public $format = 'html';

    private $list;

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->list === null) {
            $max = $model->getMaxPosition();
            $this->list = [];
            for ($i = 1; $i <= $max; $i++) {
                $this->list[$i] = $i;
            }
        }

        return \yii\helpers\Html::dropDownList('position[' . $model->primaryKey . ']', $model->{$this->attribute}, $this->list, [
            'class' => 'position form-control', 'data-id' => $model->primaryKey
        ]);
    }
}
