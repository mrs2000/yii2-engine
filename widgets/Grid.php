<?php

namespace mrssoft\engine\widgets;

use mrssoft\engine\helpers\Admin;
use yii\widgets\Pjax;

/**
 * Таблица в админ. панели
 */
class Grid extends \yii\base\Widget
{
    /** @var \mrssoft\engine\ActiveRecord */
    public $model;

    public $filter = true;

    /** @var array */
    public $columns = [];

    public $addCommonRows = true;

    public $pjax = true;

    public function run()
    {
        //Добавить частоиспользуемые колонки
        if ($this->addCommonRows) {
            $startColumns = [
                Admin::columnSerial(),
                Admin::columnCheckbox()
            ];

            if (empty($this->columns) && $this->model->hasAttribute('title')) {
                $this->columns = [Admin::columnEdit()];
            }

            $endColumns = [];
            if (empty($this->columns['public']) && $this->model->hasAttribute('public')) {
                $endColumns[] = Admin::columnPublic();
            }

            if (empty($this->columns['position']) && $this->model->hasAttribute('position')) {
                $endColumns[] = Admin::columnPosition();
            }

            if ($this->model->hasAttribute('date') && !$this->hasColumn('date')) {
                $endColumns[] = Admin::columnDate();
            }

            if ($this->model->hasAttribute('id') && !$this->hasColumn('id')) {
                $endColumns[] = Admin::columnID();
            }

            $this->columns = array_merge($startColumns, $this->columns, $endColumns);
        }

        if ($this->pjax) {
            Pjax::begin(['linkSelector' => 'a[data-page], a[data-sort]', 'id' => 'pjax-container']);
        }

        echo \yii\grid\GridView::widget([
            'dataProvider' => $this->model->search(),
            'filterModel' => $this->filter === true ? $this->model : $this->filter,
            'columns' => $this->columns,
            'layout' => "{pager}\n{summary}\n{items}\n{pager}"
        ]);

        if ($this->pjax) {
            Pjax::end();
        }
    }

    private function hasColumn($atribute)
    {
        if (!empty($this->columns[$atribute])) {
            return true;
        }

        foreach ($this->columns as $column) {
            if (!empty($column['attribute']) && $column['attribute'] === $atribute) {
                return true;
            }
        }

        return false;
    }
}
