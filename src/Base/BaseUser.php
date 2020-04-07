<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;

use Kukusa;

/**
 * Class BaseUser
 * @package Kukusa\Base
 * @property string $group
 * @property string $fullName
 */
abstract class BaseUser extends BaseModelController implements BaseUserInterface
{

}