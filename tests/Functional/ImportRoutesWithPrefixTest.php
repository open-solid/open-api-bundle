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

class ImportRoutesWithPrefixTest extends AbstractWebTestCase
{
    public function testEndpointsWithPrefix(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/api/openapi.json');
        $content = $client->getResponse()->getContent();
        self::assertResponseIsSuccessful();
        $this->assertIsString($content);
        $this->assertJson($content);
    }
}
