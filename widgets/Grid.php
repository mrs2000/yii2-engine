<?
namespace mrssoft\engine\widgets;
use mrssoft\engine\helpers\AdminHelper;

/**
 *
 */
class Grid extends \yii\base\Widget
{
    /** @var \mrssoft\engine\ActiveRecord */
    public $model;

    /** @var array */
    public $columns;

    public $addCommonRows = true;

    public function run()
    {
        if ($this->addCommonRows)
        {
            $startColumns = [
                AdminHelper::columnSerial(),
                AdminHelper::columnCheckbox()
            ];

            if (empty($this->columns))
            {
                $this->columns = [AdminHelper::columnEdit()];
            }

            $endColumns = [];
            if ($this->model->hasAttribute('public')) $endColumns[] = AdminHelper::columnPublic();
            if ($this->model->hasAttribute('position')) $endColumns[] = AdminHelper::columnPosition();
            if ($this->model->hasAttribute('date')) $endColumns[] = AdminHelper::columnDate();
            $endColumns[] = AdminHelper::columnID();

            $this->columns = array_merge($startColumns, $this->columns, $endColumns);
        }

        \yii\widgets\Pjax::begin([
            'linkSelector' => 'a[data-page], a[data-sort]'
        ]);
        echo \yii\grid\GridView::widget([
            'dataProvider' => $this->model->search(),
            'filterModel' => $this->model,
            'columns' => $this->columns,
            'layout' => "{pager}\n{summary}\n{items}\n{pager}"
        ]);
        \yii\widgets\Pjax::end();
    }
}
