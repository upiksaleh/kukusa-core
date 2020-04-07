<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Actions;


class CreateAction extends FormAction
{
    public $type = self::FORM_TYPE_CREATE;
    public function run()
    {
        return $this->controller->render($this->viewFile, $this->viewParams([
            'type'=>$this->type
        ]));
    }
}