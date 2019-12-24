<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace mrssoft\engine\events;

use yii\base\Event;

/**
 * AfterCopyEvent represents the information available in [[ActiveRecord::EVENT_AFTER_COPY]].
 */
class AfterCopyEvent extends Event
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $source;
}
