<?php

namespace mrssoft\engine\columns;

use yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class Switcher extends DataColumn
{
    public $attribute = 'public';

    public $headerOptions = ['class' => 'column-small'];

    public $contentOptions = ['class' => 'center'];

    public $action = 'state';

    public function init()
    {
        if (empty($this->filter)) {
            $this->filter = [
                0 => Yii::t('admin/main', 'No'), 1 => Yii::t('admin/main', 'Yes')
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::checkbox($this->attribute, (bool)$model->{$this->attribute}, [
            'class' => 'state',
            'value' => (bool)$model->{$this->attribute} ? '0' : '1',
            'data-id' => $key,
            'data-action' => $this->action
        ]);
    }
}
