<?php

namespace mrssoft\engine\widgets;

use Yii;

class Breadcrumbs extends \yii\base\Widget
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;

    /**
     * @var string
     */
    public $attributeParentID = 'parent_id';

    /**
     * @var string
     */
    public $attributeTitle = 'title';

    /**
     * @var string
     */
    public $route;

    public function run()
    {
        $parentId = (int)Yii::$app->request->get($this->attributeParentID);

        $route = $this->route ?: Yii::$app->controller->id . '/index';

        while (!empty($parentId)) {
            $item = $this->model::find()
                                ->select(['id', $this->attributeParentID, $this->attributeTitle])
                                ->where(['id' => $parentId])
                                ->one();

            if (empty($item)) {
                break;
            }

            $links[] = [
                'label' => $item->{$this->attributeTitle},
                'url' => [$route, $this->attributeParentID => $item->getPrimaryKey()]
            ];

            $parentId = $item->{$this->attributeParentID};
        }

        if (empty($links)) {
            return;
        }

        echo \yii\widgets\Breadcrumbs::widget([
            'links' => array_reverse($links),
            'homeLink' => [
                'label' => Yii::t('admin/main', 'Root'),
                'url' => [$route]
            ]
        ]);
    }
}