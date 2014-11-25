<?php

namespace mrssoft\engine\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * Обслуживание поля модели position
 *
 * @property \yii\db\ActiveRecord $owner
 */
class Position extends Behavior
{
    /**
     * @var string аттрибут модели
     */
    public $attribute = 'position';

    /**
     * @var array позиция относительно указанных полей
     */
    public $relativeAttributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    /**
     * Максимальное значение позиции
     * @return int
     */
    public function getMaxPosition()
    {
        $query = (new Query())->select('MAX('.$this->attribute.') AS maxColumn')->from($this->owner->tableName());
        foreach ($this->relativeAttributes as $name)
        {
            $query->andWhere($name.'=:'.$name, [':'.$name => $this->owner->{$name}]);
        }

        return $query->scalar();
    }

    /**
     * Изменение позиции
     * @param $value
     * @throws \Exception
     */
    public function changePosition($value)
    {
        $where = [];
        $params = [':p1' => $value, ':p2' => $this->owner->{$this->attribute}];

        if ($this->owner->{$this->attribute} > $value)
        {
            $where[$this->attribute.' >= :p1 AND '.$this->attribute.' < :p2'] = $params;
            $direction = '+1';
        }
        else
        {
            $where[$this->attribute.' <= :p1 AND '.$this->attribute.' > :p2'] = $params;
            $direction = '-1';
        }

        $this->executeCommand($where, $direction);

        $this->owner->{$this->attribute} = $value;
        $this->owner->update(false);
    }

    /**
     * @param array Query
     * @param $direction int
     */
    private function executeCommand($where, $direction)
    {
        foreach ($this->relativeAttributes as $name)
        {
            $where[$name.'=:'.$name] = [':'.$name => $this->owner->{$name}];
        }

        $params = [];
        foreach ($where as $w)
        {
            foreach ($w as $k => $v)
            {
                $params[$k] = $v;
            }
        }

        $cmd = Yii::$app->db->createCommand()->update(
            $this->owner->tableName(),
            [$this->attribute => new Expression($this->attribute.$direction)],
            implode(' AND ', array_keys($where)),
            $params
        );

        $cmd->execute();
    }

    /**
     * Создание модели
     */
    public function beforeInsert()
    {
        $this->owner->{$this->attribute} = $this->getMaxPosition() + 1;
    }

    /**
     * Удаление модели
     */
    public function afterDelete()
    {
        $this->executeCommand(
            [$this->attribute.'>:p0' => [':p0' => $this->owner->{$this->attribute}]],
            '-1'
        );
    }
}