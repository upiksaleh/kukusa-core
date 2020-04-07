<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


interface BaseUserInterface
{
    /**
     * full name of user
     * @return string
     */
    public function getFullName();
}