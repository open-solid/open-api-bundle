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

class NativeQueryTest extends AbstractWebTestCase
{
    public function testDoc(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.json');

        self::assertResponseIsSuccessful();
        $this->assertSameFileResponseContent($client->getResponse()->getContent(), 'doc.json');
    }

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->request('GET', '/stores/main/items?filter[name]=foo&filter[currency]=EUR&page=2&status=active');

        self::assertResponseStatusCodeSame(200);
        $this->assertSame([['store' => 'main', 'name' => 'foo', 'page' => 2, 'status' => 'active']], json_decode($client->getResponse()->getContent(), true));
    }

    public function testEmptyCollection(): void
    {
        $client = self::createClient();
        $client->request('GET', '/stores/main/items?q=empty');

        self::assertResponseStatusCodeSame(200);
        $this->assertSame('[]', $client->getResponse()->getContent());
    }
}
