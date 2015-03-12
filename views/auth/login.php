<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */


?>
<h1><?=Yii::t('admin/main','Singin');?></h1>
<p><?=Yii::t('admin/main', 'Enter your login and password.');?></p>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal', 'autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n<div class=\"col-md-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-1 control-label'],
    ],
]); ?>

<?=$form->field($model, 'username')->textInput(['required' => 'on', 'autofocus' => 'on', 'autocomplete' => 'off']);?>
<?=$form->field($model, 'password')->passwordInput(['required' => 'on', 'autocomplete' => 'off']);?>

<div class="form-group">
    <div class="col-md-offset-1 col-md-11">
        <?= Html::submitButton(Yii::t('admin/main', 'Singin'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
