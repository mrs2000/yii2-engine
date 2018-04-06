<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */

use mrssoft\engine\Asset;
use mrssoft\engine\widgets\MessageWidget;
use yii\helpers\Html;

$this->title = Yii::t('admin/main', 'Control Panel');
Asset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="url-suffix" content="<?=Yii::$app->urlManager->suffix;?>" id="url-suffix">
    <?=Html::csrfMetaTags();?>
    <title><?=Html::encode($this->title);?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            if (Yii::$app->user->can('cp')) {
                echo $this->render('@app/modules/admin/views/layouts/menu');
            }
        ?>
        <div class="container">
            <?=MessageWidget::widget();?>
            <?=$content;?>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; <?=Yii::$app->controller->module->copyright;?>, 2014 &mdash; <?=date('Y');?></p>
        </div>
    </footer>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
