<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Console\Controllers;

use Kukusa;
use Kukusa\Console\Controller;
use yii\console\ExitCode;

class SecurityController extends Controller
{
    public function actionPasswordHash($password){
        $this->stdout(Kukusa::$app->security->generatePasswordHash($password) . "\n");
        return ExitCode::OK;
    }
}