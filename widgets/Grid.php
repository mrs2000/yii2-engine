<?
namespace mrssoft\engine\widgets;
use mrssoft\engine\helpers\Admin;

/**
 *
 */
class Grid extends \yii\base\Widget
{
    /** @var \mrssoft\engine\ActiveRecord */
    public $model;

    /** @var array */
    public $columns = [];

    public $addCommonRows = true;

    public function run()
    {
        if ($this->addCommonRows)
        {
            $startColumns = [
                Admin::columnSerial(),
                Admin::columnCheckbox()
            ];

            if (empty($this->columns) && $this->model->hasAttribute('title'))
            {
                $this->columns = [Admin::columnEdit()];
            }

            $endColumns = [];
            if (empty($this->columns['public']) && $this->model->hasAttribute('public'))
                $endColumns[] = Admin::columnPublic();

            if (empty($this->columns['position']) && $this->model->hasAttribute('position'))
                $endColumns[] = Admin::columnPosition();

            if ($this->model->hasAttribute('date') && !$this->hasColumn('date'))
                $endColumns[] = Admin::columnDate();

            $endColumns[] = Admin::columnID();

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

    private function hasColumn($atribute)
    {
        if (!empty($this->columns[$atribute]))
            return true;

        foreach ($this->columns as $column)
        {
            if (!empty($column['attribute']) && $column['attribute'] == $atribute) {
                return true;
            }
        }

        return false;
    }

}
