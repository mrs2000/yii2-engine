<?php

namespace mrssoft\engine\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * Обслуживание поля модели position
 *
 * @property int $maxPosition
 * @property ActiveRecord $owner
 */
class Position extends Behavior
{
    /**
     * @var string аттрибут модели
     */
    public string $attribute = 'position';

    /**
     * @var array позиция относительно указанных полей
     */
    public array $relativeAttributes = [];

    public function events()
    {
        return [
            yii\db\BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            yii\db\BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    /**
     * Максимальное значение позиции
     * @return int
     */
    public function getMaxPosition(): int
    {
        $query = (new Query())->select('MAX(' . $this->attribute . ') AS maxColumn')
                              ->from($this->owner::tableName());
        foreach ($this->relativeAttributes as $name) {
            if ($this->owner->{$name} === null) {
                $query->andWhere($name . ' IS NULL');
            } else {
                $query->andWhere($name . '=:' . $name, [':' . $name => $this->owner->{$name}]);
            }
        }

        return (int)$query->scalar();
    }

    /**
     * Изменение позиции
     * @param int $value
     * @throws \Throwable
     * @throws yii\db\Exception
     * @throws yii\db\StaleObjectException
     */
    public function changePosition(int $value): void
    {
        $where = [];
        $params = [':p1' => $value, ':p2' => $this->owner->{$this->attribute}];

        if ($this->owner->{$this->attribute} > $value) {
            $where[$this->attribute . ' >= :p1 AND ' . $this->attribute . ' < :p2'] = $params;
            $direction = '+1';
        } else {
            $where[$this->attribute . ' <= :p1 AND ' . $this->attribute . ' > :p2'] = $params;
            $direction = '-1';
        }

        $this->executeCommand($where, $direction);

        $this->owner->{$this->attribute} = $value;
        $this->owner->update(false);
    }

    /**
     * Переместить на одну позицию вверх
     */
    public function moveUp(): void
    {
        if ($this->owner->{$this->attribute} > 1) {
            $this->changePosition($this->owner->{$this->attribute} - 1);
        }
    }

    /**
     * Переместить на одну позицию вниз
     */
    public function moveDown(): void
    {
        if ($this->owner->{$this->attribute} < $this->getMaxPosition()) {
            $this->changePosition($this->owner->{$this->attribute} + 1);
        }
    }

    /**
     * @param array $where
     * @param string $direction
     * @throws \yii\db\Exception
     */
    private function executeCommand(array $where, string $direction): void
    {
        foreach ($this->relativeAttributes as $name) {
            if ($this->owner->{$name} === null) {
                $where[$name . ' IS NULL'] = null;
            } else {
                $where[$name . '=:' . $name] = [':' . $name => $this->owner->{$name}];
            }
        }

        $params = [];
        foreach ($where as $w) {
            if ($w !== null) {
                foreach ($w as $k => $v) {
                    $params[$k] = $v;
                }
            }
        }

        $cmd = Yii::$app->db->createCommand()
                            ->update($this->owner::tableName(), [$this->attribute => new Expression($this->attribute . $direction)], implode(' AND ', array_keys($where)), $params);

        $cmd->execute();
    }

    /**
     * Создание модели
     */
    public function beforeInsert(): void
    {
        $this->owner->{$this->attribute} = $this->getMaxPosition() + 1;
    }

    /**
     * Удаление модели
     */
    public function afterDelete(): void
    {
        $this->executeCommand([$this->attribute . '>:p0' => [':p0' => $this->owner->{$this->attribute}]], '-1');
    }
}