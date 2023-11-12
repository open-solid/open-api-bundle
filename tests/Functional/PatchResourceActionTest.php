<?php

namespace Yceruto\OpenApiBundle\Tests\Functional;

class PatchResourceActionTest extends AbstractWebTestCase
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
        $client->jsonRequest('PATCH', '/resources', [
            'name' => 'bar',
        ]);

        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertApiResponse($content);
    }
}