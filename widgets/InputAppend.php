<?php
namespace mrssoft\engine\widgets;

use yii\helpers\Html;
use yii\widgets\InputWidget;

class InputAppend extends InputWidget
{
    public $buttonText;

    public $buttonOptions = [];

    public $source;

    public function run()
    {
        $this->options['class'] = 'form-control';

        if (empty($this->buttonOptions['class'])) {
            $this->buttonOptions['class'] = 'btn btn-primary';
        }

        $button = Html::button($this->buttonText, $this->buttonOptions);
        $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
        $btnGroup = Html::tag('div', $button, ['class' => 'input-group-btn']);
        $group = Html::tag('div', $input . $btnGroup, ['class' => 'input-group']);

        echo Html::tag('div', $group, ['class' => 'form-group']);
    }
}
