<?php

namespace mrssoft\engine;

use mrssoft\engine\events\AfterCopyEvent;
use yii\db\ActiveQuery;

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
    public const EVENT_AFTER_COPY = 'afterCopy';

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
     * This method is called at the end of copy a record.
     * @param ActiveRecord $source
     */
    public function afterCopy(ActiveRecord $source)
    {
        $this->trigger(self::EVENT_AFTER_COPY, new AfterCopyEvent(['source' => $source]));
    }

    /**
     * @return ActiveQuery
     */
    public static function active(): ActiveQuery
    {
        return static::find()
                     ->where(['public' => 1]);
    }
}