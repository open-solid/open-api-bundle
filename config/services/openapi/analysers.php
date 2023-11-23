<?php

use OpenApi\Analysers;
use OpenApi\Analysers\AnnotationFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\AttributeFactoryChain;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\AttributeFactoryInterface;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\MethodAttributeFactory;

use OpenSolid\OpenApiBundle\OpenApi\Analyser\PropertyAttributeFactory;

use OpenSolid\OpenApiBundle\OpenApi\Analyser\ReflectionAnalyserFactory;

use OpenSolid\OpenApiBundle\OpenApi\Analyser\SchemaAttributeFactory;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->instanceof(AttributeFactoryInterface::class)
            ->tag('openapi.attribute_factory')

        ->instanceof(AnnotationFactoryInterface::class)
           ->tag('openapi.annotation_factory')

        ->set(SchemaAttributeFactory::class)
            ->tag('openapi.attribute_factory')

        ->set(MethodAttributeFactory::class)
            ->tag('openapi.attribute_factory')

        ->set(PropertyAttributeFactory::class)
            ->tag('openapi.attribute_factory')

        ->set(Analysers\DocBlockAnnotationFactory::class)
            ->tag('openapi.annotation_factory')

        ->set(AttributeFactoryChain::class)
            ->args([
                tagged_iterator('openapi.attribute_factory'),
            ])
            ->tag('openapi.annotation_factory')

        ->set(ReflectionAnalyserFactory::class)

        ->set(Analysers\ReflectionAnalyser::class)
            ->factory(service(ReflectionAnalyserFactory::class))
            ->args([
                tagged_iterator('openapi.annotation_factory'),
            ])

        ->alias(Analysers\AnalyserInterface::class, Analysers\ReflectionAnalyser::class)
    ;
};
