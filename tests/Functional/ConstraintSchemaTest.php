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

class ConstraintSchemaTest extends AbstractWebTestCase
{
    public function testDoc(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertSameFileResponseContent($content, 'doc.json');
    }

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/items', [
            'name' => 'foo',
            'size' => 1,
            'code' => 'X1',
            'quantity' => 1,
            'notBlankInteger' => 1,
            'tags' => ['a'],
        ]);

        self::assertResponseStatusCodeSame(201);
        $this->assertSameFileResponseContent($client->getResponse()->getContent(), 'response.json');
    }

    public function testValidation(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/items', [
            'name' => 'fo',
            'size' => 1,
            'code' => '',
            'quantity' => null,
            'notBlankInteger' => 1,
        ]);

        self::assertResponseIsUnprocessable();
        $this->assertSameFileResponseContent($client->getResponse()->getContent(), 'validation_error.json');
    }
}
