<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;

class DynamicModel extends \yii\base\DynamicModel
{

    protected $_formName;

    public function formName()
    {
        return $this->_formName ?: parent::formName();
    }

    public function setFormName($name)
    {
        $this->_formName = $name;
    }
}