<?php
namespace mrssoft\engine\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Ссылки на родительский элемент
 */
class ParentLinks extends Widget
{
    public $attributeTitle = 'title';

    public $controller = '';

    public $modelClass;

    public $title;

    public $attributeID;

    public function run()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass;

        if (empty($this->title)) {
            $this->title = Yii::t('admin/main', 'List');
        }

        if (empty($this->controller)) {
            $n = strrpos($this->modelClass, '\\');
            $this->controller = substr($this->modelClass, $n + 1);
        }

        $this->controller = strtolower($this->controller);

        $id = Yii::$app->request->get($this->attributeID);

        echo Html::a($this->title, [$this->controller . '/index']);
        echo ' &bullet; ';
        echo Html::a($model::findOne($id)->{$this->attributeTitle}, [
            $this->controller . '/edit',
            'id' => $id
        ]);
    }
}