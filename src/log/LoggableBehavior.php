<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Log;

use Kukusa\Db\ActiveRecord;
use yii\base\Behavior;

class LoggableBehavior extends Behavior
{
    private $_oldAttributes = [];

    /**
     * attribute will not save to old attribute
     * @var array
     */
    public $ignore = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function afterFind($event)
    {
        $this->_oldAttributes = $this->owner->getAttributes();
    }

    public function afterInsert($event)
    {
        $attributes = $this->owner->getAttributes();
        foreach ($attributes as $key => $val) {
            if (in_array($key, $this->ignore)) {
                unset($attributes[$key]);
            }
        }
        ActivityLog::newLog(ActivityLog::TYPE_INSERT, $this->owner, $attributes);
    }

    public function afterUpdate($event)
    {
        $updated_from = [];
        $updated_to = [];
        foreach ($this->_oldAttributes as $key => $value) {
            if (in_array($key, array_merge(['updated_at', 'updated_by', 'created_at', 'created_by'], $this->ignore))) {
                continue;
            }
            if ($value != $this->owner->getAttribute($key)) {
                $updated_from[$key] = $value;
                $updated_to[$key] = $this->owner->getAttribute($key);
            }
        }
        if (!empty($updated_from) && !empty($updated_to)) {
            $msg = [
                'old' => $updated_from,
                'new' => $updated_to
            ];
            $log = ActivityLog::newLog(ActivityLog::TYPE_UPDATE, $this->owner, $msg);
        }
    }

    public function afterDelete($event)
    {

        $attributes = $this->owner->getAttributes();
        foreach ($attributes as $key => $val) {
            if (in_array($key, $this->ignore)) {
                unset($attributes[$key]);
            }
        }

        ActivityLog::newLog(ActivityLog::TYPE_DELETE, $this->owner, $attributes);
    }
}