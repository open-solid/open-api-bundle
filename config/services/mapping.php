<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yceruto\OpenApiBundle\Mapping\Loader\OpenApiSerializerAttributeLoader;
use Yceruto\OpenApiBundle\Mapping\Loader\OpenApiValidatorAttributeLoader;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(OpenApiSerializerAttributeLoader::class)
        ->set(OpenApiValidatorAttributeLoader::class)
    ;
};
