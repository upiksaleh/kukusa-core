<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


interface LoginFormInterface
{
    /**
     * handle login
     * @return bool
     */
    public function login();
}