<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\i18n;


use Kukusa;

class PhpMessageSource extends \yii\i18n\PhpMessageSource
{
    public $pathMap = [];

    protected function loadMessages($category, $language)
    {
        $messages = parent::loadMessages($category, $language);
        foreach ($this->pathMap as $pathMap) {
            $pathMap = Kukusa::getAlias($pathMap);
            if (!is_dir($pathMap)) continue;
            $fileMap = $pathMap . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $category . '.php';
            if (is_file($fileMap)) {
                $messages = array_merge($messages, include $fileMap);
            }
        }
        return $messages;
    }
}