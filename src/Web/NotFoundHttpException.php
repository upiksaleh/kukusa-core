<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


class NotFoundHttpException extends \yii\web\NotFoundHttpException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        if(!$message) $message = \Kukusa::t('kukusa', 'Halaman idak ditemukan.');
        parent::__construct($message, $code, $previous);
    }
}