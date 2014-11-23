<?php

namespace mrssoft\engine;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class SwitchColumn extends DataColumn
{
    public $attribute = 'public';

    public $action = 'changepublic';

    public $headerOptions = ['class' => 'column-small'];

    public $contentOptions = ['class' => 'center'];

    public $format = 'text';

    public $filter = [
        0 => 'Нет',
        1 => 'Да'
    ];

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ((bool)$model->{$this->attribute} === true)
        {
            $state = 'off';
            $title = 'Выключить';
        }
        else
        {
            $state = 'on';
            $title = 'Включить';
        }

        return Html::tag('span', '&nbsp;', [
            'title' => $title,
            'class' => 'changestate',
            'data-action' => $this->action,
            'data-id' => $model->primaryKey,
            'data-state' => $state
        ]);
    }
}
