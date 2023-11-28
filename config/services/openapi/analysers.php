<?php

use OpenApi\Analysers;
use OpenSolid\OpenApiBundle\OpenApi\Analyser as OABAnalyser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(OABAnalyser\Guesser\Operation\OperationRequestBodyGuesser::class)
            ->tag('openapi.analyser_guesser')

        ->set(OABAnalyser\Guesser\Operation\OperationQueryParameterGuesser::class)
            ->tag('openapi.analyser_guesser')

        ->set(OABAnalyser\Guesser\Operation\OperationResponseGuesser::class)
            ->tag('openapi.analyser_guesser')

        ->set(OABAnalyser\Guesser\Property\PropertyDefaultGuesser::class)
            ->tag('openapi.analyser_guesser')

        ->set(OABAnalyser\Guesser\Property\PropertyEnumGuesser::class)
            ->tag('openapi.analyser_guesser')

        ->set(OABAnalyser\Guesser\Schema\SchemaDefaultGuesser::class)
            ->tag('openapi.analyser_guesser')

        ->set(Analysers\DocBlockAnnotationFactory::class)
            ->tag('openapi.annotation_factory')

        ->set(OABAnalyser\Factory\AttributeFactory::class)
            ->args([
                tagged_iterator('openapi.analyser_guesser'),
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

        ->set(OABAnalyser\ResolvableAnalyser::class)
            ->args([
                tagged_iterator('openapi.analyser_resolver'),
            ])

        ->alias(Analysers\AnalyserInterface::class, OABAnalyser\ResolvableAnalyser::class)
    ;
};
