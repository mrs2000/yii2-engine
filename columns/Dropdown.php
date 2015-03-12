<?php

namespace mrssoft\engine\columns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class Dropdown extends DataColumn
{
    public $attribute = 'state';

    public $contentOptions = ['class' => 'center'];

    public $dropdownOptions = [];

    public $format = 'html';

    public $action = 'state';

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        if (is_array($this->dropdownOptions) && !empty($this->dropdownOptions) && is_array(reset($this->dropdownOptions))) {
            if (isset($this->dropdownOptions[$model->{$this->attribute}])) {
                $options = $this->dropdownOptions[$model->{$this->attribute}];
            } else {
                $options = [];
            }
        } else {
            $options = $this->dropdownOptions;
        }

        if (is_array($value)) {
            Html::addCssClass($options, 'state form-control');
            $options['data-action'] = $this->action;
            $options['data-id'] = $key;

            return \yii\helpers\Html::dropDownList($this->attribute, $model->{$this->attribute}, $this->getDataCellValue($model, $key, $index), $options);
        }

        return Html::tag('span', $value, $options);
    }
}
