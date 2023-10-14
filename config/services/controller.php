<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Yceruto\OpenApiBundle\Controller\OpenApiController;
use Yceruto\OpenApiBundle\Generator;
use Yceruto\OpenApiBundle\HttpKernel\Controller\ControllerResultSubscriber;
use Yceruto\OpenApiBundle\HttpKernel\Controller\PathValueResolver;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(OpenApiController::class)
            ->args([
                service(Generator::class),
            ])
            ->tag('controller.service_arguments')

        ->set(ControllerResultSubscriber::class)
            ->args([
                service(SerializerInterface::class),
            ])
            ->tag('kernel.event_subscriber')

        ->set(PathValueResolver::class)
            ->args([
                service('argument_resolver.request_attribute'),
                service(ValidatorInterface::class),
            ])
            ->tag('controller.argument_value_resolver', ['priority' => 110])
    ;
};
