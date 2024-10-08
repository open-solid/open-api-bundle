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

namespace OpenSolid\Tests\OpenApiBundle\Functional;

class ExportOpenApiCommandTest extends AbstractWebTestCase
{
    public function testExportToJson(): void
    {
        $tester = $this->getApplicationTester();
        $tester->run([
            'command' => 'openapi:export',
            '--output' => $filename = sys_get_temp_dir().'/oab-export-command-openapi.json',
        ]);

        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString('OpenAPI spec has been exported successfully.', $tester->getDisplay());

        $actual = trim(file_get_contents($filename));
        $this->assertSameFileResponseContent($actual, 'doc.json');
    }

    public function testExportToYaml(): void
    {
        $tester = $this->getApplicationTester();
        $tester->run([
            'command' => 'openapi:export',
            '--output' => $filename = sys_get_temp_dir().'/oab-export-command-openapi.yaml',
        ]);

        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString('OpenAPI spec has been exported successfully.', $tester->getDisplay());

        $actual = trim(file_get_contents($filename));
        // file_put_contents(__DIR__.'/App/ExportOpenApiCommand/Output/doc.yaml', $actual);
        $expected = trim(file_get_contents(__DIR__.'/App/ExportOpenApiCommand/Output/doc.yaml'));
        $this->assertSame($expected, $actual);
    }
}
