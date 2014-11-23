<?php

namespace mrssoft\engine;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class ImageThumbColumn extends DataColumn
{
    public $attribute = 'image';

    public $header = 'Изображение';

    public $contentOptions = ['class' => 'center'];

    public $format = 'html';

    public $filter = false;

    public $enablePreview = true;

    /**
     * @inheritdoc
     */
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
