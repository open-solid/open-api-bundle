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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TrackPathsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $paths = $container->getParameter('openapi_paths');

        foreach ($paths as $path) {
            if (!$container->fileExists($path)) {
                throw new \RuntimeException(sprintf('OpenAPI path "%s" not found.', $path));
            }
        }
    }
}
