<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use OpenSolid\OpenApiBundle\Serializer\Mapping\Loader\OpenApiSerializerMetadataLoader;
use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\FormatMetadataLoader;
use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\OpenApiValidatorMetadataLoader;
use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\ValidationMetadataLoader;

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
