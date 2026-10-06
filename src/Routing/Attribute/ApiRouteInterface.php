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

namespace OpenSolid\OpenApiBundle\Routing\Attribute;

use Symfony\Component\Routing\Attribute\Route;

/**
 * An attribute that defines both a Symfony route and an OpenAPI operation.
 *
 * @property Route $route
 */
interface ApiRouteInterface
{
    public function getMethod(): string;
}
