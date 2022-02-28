<?php

namespace mrssoft\engine\columns;

use yii\grid\DataColumn;
use yii\helpers\Html;

class Dropdown extends DataColumn
{
    public $attribute = 'state';

    public $contentOptions = ['class' => 'center'];

    public array $dropdownOptions = [];

    public $format = 'html';

    public string $action = 'state';

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        if (!empty($this->dropdownOptions) && is_array(reset($this->dropdownOptions))) {
            $options = [];
            if (array_key_exists($model->{$this->attribute}, $this->dropdownOptions)) {
                $options = $this->dropdownOptions[$model->{$this->attribute}];
            }
        } else {
            $options = $this->dropdownOptions;
        }

        if (is_array($value)) {
            Html::addCssClass($options, 'state form-control');
            $options['data-action'] = $this->action;
            $options['data-id'] = $key;

            return Html::dropDownList($this->attribute, $model->{$this->attribute}, $this->getDataCellValue($model, $key, $index), $options);
        }

        return Html::tag('span', $value, $options);
    }
}
