<?php

namespace mrssoft\engine\widgets;

use mrssoft\engine\helpers\Admin;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\db\ActiveRecord;
use yii\widgets\Pjax;

/**
 * Таблица в админ. панели
 */
class Grid extends \yii\base\Widget
{
    /** @var \mrssoft\engine\ActiveRecord */
    public $model;

    /**
     * @var \mrssoft\engine\ActiveRecord
     */
    public $filter = true;

    /** @var array */
    public $columns = [];

    public $addCommonRows = true;

    public $pjax = true;

    public $enableSelectColumns = false;

    public $defaultHiddenColumns = [];

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
            if ($this->model->hasAttribute('date') && $this->hasColumn('date') === false) {
                $endColumns[] = Admin::columnDate();
            }
            if ($this->model->hasAttribute('id') && $this->hasColumn('id') === false) {
                $endColumns[] = Admin::columnID();
            }

            $this->columns = array_merge($startColumns, $this->columns, $endColumns);
        }

        if ($this->enableSelectColumns) {
            $this->renderConfigWindow($this->columns);
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

    private function hasColumn(string $atribute): bool
    {
        if (!empty($this->columns[$atribute])) {
            return true;
        }

        foreach ($this->columns as $column) {
            if (isset($column['attribute']) && $column['attribute'] === $atribute) {
                return true;
            }
        }

        return false;
    }

    private function renderConfigWindow(array $columns): void
    {
        $cookieName = 'egc-' . Yii::$app->controller->id;

        $cookie = Yii::$app->request->cookies[$cookieName];
        $hiddenColumns = $cookie->value ?? $this->defaultHiddenColumns;

        if (!empty($hiddenColumns)) {
            foreach ($this->columns as $index => &$c) {
                $attribute = (string)($c['attribute'] ?? $index);
                if (in_array($attribute, $hiddenColumns)) {
                    $c['visible'] = false;
                    $columns[$index]['visible'] = false;
                }
            }
            unset($c);
        }

        Modal::begin([
            'size' => Modal::SIZE_SMALL,
            'header' => 'Параметры таблицы',
            'toggleButton' => [
                'label' => Html::icon('cog'),
                'class' => 'btn btn-default pull-right',
                'style' => 'margin: 20px 0 5px',
                'title' => 'Параметры'
            ],
            'footer' => Html::button('Ok', ['class' => 'btn btn-primary action', 'data-action' => 'table-config'])
        ]);

        $model = $this->filter instanceof ActiveRecord ? $this->filter : $this->model;

        $list = [];
        $selected = [];
        foreach ($columns as $index => $column) {
            if (isset($column['attribute']) || isset($column['label'])) {
                if (empty($column['attribute'])) {
                    $column['attribute'] = $index;
                }
                if ($column['attribute'] === 'public') {
                    $label = Yii::t('admin/main', 'Public');
                } else {
                    $label = $column['label'] ?? $model->getAttributeLabel($column['attribute']);
                }
                if ($label) {
                    $list[$column['attribute']] = $label;
                    if (isset($column['visible']) === false || $column['visible']) {
                        $selected[] = $column['attribute'];
                    }
                    echo Html::hiddenInput('table-config[]', $column['attribute']);
                }
            }
        }

        echo Html::checkboxList('table-config-visible', $selected, $list);

        Modal::end();
    }
}
