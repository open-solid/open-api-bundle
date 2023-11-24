<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional;

class GetResourceActionTest extends AbstractWebTestCase
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
        $client->jsonRequest('GET', '/resources/4f09d694-446a-4769-9929-dad96a071cad');

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'response.json');
    }

    public function testValidation(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/resources/invalid-uuid');

        $content = $client->getResponse()->getContent();

        self::assertResponseIsUnprocessable();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'path_error.json');
    }
}
