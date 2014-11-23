<?
namespace mrssoft\engine;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\validators\ImageValidator;
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
    public $title = 'Загрузка изображений';

    /**
     * @var bool
     */
    private $multiple = false;

    public function run()
    {
        $buttons = Html::submitButton('Загрузить', ['class' => 'btn btn-primary btn-upload']).
            Html::button('Закрыть', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']);

        Modal::begin([
            'header' => Html::tag('h4', $this->title, ['class' => 'modal-title']),
            'options' => ['class' => 'upload-window'],
            'footer' => $buttons
        ]);

        $demands = $this->getDemands();
        if (!empty($demands))
        {
            echo Html::tag('b', 'Требования к загружаемым файлам:');
            echo $demands;
        }

        echo Html::activeFileInput(
            $this->model,
            $this->attribute.'[]',
            ['multiple' => $this->model->multiple ? 'on' : 'off', 'required' => 'on']
        );

        Modal::end();

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
                    $('.mrs2000box').mrs2000box();
                }
            }
            init_mrs2000box();
            $(document).on('pjax:complete', init_mrs2000box);
        });";

        $this->view->registerJs($script, View::POS_END);

        \app\assets\mrs2000boxAsset::register($this->view);
    }

    /**
     * Получить требования на основе правил валидации
     * @return string
     */
    public function getDemands()
    {
        return $this->model->getDemands();
    }
}