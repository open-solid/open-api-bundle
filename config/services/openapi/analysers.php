<?php

use OpenApi\Analysers;
use OpenApi\Analysers\AnnotationFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yceruto\OpenApiBundle\OpenApi\Analyser\AttributeFactoryChain;
use Yceruto\OpenApiBundle\OpenApi\Analyser\AttributeFactoryInterface;
use Yceruto\OpenApiBundle\OpenApi\Analyser\MethodAttributeFactory;

use Yceruto\OpenApiBundle\OpenApi\Analyser\PropertyAttributeFactory;

use Yceruto\OpenApiBundle\OpenApi\Analyser\ReflectionAnalyserFactory;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->instanceof(AttributeFactoryInterface::class)
            ->tag('openapi.attribute_factory')

        ->instanceof(AnnotationFactoryInterface::class)
           ->tag('openapi.annotation_factory')

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
