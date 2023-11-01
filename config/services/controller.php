<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Yceruto\OpenApiBundle\Controller\OpenApiController;
use Yceruto\OpenApiBundle\Generator;
use Yceruto\OpenApiBundle\HttpKernel\Controller\ControllerResultSubscriber;
use Yceruto\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser\NativeConstraintGuesser;
use Yceruto\OpenApiBundle\HttpKernel\Controller\ValueResolver\PathValueResolver;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

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
                tagged_iterator('controller.path_constraint_guesser'),
            ])
            ->tag('controller.argument_value_resolver', ['priority' => 110])

        ->set(NativeConstraintGuesser::class)
            ->tag('controller.path_constraint_guesser')
    ;
};
