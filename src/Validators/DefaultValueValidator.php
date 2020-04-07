<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Validators;


class DefaultValueValidator extends \yii\validators\DefaultValueValidator
{
    public function getClientOptions($model, $attribute)
    {
        return ['_value' => $this->value];
    }
}