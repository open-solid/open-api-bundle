<?php

use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Processors\ProcessorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Yceruto\OpenApiBundle\Controller\OpenApiController;
use Yceruto\OpenApiBundle\Generator;
use Yceruto\OpenApiBundle\HttpKernel\Controller\PathValueResolver;
use Yceruto\OpenApiBundle\OpenApi\Analyser\AttributeFactoryInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->instanceof(AttributeFactoryInterface::class)
            ->tag('openapi.annotation_factory')

        ->instanceof(AnnotationFactoryInterface::class)
            ->tag('openapi.annotation_factory')

        ->instanceof(ProcessorInterface::class)
            ->tag('openapi.processor')

        ->set(Generator::class)
            ->args([
                tagged_iterator('openapi.annotation_factory'),
                tagged_iterator('openapi.processor', defaultPriorityMethod: 'priority'),
                param('openapi_generator_scan_dirs'),
                param('openapi_spec'),
            ])

        ->set(PathValueResolver::class)
            ->args([
                service('argument_resolver.request_attribute'),
                service(ValidatorInterface::class),
            ])
            ->tag('controller.argument_value_resolver', ['priority' => 110])

        ->set(OpenApiController::class)
            ->args([
                service(Generator::class),
            ])
            ->tag('controller.service_arguments')
    ;
};
