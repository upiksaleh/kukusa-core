<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;

/**
 * Class User
 * @package Kukusa\Web
 * @property UserIdent $identity
 */
class User extends \yii\web\User
{
    /**
     * grup user. Ex: ['system' => '\App\Model\UserModel']
     * @var array
     */
    public $groups;
    public $identityClass = UserIdent::class;

    /**
     * @param string $group
     * @return string|null
     */
    public function groupClass($group)
    {
        if(isset($this->groups[$group]))
            return $this->groups[$group];
        return null;
    }
}