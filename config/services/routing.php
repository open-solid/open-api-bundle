<?php

use OpenSolid\OpenApiBundle\Routing\Attribute\ApiRouteInterface;
use OpenSolid\OpenApiBundle\Routing\Loader\OpenApiRouteControllerLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('routing.loader.attribute.openapi')
            ->class(OpenApiRouteControllerLoader::class)
            ->args(['%kernel.environment%'])
            ->call('setRouteAttributeClass', [ApiRouteInterface::class])
            ->tag('routing.loader', ['priority' => -5])
    ;
};
