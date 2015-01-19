<?
namespace mrssoft\engine;

/**
 * @method search()
 * @method addCondition()
 * @method addWithCondition()
 * @method shortName()
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    const EVENT_COPY = 'copy';

    public function init()
    {
        if ($this->scenario == 'create')
        {
            if ($this->hasAttribute('public'))
            {
                $this->setAttribute('public', 1);
            }

            if ($this->hasAttribute('date'))
            {
                $this->setAttribute('date', date('Y-m-d'));
            }
        }
    }

    public function copy()
    {
        if ($this->hasAttribute('date'))
        {
            $this->setAttribute('date', date('Y-m-d'));
        }

        $this->trigger(self::EVENT_COPY);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return static::find()->where('public=1');
    }
}