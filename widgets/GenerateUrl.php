<?
namespace mrssoft\engine\widgets;

use mrssoft\engine\Asset;
use mrssoft\engine\Russin2UrlAsset;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class GenerateUrl extends InputWidget
{
    public $buttonText = 'Сгенерировать';

    public $buttonOptions = ['class' => 'btn btn-primary'];

    public $source;

    public function run()
    {
        $id = $this->getId();

        if (empty($this->buttonOptions['class'])) $this->buttonOptions['class'] = '';
        $this->buttonOptions['class'] .= ' generate-url';

        $this->options['class'] = 'form-control';
        $this->options['id'] = $id;

        $button = Html::button($this->buttonText, $this->buttonOptions);
        $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
        $btnGroup = Html::tag('div', $button, ['class' => 'input-group-btn']);
        $group = Html::tag('div', $input.$btnGroup, ['class' => 'input-group']);

        echo Html::tag('div', $group, ['class' => 'form-group']);

        $js = "var generate = function() {
            $('#".$id."').val(russian2url($('".$this->source."').val()));
            return false;
        };
        $('.generate-url').click(generate);
        $('".$this->source."').blur(function () { if ($('#".$id."').val() == '') generate(); });";

        Russin2UrlAsset::register($this->view);
        $this->view->registerJs($js, \yii\web\View::POS_END);
    }
}
