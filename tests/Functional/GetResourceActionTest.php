<?php

namespace Yceruto\OpenApiBundle\Tests\Functional;

class GetResourceActionTest extends AbstractWebTestCase
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
        $client->jsonRequest('GET', '/resources/4f09d694-446a-4769-9929-dad96a071cad');

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertApiResponse($content);
    }
}