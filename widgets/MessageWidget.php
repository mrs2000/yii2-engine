<?php

namespace mrssoft\engine\widgets;

use yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Вывод сообщений
 *
 * @property mixed $closeButton
 */
class MessageWidget extends Widget
{
    public $title = '';
    public $buttons = [];

    private function getCloseButton(): string
    {
        return Html::button('&times;', ['class' => 'close', 'data-dismiss' => 'alert']);
    }

    public function run()
    {
        if ($msgSuccess = Yii::$app->session->getFlash('msg-success')) {
            echo Html::tag('div', $this->getCloseButton() . $msgSuccess, ['class' => 'alert alert-success']);
        }

        if ($msgError = Yii::$app->session->getFlash('msg-error')) {
            echo Html::tag('div', $this->getCloseButton() . $msgError, ['class' => 'alert alert-danger']);
        }
    }
}
