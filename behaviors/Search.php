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
     * @var array
     */
    public $defaultOrder = null;

    private $searchAttributes = [
        'title' => true,
        'public' => false,
        'position' => false,
        'id' => false,
    ];

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function search()
    {
        if ($this->defaultOrder === null)
        {
            $this->defaultOrder = ['id' => SORT_ASC];

            if ($this->owner->hasAttribute('position'))
            {
                $this->defaultOrder = ['position' => SORT_ASC];
            }
            elseif ($this->owner->hasAttribute('date'))
            {
                $this->defaultOrder = ['date' => SORT_DESC];
            }
            elseif ($this->owner->hasAttribute('title'))
            {
                $this->defaultOrder = ['title' => SORT_ASC];
            }
        }

        $query = $this->owner->find();
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => $this->defaultOrder]
        ]);

        if (\Yii::$app->request->get())
        {
            foreach ($this->searchAttributes as $attribute => $compare)
            {
                if ($this->owner->hasAttribute($attribute))
                {
                    $this->addCondition($query, $attribute, $compare);
                }
            }
        }

        return $dataProvider;
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
        if (trim($value) === '')
        {
            return;
        }
        if ($partialMatch)
        {
            $query->andWhere(['like', $attribute, $value]);
        }
        else
        {
            $query->andWhere([$attribute => $value]);
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
        if (trim($value) === '')
        {
            return;
        }
        if ($partialMatch)
        {
            $query->innerJoinWith([$relation])->andWhere(['like', $targetAttribute, $value]);
        }
        else
        {
            $query->innerJoinWith([$relation])->andWhere([$targetAttribute => $value]);
        }
    }
}