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

namespace OpenSolid\Tests\OpenApiBundle\Unit\Serializer;

use OpenSolid\OpenApiBundle\Attribute\Property;
use OpenSolid\OpenApiBundle\Serializer\Mapping\Loader\OpenApiSerializerMetadataLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadata;

class OpenApiSerializerMetadataLoaderTest extends TestCase
{
    public function testGroups(): void
    {
        $metadata = new ClassMetadata(GroupsFixture::class);
        $metadata->addAttributeMetadata(new AttributeMetadata('name'));

        $this->assertTrue((new OpenApiSerializerMetadataLoader())->loadClassMetadata($metadata));

        $attributes = $metadata->getAttributesMetadata();
        $this->assertSame(['read', 'write'], $attributes['name']->getGroups());
        $this->assertSame(['read'], $attributes['id']->getGroups());
        $this->assertSame([], $attributes['other']->getGroups());
    }

    public function testWithoutGroups(): void
    {
        $metadata = new ClassMetadata(\stdClass::class);

        $this->assertFalse((new OpenApiSerializerMetadataLoader())->loadClassMetadata($metadata));
    }
}

class GroupsFixture
{
    #[Property(groups: ['read', 'write'])]
    public string $name;

    #[Property(groups: ['read'])]
    public string $id;

    #[Property]
    public string $other;
}
