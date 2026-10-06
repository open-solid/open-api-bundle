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

namespace OpenSolid\Tests\OpenApiBundle\Unit\Command;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenSolid\OpenApiBundle\Command\ExportOpenApiCommand;
use OpenSolid\OpenApiBundle\Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ExportOpenApiCommandTest extends TestCase
{
    public function testSpecNotFound(): void
    {
        $generator = new Generator(new ReflectionAnalyser([new AttributeAnnotationFactory()]), [], [dirname(__DIR__).'/Controller/Fixtures/Empty']);
        $tester = new CommandTester(new ExportOpenApiCommand($generator));

        $this->assertSame(Command::FAILURE, $tester->execute([]));
        $this->assertStringContainsString('OpenAPI spec not found.', $tester->getDisplay());
    }
}
