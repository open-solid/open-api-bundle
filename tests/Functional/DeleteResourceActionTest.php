<?php

namespace Yceruto\Tests\OpenApiBundle\Functional;

class DeleteResourceActionTest extends AbstractWebTestCase
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

    public function testEndpoint(): void
    {
        $client = self::createClient();
        $client->jsonRequest('DELETE', '/resources/4f09d694-446a-4769-9929-dad96a071cad');

        self::assertResponseStatusCodeSame(204);
        $this->assertSame('', $client->getResponse()->getContent());
    }
}
