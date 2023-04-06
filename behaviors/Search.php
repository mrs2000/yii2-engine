<?php

namespace mrssoft\engine\behaviors;

use mrssoft\engine\ActiveRecord;
use ReflectionClass;
use yii;
use yii\base\Behavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Поведение добавляет функции поиска модели
 */
class Search extends Behavior
{
    /**
     * @var array атрибуты фильтров по умолчанию
     */
    public array $searchAttributes = [
        'title' => true,
        'public' => false,
        'position' => false,
        'id' => false,
    ];

    /**
     * Get the name of the class without its namespace
     * @return string
     */
    public function shortName(): string
    {
        return (new ReflectionClass($this->owner))->getShortName();
    }

    /**
     * @return ActiveDataProvider
     */
    public function search(): ActiveDataProvider
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        $query = $owner::find();

        /**
         * Определение cортировки по умолчанию
         */
        if (property_exists($owner, 'defaultOrder')) {
            $defaultOrder = $owner->defaultOrder;
        } else if ($owner->hasAttribute('position')) {
            $defaultOrder = ['position' => SORT_ASC];
        } elseif ($owner->hasAttribute('date')) {
            $defaultOrder = ['date' => SORT_DESC];
        } elseif ($owner->hasAttribute('title')) {
            $defaultOrder = ['title' => SORT_ASC];
        } else {
            $primary = $owner->getPrimaryKey(true);
            $defaultOrder = [key($primary) => SORT_ASC];
        }

        /**
         * Условия отбора относительно родителя
         */
        if (property_exists($owner, 'relativeAttributes')) {
            foreach ($owner->relativeAttributes as $attribute) {
                $value = Yii::$app->request->get($attribute);
                if ($value === null) {
                    $query->andWhere($attribute . ' IS NULL OR 0=' . $attribute);
                } else {
                    $query->andWhere([$attribute => $value]);
                }
            }
        }

        /**
         * Дополнительные условия поиска
         */
        if (property_exists($owner, 'searchCondition')) {
            $query->andWhere($owner->searchCondition);
        }

        /**
         * Отбор с помощью фильтров
         */
        if (Yii::$app->request->get($this->shortName())) {
            if (property_exists($owner, 'searchAttributes')) {
                $this->searchAttributes = array_merge($owner->searchAttributes, $this->searchAttributes);
            }

            foreach ($this->searchAttributes as $attribute => $compare) {
                if ($owner->hasAttribute($attribute)) {
                    $this->addCondition($query, $attribute, $compare);
                }
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => $defaultOrder]
        ]);
    }

    /**
     * Add criteria condition.
     *
     * @param \yii\db\Query $query Query instance.
     * @param string $attribute Searched attribute name
     * @param boolean $partialMatch Matching type
     */
    public function addCondition(yii\db\Query $query, string $attribute, bool $partialMatch = false): void
    {
        $value = $this->owner->{$attribute};
        if ($value === null || trim($value) === '') {
            return;
        }
        if ($partialMatch) {
            $query->andWhere(['LIKE', $attribute, $value]);
        } else {
            $query->andWhere([$attribute => $value]);
        }
    }

    /**
     * Add inner join with criteria.
     *
     * @param ActiveQuery $query Query instance
     * @param string $attribute Serched attribute name
     * @param string $relation Relation name
     * @param string $targetAttribute Target attribute name
     * @param boolean $partialMatch matching type
     */
    public function addWithCondition(ActiveQuery $query, string $attribute, string $relation, string $targetAttribute, bool $partialMatch = false): void
    {
        $value = $this->owner->{$attribute};
        if (trim($value) === '') {
            return;
        }
        if ($partialMatch) {
            $query->innerJoinWith([$relation])
                  ->andWhere(['LIKE', $targetAttribute, $value]);
        } else {
            $query->innerJoinWith([$relation])
                  ->andWhere([$targetAttribute => $value]);
        }
    }
}