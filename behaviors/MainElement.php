<?php

namespace mrssoft\engine\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @property array $relationCondition
 */
class MainElement extends Behavior
{
    /** @var ActiveRecord */
    public $owner;

    public string $attribute = 'main';

    public array $relativeAttributes = [];

    public function events()
    {
        return [
            yii\db\BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            yii\db\BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            yii\db\BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function beforeInsert()
    {
        if ($this->hasMain() === false) {
            $this->owner->{$this->attribute} = 1;
        }
    }

    public function beforeUpdate()
    {
        if (array_key_exists($this->attribute, $this->owner->dirtyAttributes) && $this->owner->{$this->attribute} == 1) {
            Yii::$app->db->createCommand()
                         ->update($this->owner::tableName(), [$this->attribute => 0], $this->getRelationCondition())
                         ->execute();
        }
    }

    public function afterDelete()
    {
        if ($this->hasMain() === false) {
            $obj = $this->owner::find()
                               ->where($this->getRelationCondition())
                               ->one();
            if ($obj) {
                $obj->{$this->attribute} = 1;
                $obj->save();
            }
        }
    }

    /**
     * @return bool
     */
    private function hasMain():bool
    {
        $query = (new Query())->from($this->owner::tableName())
                              ->where($this->getRelationCondition())
                              ->andWhere([$this->attribute => 1])
                              ->count();
        return (bool)$query;
    }

    /**
     * @return array
     */
    private function getRelationCondition(): array
    {
        $condition = [];
        foreach ($this->relativeAttributes as $attribute) {
            $condition[$attribute] = $this->owner->{$attribute};
        }
        return $condition;
    }
}