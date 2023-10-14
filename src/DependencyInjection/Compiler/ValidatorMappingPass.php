<?php

namespace Yceruto\OpenApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Yceruto\OpenApiBundle\Mapping\Loader\OpenApiValidatorAttributeLoader;

class ValidatorMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('validator.builder')) {
            return;
        }

        $validatorBuilder = $container->getDefinition('validator.builder');
        $validatorBuilder->addMethodCall('addLoader', [new Reference(OpenApiValidatorAttributeLoader::class)]);
    }
}
