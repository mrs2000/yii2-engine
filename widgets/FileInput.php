<?

namespace mrssoft\engine\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class FileInput extends Widget
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;

    public $attribute;

    public $label = 'Выберите файл...';

    public function run()
    {
        $id = $this->getId();
        $file = Html::activeFileInput($this->model, $this->attribute);
        $selected = Html::tag('div', '', ['class' => 'selected']);
        echo Html::tag('div', $this->label.$file.$selected, ['class' => 'btn-file btn-block btn btn-primary', 'id' => $id]);

        $script = "$(document).on('change', '#$id input', function () {
            var count = this.files.length;
            $('#$id .selected').text(this.files[0].name);
         });";
        $this->view->registerJs($script, \yii\web\View::POS_END);
    }
}
