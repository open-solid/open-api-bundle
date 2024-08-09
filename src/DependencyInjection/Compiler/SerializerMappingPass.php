<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSolid\OpenApiBundle\DependencyInjection\Compiler;

use OpenSolid\OpenApiBundle\Serializer\Mapping\Loader\OpenApiSerializerMetadataLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SerializerMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('serializer.mapping.chain_loader')) {
            return;
        }

        $chainLoader = $container->getDefinition('serializer.mapping.chain_loader');
        $serializerLoaders = $chainLoader->getArgument(0);
        $serializerLoaders[] = new Reference(OpenApiSerializerMetadataLoader::class);
        $chainLoader->replaceArgument(0, $serializerLoaders);
        $container->getDefinition('serializer.mapping.cache_warmer')->replaceArgument(0, $serializerLoaders);
    }
}
