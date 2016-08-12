<?php

namespace ViewComponents\Grids;

use ViewComponents\ViewComponents\Rendering\TemplateFinder;
use ViewComponents\ViewComponents\Service\ServiceContainer;
use ViewComponents\ViewComponents\Service\ServiceId;
use ViewComponents\ViewComponents\Service\ServiceProviderInterface;

/**
 * Grids service provider for view-components.
 *
 * It's registered in bootstrap.php file that's autoloaded by composer.
 * 
 * Note for Laravel users: It's not a service provider for Laravel. 
 * This class is used internally by view-components.
 * Do not include it to your service-providers section of Laravel application config.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container)
    {
        /** registers path to grid views */
        $container->extend(ServiceId::TEMPLATE_FINDER, function (TemplateFinder $finder) {
            $finder->registerPath(dirname(__DIR__) . '/resources/views');
            return $finder;
        });
    }
}
