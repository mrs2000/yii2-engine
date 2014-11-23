<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */


?>
<h1>Вход</h1>
<p>Введите имя пользователя и пароль.</p>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n<div class=\"col-md-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-1 control-label'],
    ],
]); ?>

<?=$form->field($model, 'username')->textInput(['required' => 'on', 'autofocus' => 'on']);?>
<?=$form->field($model, 'password')->passwordInput(['required' => 'on']);?>

<div class="form-group">
    <div class="col-md-offset-1 col-md-11">
        <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
