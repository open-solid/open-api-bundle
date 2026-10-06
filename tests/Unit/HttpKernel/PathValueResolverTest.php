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

namespace OpenSolid\Tests\OpenApiBundle\Unit\HttpKernel;

use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser\NativeConstraintGuesser;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\PathValueResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

class PathValueResolverTest extends TestCase
{
    public function testVariadicArgumentIsRejected(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Mapping variadic argument "$id" is not supported.');

        $this->createResolver()->resolve(new Request(), new ArgumentMetadata('id', 'string', true, false, null));
    }

    public function testMissingValue(): void
    {
        $this->assertSame([], $this->createResolver()->resolve(new Request(), new ArgumentMetadata('id', 'string', false, false, null)));
    }

    public function testValueWithoutPathAttribute(): void
    {
        $request = new Request(attributes: ['id' => 'foo']);

        $this->assertSame(['foo'], $this->createResolver()->resolve($request, new ArgumentMetadata('id', 'string', false, false, null)));
    }

    public function testValueWithoutConstraints(): void
    {
        $request = new Request(attributes: ['id' => 'foo']);
        $argument = new ArgumentMetadata('id', 'string', false, false, null, attributes: [new Path()]);

        $this->assertSame(['foo'], $this->createResolver()->resolve($request, $argument));
    }

    public function testValidValue(): void
    {
        $request = new Request(attributes: ['id' => '4f09d694-446a-4769-9929-dad96a071cad']);
        $argument = new ArgumentMetadata('id', 'string', false, false, null, attributes: [new Path(format: 'uuid')]);

        $this->assertSame(['4f09d694-446a-4769-9929-dad96a071cad'], $this->createResolver()->resolve($request, $argument));
    }

    public function testInvalidValue(): void
    {
        $this->expectException(ValidationFailedException::class);

        $request = new Request(attributes: ['id' => 'foo']);
        $argument = new ArgumentMetadata('id', 'string', false, false, null, attributes: [new Path(format: 'uuid')]);

        $this->createResolver()->resolve($request, $argument);
    }

    private function createResolver(): PathValueResolver
    {
        return new PathValueResolver(new RequestAttributeValueResolver(), Validation::createValidator(), [new NativeConstraintGuesser()]);
    }
}
