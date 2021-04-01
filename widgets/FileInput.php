<?php

namespace mrssoft\engine\widgets;

use yii\bootstrap\InputWidget;
use yii\helpers\Html;

class FileInput extends InputWidget
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    public $label;

    /**
     * @var array
     */
    public $inputOptions = [];

    public function run()
    {
        if (empty($this->label)) {
            $this->label = array_key_exists('multiple', $this->inputOptions) && $this->inputOptions['multiple'] === 'on' ?
                'Select files...' : 'Select file...';

            $this->label = \Yii::t('admin/main', $this->label);
        }

        $id = $this->getId();
        if ($this->model) {
            $file = Html::activeFileInput($this->model, $this->attribute, $this->inputOptions);
        } else {
            $file = Html::fileInput($this->name, $this->value, $this->inputOptions);
        }

        $selected = Html::tag('div', '', ['class' => 'selected']);
        echo Html::tag('div', $this->label . $file, ['class' => 'btn-file btn-block btn btn-primary', 'id' => $id]);
        echo $selected;

        $script = "$('.btn-file').on('change', 'input', function () {
            var obj = $(this).parent();
            obj.addClass('active');
            var t = [];
            for (var i= 0; i < this.files.length; i++) {t.push(this.files[i].name);}
            obj.next('.selected').html(t.join(' &bullet; '));
        });";
        $this->view->registerJs($script, \yii\web\View::POS_END, 'file-input-js');
    }
}
