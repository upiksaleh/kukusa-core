<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Themes\Kukusa;

use Kukusa\Widgets\LoginWidget;

class Theme extends \Kukusa\Theme\BaseTheme {

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Kukusa';
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return __DIR__;
    }

    /**
     * @inheritDoc
     */
    public function getContainer()
    {
        return [
            LoginWidget::class=>\Kukusa\Themes\Kukusa\Widgets\LoginWidget::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function getPathMap()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function mainAssets()
    {
        // TODO: Implement mainAssets() method.
    }
}