<?php

use OpenSolid\OpenApiBundle\Command\ExportOpenApiCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use OpenSolid\OpenApiBundle\Generator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(ExportOpenApiCommand::class)
            ->args([
                service(Generator::class),
            ])
            ->tag('console.command')
    ;
};
