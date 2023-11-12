<?php

namespace Yceruto\OpenApiBundle\Tests\Functional;

use Symfony\Component\HttpKernel\KernelInterface;
use Yceruto\OpenApiBundle\Generator;

class DefaultTest extends AbstractWebTestCase
{
    public function testDefaultServices(): void
    {
        $generator = self::getContainer()->get('openapi.generator');
        $this->assertInstanceOf(Generator::class, $generator);
    }

    public function testDefaultEndpoints(): void
    {
        $client = self::createClient(['test_case' => 'Default']);

        $client->request('GET', '/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/openapi.json');
        $content = $client->getResponse()->getContent();
        self::assertResponseIsSuccessful();
        $this->assertIsString($content);
        $this->assertJson($content);

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('openapi', $data);
        $this->assertArrayHasKey('info', $data);
        $this->assertArrayHasKey('servers', $data);
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return parent::createKernel(['test_case' => 'Default']);
    }
}
