<?php

use OpenApi\Analysers;
use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenSolid\OpenApiBundle\OpenApi\Analyser as OABAnalyser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->instanceof(OABAnalyser\Factory\AttributeFactoryInterface::class)
            ->tag('openapi.attribute_factory')

        ->instanceof(AnnotationFactoryInterface::class)
           ->tag('openapi.annotation_factory')

        ->set(OABAnalyser\Factory\SchemaAttributeFactory::class)
            ->tag('openapi.attribute_factory')

        ->set(OABAnalyser\Factory\MethodAttributeFactory::class)
            ->tag('openapi.attribute_factory')

        ->set(OABAnalyser\Factory\PropertyAttributeFactory::class)
            ->tag('openapi.attribute_factory')

        ->set(Analysers\DocBlockAnnotationFactory::class)
            ->tag('openapi.annotation_factory')

        ->set(OABAnalyser\Factory\AttributeFactoryChain::class)
            ->args([
                tagged_iterator('openapi.attribute_factory'),
            ])
            ->tag('openapi.annotation_factory')

        ->set(OABAnalyser\Factory\ReflectionAnalyserFactory::class)

        ->set(Analysers\ReflectionAnalyser::class)
            ->factory(service(OABAnalyser\Factory\ReflectionAnalyserFactory::class))
            ->args([
                tagged_iterator('openapi.annotation_factory'),
            ])

        ->set(OABAnalyser\Resolver\PhpAnalyserResolver::class)
            ->args([
                service(Analysers\ReflectionAnalyser::class),
            ])
            ->tag('openapi.analyser_resolver')

        ->set(OABAnalyser\SerializedAnalyser::class)

        ->set(OABAnalyser\Resolver\SerializedAnalyserResolver::class)
            ->args([
                service(OABAnalyser\SerializedAnalyser::class),
            ])
            ->tag('openapi.analyser_resolver')

        ->set(OABAnalyser\Resolver\AnalyserResolverChain::class)
            ->args([
                tagged_iterator('openapi.analyser_resolver'),
            ])

        ->alias(OABAnalyser\Resolver\AnalyserResolverInterface::class, OABAnalyser\Resolver\AnalyserResolverChain::class)

        ->set(OABAnalyser\ResolvableAnalyser::class)
            ->args([
                service(OABAnalyser\Resolver\AnalyserResolverInterface::class),
            ])

        ->alias(Analysers\AnalyserInterface::class, OABAnalyser\ResolvableAnalyser::class)
    ;
};
