<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */

$this->title = 'Панель управления';
\mrssoft\engine\Asset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= \yii\helpers\Html::csrfMetaTags() ?>
    <title><?= \yii\helpers\Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php if (Yii::$app->user->can('cp')) echo $this->render('@app/modules/admin/views/layouts/menu'); ?>
        <div class="container">
            <?=\mrssoft\engine\widgets\MessageWidget::widget();?>
            <?=$content;?>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Компания «Группа «Новатор», <?=date('Y');?></p>
        </div>
    </footer>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>