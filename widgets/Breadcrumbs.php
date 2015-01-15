<?

namespace mrssoft\engine\widgets;

class Breadcrumbs extends \yii\base\Widget
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;

    public $attributeParentID = 'parent_id';

    public $attributeTitle = 'title';

    public function run()
    {
        $parentID = \Yii::$app->request->get($this->attributeParentID);

        $route = \Yii::$app->controller->id.'/index';

        while (!empty($parentID)) {
            $item = $this->model->find()->
            select(['id', $this->attributeParentID, $this->attributeTitle])->
            where('id='.$parentID)->
            one();
            if (empty($item)) break;

            $links[] = [
                'label' => $item->{$this->attributeTitle},
                'url' => [$route, $this->attributeParentID => $item->id]
            ];

            $parentID = $item->{$this->attributeParentID};
        }

        if (empty($links)) return;

        echo \yii\widgets\Breadcrumbs::widget([
            'links' => array_reverse($links),
            'homeLink' => [
                'label' => \Yii::t('admin/main', 'Root'),
                'url' => [$route]
            ]
        ]);
    }
}