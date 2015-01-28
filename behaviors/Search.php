<?
namespace mrssoft\engine\behaviors;

/**
 * Поведение добавляет функции поиска
 */
class Search extends \yii\base\Behavior
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $owner;

    /**
     * @var array атрибуты фильтров по умолчанию
     */
    public $searchAttributes = [
        'title' => true,
        'public' => false,
        'position' => false,
        'id' => false,
    ];

    /**
     * Get the name of the class without its namespace
     * @return string
     */
    public function shortName()
    {
        return (new \ReflectionClass($this->owner))->getShortName();
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function search()
    {
        /**
         * Определение cортировки по умолчанию
         */
        if ($this->owner->canGetProperty('defaultOrder', true, false)) {
            $defaultOrder = $this->owner->{'defaultOrder'};
        } else {
            $defaultOrder = ['id' => SORT_ASC];

            if ($this->owner->hasAttribute('position')) {
                $defaultOrder = ['position' => SORT_ASC];
            } elseif ($this->owner->hasAttribute('date')) {
                $defaultOrder = ['date' => SORT_DESC];
            } elseif ($this->owner->hasAttribute('title')) {
                $defaultOrder = ['title' => SORT_ASC];
            }
        }

        $query = $this->owner->find();

        /**
         * Условия отбора относительно родителя
         */
        if (property_exists($this->owner, 'relativeAttributes')) {
            foreach ($this->owner->{'relativeAttributes'} as $attribute) {
                $value = \Yii::$app->request->get($attribute);
                if ($value === null) {
                    $query->andWhere($attribute . ' IS NULL');
                } else {
                    $query->andWhere($attribute . '=:' . $attribute, [':' . $attribute => $value]);
                }
            }
        }

        /**
         * Дополнительные условия поиска
         */
        if ($this->owner->canGetProperty('searchCondition')) {
            $query->andWhere($this->owner->{'searchCondition'});
        }

        /**
         * Отбор с помощью фильтров
         */
        if (\Yii::$app->request->get($this->shortName())) {
            if ($this->owner->canGetProperty('searchAttributes')) {
                $this->searchAttributes = array_merge(
                    $this->owner->{'searchAttributes'},
                    $this->searchAttributes
                );
            }

            foreach ($this->searchAttributes as $attribute => $compare) {
                if ($this->owner->hasAttribute($attribute)) {
                    $this->addCondition($query, $attribute, $compare);
                }
            }
        }

        return new \yii\data\ActiveDataProvider([
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
    public function addCondition($query, $attribute, $partialMatch = false)
    {
        $value = $this->owner->{$attribute};
        if (trim($value) === '') {
            return;
        }
        if ($partialMatch) {
            $query->andWhere(['like', $attribute, $value]);
        } else {
            if ($value === null) {
                $query->andWhere($attribute . ' IS NULL');
            } else {
                $query->andWhere([$attribute => $value]);
            }
        }
    }

    /**
     * Add inner join with criteria.
     *
     * @param \yii\db\ActiveQuery $query Query instance
     * @param string $attribute Serched attribute name
     * @param string $relation Relation name
     * @param string $targetAttribute Target attribute name
     * @param boolean $partialMatch matching type
     */
    public function addWithCondition($query, $attribute, $relation, $targetAttribute, $partialMatch = false)
    {
        $value = $this->owner->{$attribute};
        if (trim($value) === '') {
            return;
        }
        if ($partialMatch) {
            $query->innerJoinWith([$relation])->andWhere(['like', $targetAttribute, $value]);
        } else {
            $query->innerJoinWith([$relation])->andWhere([$targetAttribute => $value]);
        }
    }
}