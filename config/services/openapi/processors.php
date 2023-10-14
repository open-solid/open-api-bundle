<?php

use OpenApi\Processors;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yceruto\OpenApiBundle\OpenApi\Processor\CleanupProcessor;
use Yceruto\OpenApiBundle\OpenApi\Processor\MergeConfigIntoOpenApi;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->instanceof(Processors\ProcessorInterface::class)
            ->tag('openapi.processor')

        ->set(Processors\DocBlockDescriptions::class)
            ->tag('openapi.processor', ['priority' => 100])

        ->set(Processors\MergeIntoOpenApi::class)
            ->tag('openapi.processor', ['priority' => 95])

        ->set(MergeConfigIntoOpenApi::class)
            ->args([
                param('openapi_spec'),
            ])
            ->tag('openapi.processor', ['priority' => 90])

        ->set(Processors\MergeIntoComponents::class)
            ->tag('openapi.processor', ['priority' => 85])

        ->set(Processors\ExpandClasses::class)
            ->tag('openapi.processor', ['priority' => 80])

        ->set(Processors\ExpandInterfaces::class)
            ->tag('openapi.processor', ['priority' => 75])

        ->set(Processors\ExpandTraits::class)
            ->tag('openapi.processor', ['priority' => 70])

        ->set(Processors\ExpandEnums::class)
            ->tag('openapi.processor', ['priority' => 65])

        ->set(Processors\AugmentSchemas::class)
            ->tag('openapi.processor', ['priority' => 60])

        ->set(Processors\AugmentProperties::class)
            ->tag('openapi.processor', ['priority' => 55])

        ->set(Processors\BuildPaths::class)
            ->tag('openapi.processor', ['priority' => 50])

        ->set(Processors\AugmentParameters::class)
            ->tag('openapi.processor', ['priority' => 45])

        ->set(Processors\AugmentRefs::class)
            ->tag('openapi.processor', ['priority' => 40])

        ->set(Processors\MergeJsonContent::class)
            ->tag('openapi.processor', ['priority' => 35])

        ->set(Processors\MergeXmlContent::class)
            ->tag('openapi.processor', ['priority' => 30])

        ->set(Processors\OperationId::class)
            ->tag('openapi.processor', ['priority' => 25])

        ->set(Processors\CleanUnmerged::class)
            ->tag('openapi.processor', ['priority' => 20])

        ->set(CleanupProcessor::class)
            ->tag('openapi.processor', ['priority' => 15])
    ;
};
