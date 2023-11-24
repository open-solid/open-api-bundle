<?php

namespace OpenSolid\OpenApiBundle\DependencyInjection\Compiler;

use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\OpenApiValidatorMetadataLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValidatorMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('validator.builder')) {
            return;
        }

        $validatorBuilder = $container->getDefinition('validator.builder');
        $validatorBuilder->addMethodCall('addLoader', [new Reference(OpenApiValidatorMetadataLoader::class)]);
    }
}
