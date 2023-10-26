<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yceruto\OpenApiBundle\Mapping\Serializer\Loader\OpenApiSerializerMetadataLoader;
use Yceruto\OpenApiBundle\Mapping\Validator\Loader\FormatMetadataLoader;
use Yceruto\OpenApiBundle\Mapping\Validator\Loader\OpenApiValidatorMetadataLoader;
use Yceruto\OpenApiBundle\Mapping\Validator\Loader\ValidationMetadataLoader;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(FormatMetadataLoader::class)
            ->tag('openapi.validator_metadata_loader')

        ->set(ValidationMetadataLoader::class)
            ->tag('openapi.validator_metadata_loader')

        ->set(OpenApiSerializerMetadataLoader::class)

        ->set(OpenApiValidatorMetadataLoader::class)
            ->args([
                tagged_iterator('openapi.validator_metadata_loader'),
            ])
    ;
};
