<?php

namespace mrssoft\engine;

/**
 * @property array $searchAttributes
 * @property array|string $defaultOrder
 * @property array|string $searchCondition
 * @property array $relativeAttributes
 *
 * @method search()
 * @method addCondition($query, $attribute, $partialMatch = false)
 * @method addWithCondition($query, $attribute, $relation, $targetAttribute, $partialMatch)
 * @method shortName()
 * @method changePosition($position)
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    public const EVENT_COPY = 'copy';

    public function init()
    {
        if ($this->scenario == 'create') {
            if ($this->hasAttribute('public')) {
                $this->setAttribute('public', 1);
            }

            if ($this->hasAttribute('date')) {
                $this->setAttribute('date', date('Y-m-d'));
            }
        }
    }

    public function copy()
    {
        if ($this->hasAttribute('date')) {
            $this->setAttribute('date', date('Y-m-d'));
        }

        $this->trigger(self::EVENT_COPY);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return static::find()
                     ->where('public=1');
    }
}