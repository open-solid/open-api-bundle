<?php

namespace Yceruto\OpenApiBundle\Tests\Functional;

class PostResourceActionTest extends AbstractWebTestCase
{
    public function testDoc(): void
    {
        $client = self::createClient();
        $client->request('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertApiDoc($content);
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

    public function testValidation(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/resources', [
            'name' => null,
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsUnprocessable();
        $this->assertJson($content);
        $this->assertApiResponse($content, filename: 'validation_error.json');
    }
}
