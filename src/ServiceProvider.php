<?php

namespace ViewComponents\Grids;

use Interop\Container\ContainerInterface;
use RuntimeException;
use ViewComponents\ViewComponents\Rendering\RendererInterface;
use ViewComponents\ViewComponents\Rendering\SimpleRenderer;
use ViewComponents\ViewComponents\Service\ServiceContainer;
use ViewComponents\ViewComponents\Service\ServiceName;
use ViewComponents\ViewComponents\Service\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container)
    {
        /** registers path to grid views */
        $container->extend(
            ServiceName::RENDERER,
            function (RendererInterface $renderer) {
                if (!$renderer instanceof SimpleRenderer) {
                    throw new RuntimeException('Grids supports only SimpleRenderer. You have ' . get_class($renderer));
                }
                $renderer->registerViewsPath(dirname(__DIR__) . '/resources/views');
                return $renderer;
            }
        );
    }
}
