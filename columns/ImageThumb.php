<?php

namespace mrssoft\engine\columns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class ImageThumb extends DataColumn
{
    public $attribute = 'image';

    public $contentOptions = ['class' => 'center'];

    public $format = 'html';

    public $filter = false;

    public $enablePreview = true;

    public function init()
    {
        if (empty($this->header)) {
            $this->header = Yii::t('admin/main', 'Image');
        }
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $img = Html::img($model->getThumb());

        if ($this->enablePreview) {
            return Html::a($img, $model->getImage(), ['class' => 'mrs2000box']);
        } else {
            return $img;
        }
    }
}
