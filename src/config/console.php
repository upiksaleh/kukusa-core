<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


return [
    'id' => 'kukusa',
    'name' => 'Kukusa App',
    'bootstrap' => ['log'],
    'controllerMap' => [
        'migrate' => 'Kukusa\Console\Controllers\MigrateController',
        'security' => 'Kukusa\Console\Controllers\SecurityController',
        'setup' => 'Kukusa\Console\Controllers\SetupController',
    ],
];