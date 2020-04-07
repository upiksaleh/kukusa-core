<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Actions;


class ViewAction extends Action
{
    public $viewFile = '@kukusa/views/model-repo/view';

    public function run()
    {
        return $this->controller->render($this->viewFile, $this->viewParams());
    }

}