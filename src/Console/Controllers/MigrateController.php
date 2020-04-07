<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Console\Controllers;


class MigrateController extends \yii\console\controllers\MigrateController
{
    public $migrationPath = ['@kukusa/migrations', '@app/migrations'];

}