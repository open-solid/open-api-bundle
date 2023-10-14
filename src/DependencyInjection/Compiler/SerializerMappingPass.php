<?php

namespace Yceruto\OpenApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Yceruto\OpenApiBundle\Mapping\Loader\OpenApiSerializerAttributeLoader;

class SerializerMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('serializer.mapping.chain_loader')) {
            return;
        }

        $chainLoader = $container->getDefinition('serializer.mapping.chain_loader');
        $serializerLoaders = $chainLoader->getArgument(0);
        $serializerLoaders[] = new Reference(OpenApiSerializerAttributeLoader::class);
        $chainLoader->replaceArgument(0, $serializerLoaders);
        $container->getDefinition('serializer.mapping.cache_warmer')->replaceArgument(0, $serializerLoaders);
    }
}
