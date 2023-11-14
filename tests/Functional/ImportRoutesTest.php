<?php

namespace Yceruto\Tests\OpenApiBundle\Functional;

use Yceruto\OpenApiBundle\Generator;

class ImportRoutesTest extends AbstractWebTestCase
{
    public function testDefaultServices(): void
    {
        $generator = self::getContainer()->get('openapi.generator');
        $this->assertInstanceOf(Generator::class, $generator);
    }

    public function testDefaultEndpoints(): void
    {
        $client = self::createClient();

        $client->request('GET', '/');
        self::assertResponseIsSuccessful();

        $client->jsonRequest('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();
        self::assertResponseIsSuccessful();
        $this->assertIsString($content);
        $this->assertJson($content);

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('openapi', $data);
        $this->assertArrayHasKey('info', $data);
        $this->assertArrayHasKey('servers', $data);
    }
}
