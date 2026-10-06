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

class ResponseInferenceTest extends AbstractWebTestCase
{
    public function testDoc(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.json');

        self::assertResponseIsSuccessful();
        $this->assertSameFileResponseContent($client->getResponse()->getContent(), 'doc.json');
    }

    public function testDeclaredSuccessResponseStatusCode(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/items', ['name' => 'foo']);

        self::assertResponseStatusCodeSame(201);
        $this->assertSame('{"name":"foo"}', $client->getResponse()->getContent());
    }

    public function testCustomStatusCodeWithoutContent(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/items/1');

        self::assertResponseStatusCodeSame(202);
        $this->assertSame('', $client->getResponse()->getContent());
    }

    public function testHead(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/items/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testOptionsReturnsTheControllerResponse(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/items');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('Allow', 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS');
    }

    public function testCollectionPayloadWithNativeType(): void
    {
        $client = self::createClient();
        $client->jsonRequest('PUT', '/items', [['name' => 'foo'], ['name' => 'bar']]);

        self::assertResponseStatusCodeSame(200);
        $this->assertSame('[{"name":"foo"},{"name":"bar"}]', $client->getResponse()->getContent());
    }

    public function testCollectionPayloadWithNativeTypeIsValidated(): void
    {
        $client = self::createClient();
        $client->jsonRequest('PUT', '/items', [['name' => '']]);

        self::assertResponseIsUnprocessable();
    }

    public function testScalarCollection(): void
    {
        $client = self::createClient();
        $client->request('GET', '/items/ids');

        self::assertResponseStatusCodeSame(200);
        $this->assertSame('[1,2]', $client->getResponse()->getContent());
    }
}
