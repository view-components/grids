<?php

namespace ViewComponents\Grids;

use ViewComponents\Grids\WebApp\Controller;
use ViewComponents\TestingHelpers\Installer\Installer as BaseInstaller;

/**
 * Installer for testing/demo web-application.
 *
 * This class should not be used on production.
 */
class Installer extends BaseInstaller
{
    protected function provideEnvDefaults($values)
    {
        $values = parent::provideEnvDefaults($values);
        $values['WEBAPP_CONTROLLERS'] = Controller::class;
        return $values;
    }
}
