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

namespace Functional;

use OpenSolid\Tests\OpenApiBundle\Functional\AbstractWebTestCase;

class PostResourcesActionTest extends AbstractWebTestCase
{
    public function testDoc(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'doc.json');
    }

    public function testDocSchema(): void
    {
        $client = self::createClient();
        $client->request('GET', '/schema/PostResourcePayload');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'schema.json');
    }

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            [
                'name' => 'foo',
                'status' => 'draft',
            ],
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'response.json');
    }
}
