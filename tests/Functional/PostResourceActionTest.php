<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional;

class PostResourceActionTest extends AbstractWebTestCase
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
        $client->request('GET', '/schema/PostResourceBody');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'schema.json');
    }

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            'name' => 'foo',
            'status' => 'draft',
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'response.json');
    }

    public function testValidation1(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources');

        $content = $client->getResponse()->getContent();

        self::assertResponseIsUnprocessable();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'validation_error1.json');
    }

    public function testValidation2(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            'name' => 'fo',
            'status' => 'dra',
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsUnprocessable();
        $this->assertJson($content);
        $this->assertSameFileResponseContent($content, 'validation_error2.json');
    }
}
