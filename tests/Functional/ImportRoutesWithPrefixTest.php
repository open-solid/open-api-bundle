<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional;

class ImportRoutesWithPrefixTest extends AbstractWebTestCase
{
    public function testEndpointsWithPrefix(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/api/openapi.json');
        $content = $client->getResponse()->getContent();
        self::assertResponseIsSuccessful();
        $this->assertIsString($content);
        $this->assertJson($content);
    }
}
