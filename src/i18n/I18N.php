<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\i18n;


class I18N extends \yii\i18n\I18N
{
    public function getAllMessages($category, $language)
    {
        return $this->getMessageSource($category)->allMessages($category, $language);
    }
}