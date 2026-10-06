<?php

use OpenApi\Processors;
use OpenSolid\OpenApiBundle\OpenApi as OAB;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(OAB\Constraint\ConstraintSchemaApplier::class)

        ->set(Processors\DocBlockDescriptions::class)
            ->tag('openapi.processor', ['priority' => 100])

        ->set(Processors\MergeIntoOpenApi::class)
            ->tag('openapi.processor', ['priority' => 95])

        ->set(OAB\Processor\RemoveScannedQueryParameters::class)
            ->tag('openapi.processor', ['priority' => 92])

        ->set(Processors\MergeIntoComponents::class)
            ->tag('openapi.processor', ['priority' => 90])

        ->set(Processors\ExpandClasses::class)
            ->tag('openapi.processor', ['priority' => 85])

        ->set(Processors\ExpandInterfaces::class)
            ->tag('openapi.processor', ['priority' => 80])

        ->set(Processors\ExpandTraits::class)
            ->tag('openapi.processor', ['priority' => 75])

        ->set(Processors\ExpandEnums::class)
            ->tag('openapi.processor', ['priority' => 70])

        ->set(Processors\AugmentSchemas::class)
            ->tag('openapi.processor', ['priority' => 65])

        ->set(OAB\Processor\AugmentSchemas::class)
            ->args([service(OAB\Constraint\ConstraintSchemaApplier::class)])
            ->tag('openapi.processor', ['priority' => 64])

        ->set(OAB\Processor\AugmentSchemaConstraints::class)
            ->args([service(OAB\Constraint\ConstraintSchemaApplier::class)])
            ->tag('openapi.processor', ['priority' => 63])

        ->set(Processors\AugmentRequestBody::class)
            ->tag('openapi.processor', ['priority' => 62])

        ->set(Processors\AugmentProperties::class)
            ->tag('openapi.processor', ['priority' => 60])

        ->set(Processors\AugmentDiscriminators::class)
            ->tag('openapi.processor', ['priority' => 55])

        ->set(Processors\BuildPaths::class)
            ->tag('openapi.processor', ['priority' => 50])

        ->set(Processors\AugmentParameters::class)
            ->tag('openapi.processor', ['priority' => 45])

        ->set(OAB\Processor\AugmentPathParameters::class)
            ->tag('openapi.processor', ['priority' => 44])

        ->set(OAB\Processor\AugmentQueryParameters::class)
            ->tag('openapi.processor', ['priority' => 43])

        ->set(OAB\Processor\AugmentParameterConstraints::class)
            ->args([service(OAB\Constraint\ConstraintSchemaApplier::class)])
            ->tag('openapi.processor', ['priority' => 42])

        ->set(OAB\Processor\AugmentEnumSchemas::class)
            ->tag('openapi.processor', ['priority' => 41])

        ->set(Processors\AugmentRefs::class)
            ->tag('openapi.processor', ['priority' => 40])

        ->set(Processors\AugmentItems::class)
            ->tag('openapi.processor', ['priority' => 33])

        ->set(Processors\MergeJsonContent::class)
            ->tag('openapi.processor', ['priority' => 30])

        ->set(Processors\MergeXmlContent::class)
            ->tag('openapi.processor', ['priority' => 25])

        ->set(Processors\AugmentMediaType::class)
            ->tag('openapi.processor', ['priority' => 23])

        ->set(Processors\OperationId::class)
            ->tag('openapi.processor', ['priority' => 20])

        ->set(Processors\CleanUnmerged::class)
            ->tag('openapi.processor', ['priority' => 15])

        ->set(OAB\Processor\PathDeciderProcessor::class)
            ->args([
                tagged_iterator('routing.expression_language_provider'),
                service('router.request_context'),
            ])
            ->tag('openapi.processor', ['priority' => 10])

        ->set(OAB\Processor\CleanupAnnotations::class)
            ->tag('openapi.processor', ['priority' => 5])

        ->set(Processors\AugmentTags::class)
            ->tag('openapi.processor', ['priority' => 0])
    ;
};
