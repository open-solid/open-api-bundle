<?php

use OpenSolid\OpenApiBundle\Controller\OpenApiController;
use OpenSolid\OpenApiBundle\Generator;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ArgumentResolver\RequestPayloadArrayResolver;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ControllerResultSubscriber;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser\NativeConstraintGuesser;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\PathValueResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        ->set(RequestPayloadArrayResolver::class)
            ->decorate('argument_resolver.request_payload')
            ->args([
                service('serializer'),
                service('validator')->nullOnInvalid(),
                service('translator')->nullOnInvalid(),
            ])
            ->tag('controller.targeted_value_resolver', ['name' => RequestPayloadArrayResolver::class])
            ->tag('kernel.event_subscriber')
            ->lazy()

        ->set(NativeConstraintGuesser::class)
            ->tag('controller.path_constraint_guesser')
    ;
};
