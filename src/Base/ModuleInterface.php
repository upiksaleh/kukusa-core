<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


interface ModuleInterface
{

    /**
     * Bootstrap both console and web
     * @param \Kukusa\Web\Application|\Kukusa\Console\Application $app
     */
    public function boot_all($app);
    /**
     * Bootstrap console
     * @param \Kukusa\Console\Application $app
     */
    public function boot_console($app);
    /**
     * Bootstrap web
     * @param \Kukusa\Web\Application $app
     */
    public function boot_web($app);


}