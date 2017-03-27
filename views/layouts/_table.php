<?php
/**
 * @var \mrssoft\engine\ActiveRecord $model
 * @var $this \yii\web\View
 * @var string $title
 * @var array $buttons
 */

use mrssoft\engine\helpers\Admin;
use mrssoft\engine\widgets\TableForm;

TableForm::begin([
    'title' => $title,
    'buttons' => $buttons
]);

echo $this->render(
    Admin::getView('grid'),
    ['model' => $model],
    Yii::$app->controller
);

TableForm::end();