<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional;

use OpenSolid\OpenApiBundle\Generator;
use Symfony\Component\Yaml\Yaml;

class ImportRoutesTest extends AbstractWebTestCase
{
    public function testDefaultServices(): void
    {
        $generator = self::getContainer()->get('openapi.generator');
        $this->assertInstanceOf(Generator::class, $generator);
    }

    public function testIndexController(): void
    {
        $client = self::createClient();

        $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'text/html; charset=UTF-8');
    }

    public function testYamlController(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.yaml');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/x-yaml');
        $this->assertIsString($content);

        $data = Yaml::parse($content);

        $this->assertArrayHasKey('openapi', $data);
        $this->assertArrayHasKey('info', $data);
        $this->assertArrayHasKey('servers', $data);
        $this->assertArrayHasKey('paths', $data);
        $this->assertArrayHasKey('components', $data);
    }

    public function testJsonController(): void
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertIsString($content);
        $this->assertJson($content);

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('openapi', $data);
        $this->assertArrayHasKey('info', $data);
        $this->assertArrayHasKey('servers', $data);
        $this->assertArrayHasKey('paths', $data);
        $this->assertArrayHasKey('components', $data);
    }

    public function testJsonSchemaController(): void
    {
        $client = self::createClient();

        $client->jsonRequest('GET', '/schema/ResourceView');
        $content = $client->getResponse()->getContent();
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertIsString($content);
        $this->assertJson($content);

        $this->assertSameFileResponseContent($content, 'schema.json');
    }
}
