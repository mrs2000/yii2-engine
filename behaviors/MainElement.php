<?

namespace app\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Query;

class MainElement extends Behavior
{
    /** @var ActiveRecord */
    public $owner;

    public $attribute = 'main';

    public $relativeAttributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function beforeInsert()
    {
        if ($this->hasMain() == false) {
            $this->owner->{$this->attribute} = 1;
        }
    }

    public function beforeUpdate()
    {
        if ($this->owner->{$this->attribute} == 1)
        {
            \Yii::$app->db->createCommand()->update(
                $this->owner->tableName(),
                [$this->attribute => 0],
                $this->getRelationCondition()
            )->execute();
        }
    }

    public function afterUpdate()
    {
        $this->setFirstMain();
    }

    public function afterDelete()
    {
        $this->setFirstMain();
    }

    private function setFirstMain()
    {
        if ($this->hasMain() == false) {
            $obj = $this->owner->find()->where($this->getRelationCondition())->one();
            if ($obj) {
                $obj->{$this->attribute} = 1;
                $obj->save();
            }
        }
    }

    /**
     * @return bool
     */
    private function hasMain()
    {
        $query = (new Query())->from($this->owner->tableName())
                              ->where($this->getRelationCondition())
                              ->andWhere([$this->attribute => 1])
                              ->count();
        return (bool)$query;
    }

    /**
     * @return array
     */
    private function getRelationCondition()
    {
        $condition = [];
        foreach ($this->relativeAttributes as $attribute) {
            $condition[$attribute] =  $this->owner->{$attribute};
        }
        return $condition;
    }
}