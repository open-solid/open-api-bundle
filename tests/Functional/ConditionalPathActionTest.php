<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional;

class ConditionalPathActionTest extends AbstractWebTestCase
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
        $client->jsonRequest('GET', '/conditional');

        $content = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404);
        $this->assertJson($content);
        $this->assertApiResponse($content);
    }
}
