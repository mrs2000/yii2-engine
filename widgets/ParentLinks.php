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
    var $attributeTitle = 'title';

    var $controller = '';

    var $modelClass;

    var $title = 'Список';

    var $attributeID;

    public function run()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass;

        if (empty($this->controller))
        {
            $n = strrpos($this->modelClass, '\\');
            $this->controller = substr($this->modelClass, $n + 1);
        }

        $this->controller = strtolower($this->controller);

        $id = Yii::$app->request->get($this->attributeID);
        echo Html::a($model::findOne($id)->{$this->attributeTitle}, [$this->controller.'/edit', 'id' => $id]).' &bullet; ';
        echo Html::a($this->title, [$this->controller.'/index']);
    }
}