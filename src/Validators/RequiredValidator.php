<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Validators;


class RequiredValidator extends \yii\validators\RequiredValidator
{
    protected $fieldType = 'required';
}