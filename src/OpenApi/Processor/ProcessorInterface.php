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

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;

/**
 * A step of the OpenAPI processor pipeline.
 *
 * Services implementing this interface are tagged "openapi.processor" automatically.
 * Use the tag "priority" attribute to place the processor in the pipeline.
 */
interface ProcessorInterface
{
    public function __invoke(Analysis $analysis): void;
}
