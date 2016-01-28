<?php

namespace Presentation\Grids;

use Interop\Container\ContainerInterface;
use Presentation\Framework\Rendering\RendererInterface;
use Presentation\Framework\Rendering\SimpleRenderer;
use Presentation\Framework\Service\Container\WritableContainerInterface;
use Presentation\Framework\Service\ServiceName;
use Presentation\Framework\Service\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(WritableContainerInterface $container)
    {
        $container->extend(ServiceName::RENDERER, function(RendererInterface $renderer, ContainerInterface $container) {
            if (!$renderer instanceof SimpleRenderer) {
                throw new \RuntimeException('Charts supports only SimpleRenderer. You have ' . get_class($renderer));
            }
            $renderer->registerViewsPath(dirname(__DIR__) . '/resources/views');
        });
    }
}
