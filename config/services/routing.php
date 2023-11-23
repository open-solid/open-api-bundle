<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use OpenSolid\OpenApiBundle\Routing\Attribute\Delete;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\OpenApiBundle\Routing\Attribute\Patch;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\OpenApiBundle\Routing\Attribute\Put;
use OpenSolid\OpenApiBundle\Routing\Loader\OpenApiRouteControllerLoader;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('routing.loader.attribute.post')
            ->class(OpenApiRouteControllerLoader::class)
            ->args(['%kernel.environment%'])
            ->call('setRouteAnnotationClass', [Post::class])
            ->tag('routing.loader', ['priority' => -5])

        ->set('routing.loader.attribute.get')
            ->class(OpenApiRouteControllerLoader::class)
            ->args(['%kernel.environment%'])
            ->call('setRouteAnnotationClass', [Get::class])
            ->tag('routing.loader', ['priority' => -5])

        ->set('routing.loader.attribute.put')
            ->class(OpenApiRouteControllerLoader::class)
            ->args(['%kernel.environment%'])
            ->call('setRouteAnnotationClass', [Put::class])
            ->tag('routing.loader', ['priority' => -5])

        ->set('routing.loader.attribute.patch')
            ->class(OpenApiRouteControllerLoader::class)
            ->args(['%kernel.environment%'])
            ->call('setRouteAnnotationClass', [Patch::class])
            ->tag('routing.loader', ['priority' => -5])

        ->set('routing.loader.attribute.delete')
            ->class(OpenApiRouteControllerLoader::class)
            ->args(['%kernel.environment%'])
            ->call('setRouteAnnotationClass', [Delete::class])
            ->tag('routing.loader', ['priority' => -5])
    ;
};
