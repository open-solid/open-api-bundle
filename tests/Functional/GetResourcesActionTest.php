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

class GetResourcesActionTest extends AbstractWebTestCase
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

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/resources?sort=author&page[limit]=5&page[offset]=5&filter[name]=John%20Doe');

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'response.json');
    }
}
