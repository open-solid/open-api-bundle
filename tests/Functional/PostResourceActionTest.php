<?php

namespace Yceruto\Tests\OpenApiBundle\Functional;

class PostResourceActionTest extends AbstractWebTestCase
{
    public function testDoc(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertApiDoc($content);
    }

    public function testDocSchema(): void
    {
        $client = self::createClient();
        $client->request('GET', '/schema/PostResourceBody');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertApiDoc($content, filename: 'schema.json');
    }

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            'name' => 'foo',
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertApiResponse($content);
    }

    public function testValidation1(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            'name' => null,
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsUnprocessable();
        $this->assertJson($content);
        $this->assertApiResponse($content, filename: 'validation_error1.json');
    }

    public function testValidation2(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            'name' => 'fo',
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsUnprocessable();
        $this->assertJson($content);
        $this->assertApiResponse($content, filename: 'validation_error2.json');
    }
}
