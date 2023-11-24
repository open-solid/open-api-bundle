<?php

use OpenApi\Analysers\AnalyserInterface;
use OpenSolid\OpenApiBundle\Generator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->import('services/openapi/analysers.php');
    $container->import('services/openapi/processors.php');
    $container->import('services/command.php');
    $container->import('services/controller.php');
    $container->import('services/mapping.php');
    $container->import('services/routing.php');

    $container->services()
        ->set('openapi.generator', Generator::class)
            ->args([
                service(AnalyserInterface::class),
                tagged_iterator('openapi.processor', defaultPriorityMethod: 'priority'),
                param('openapi_paths'),
            ])

        ->alias(Generator::class, 'openapi.generator')
    ;
};
