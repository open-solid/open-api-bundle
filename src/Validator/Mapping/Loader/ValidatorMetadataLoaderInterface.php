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

namespace OpenSolid\OpenApiBundle\Validator\Mapping\Loader;

use OpenSolid\OpenApiBundle\Attribute\Property;
use Symfony\Component\Validator\Mapping\ClassMetadata;

interface ValidatorMetadataLoaderInterface
{
    public function load(ClassMetadata $metadata, \ReflectionProperty $reflectionProperty, Property $property): bool;
}
