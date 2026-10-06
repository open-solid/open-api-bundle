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

namespace OpenSolid\Tests\OpenApiBundle\Unit\DependencyInjection;

use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\SerializerMappingPass;
use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\TrackPathsPass;
use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\ValidatorMappingPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPassTest extends TestCase
{
    public function testPassesWithoutSerializerAndValidator(): void
    {
        $container = new ContainerBuilder();

        (new SerializerMappingPass())->process($container);
        (new ValidatorMappingPass())->process($container);

        $this->assertFalse($container->has('serializer.mapping.chain_loader'));
        $this->assertFalse($container->has('validator.builder'));
    }

    public function testMissingPath(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('openapi_paths', [__DIR__.'/missing']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenAPI path "'.__DIR__.'/missing" not found.');

        (new TrackPathsPass())->process($container);
    }
}
