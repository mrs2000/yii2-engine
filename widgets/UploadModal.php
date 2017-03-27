<?php

namespace mrssoft\engine\widgets;

use yii;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;

class UploadModal extends Widget
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;

    /**
     * @var string
     */
    public $attribute = 'image';

    /**
     * @var string Заголовок окна
     */
    public $title;

    public function run()
    {
        if (!$this->title) {
            $this->title = Yii::t('admin/main', 'Upload files');
        }

        $buttons = Html::submitButton(Yii::t('admin/main', 'Download'), ['class' => 'btn btn-primary btn-upload']) . Html::button(Yii::t('admin/main', 'Close'), [
                'class' => 'btn btn-default',
                'data-dismiss' => 'modal'
            ]);

        Modal::begin([
            'header' => Html::tag('h4', $this->title, ['class' => 'modal-title']),
            'options' => ['class' => 'upload-window'],
            'footer' => $buttons
        ]);

        $demands = $this->getDemands();
        if (!empty($demands)) {
            echo Html::tag('b', Yii::t('image-demands', 'Requirements for download:'));
            echo $demands;
        }

        $prop = 'multiple';
        $multiple = $this->model->hasProperty($prop) && $this->model->{$prop};

        echo FileInput::widget([
            'model' => $this->model,
            'attribute' => $this->attribute . '[]',
            'inputOptions' => ['multiple' => $multiple ? 'on' : 'off', 'required' => 'on']
        ]);

        Modal::end();

        \mrssoft\mrs2000box\Asset::register($this->view);
        $errImgPath = Yii::$app->assetManager->getPublishedUrl('@vendor/mrssoft/yii2-mrs2000box/assets') . '/err-img.gif';

        $script = "$(document).ready(function() {
            $('.btn-show-upload').click(function (e) {
                e.preventDefault();
                $('.upload-window').modal();
                return false;
            });

            $('.btn-upload').click(function () {
                change_action_and_submit('upload');
            });

            function init_mrs2000box() {
                if (typeof $.fn.mrs2000box === 'function') {
                    $('.mrs2000box').mrs2000box({err_img_path:'" . $errImgPath . "'});
                }
            }
            init_mrs2000box();
            $(document).on('pjax:complete', init_mrs2000box);
        });";

        $this->view->registerJs($script, View::POS_END);
    }

    /**
     * Получить требования на основе правил валидации
     * @return string
     */
    public function getDemands()
    {
        $method = 'getDemands';

        return $this->model->hasMethod($method) ? $this->model->{$method}() : '';
    }
}