<?php

namespace Yceruto\OpenApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Yceruto\OpenApiBundle\Tests\Functional\App\AppKernel;

class AbstractWebTestCase extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        static::deleteTmpDir();
    }

    public static function tearDownAfterClass(): void
    {
        static::deleteTmpDir();
    }

    protected static function deleteTmpDir(): void
    {
        if (!file_exists($dir = sys_get_temp_dir().'/'.static::getVarDir())) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    protected static function getKernelClass(): string
    {
        require_once __DIR__.'/App/AppKernel.php';

        return AppKernel::class;
    }

    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        if (!isset($options['test_case'])) {
            $options['test_case'] = static::getTestCase();
        }

        return parent::createClient($options, $server);
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            $options['test_case'] = static::getTestCase();
        }

        return new $class(
            static::getVarDir(),
            $options['test_case'],
            $options['root_config'] ?? 'config.yaml',
            $options['environment'] ?? 'test',
            $options['debug'] ?? false,
        );
    }

    protected static function getVarDir(): string
    {
        return 'YOA'.substr(strrchr(static::class, '\\'), 1);
    }

    protected static function getTestCase(): string
    {
        $parts = explode('\\', static::class);

        return substr(end($parts), 0, -4);
    }

    protected function assertApiDoc(string $content, bool $save = false, string $filename = 'doc.json'): void
    {
        if ($save) {
            file_put_contents(__DIR__.'/App/'.self::getTestCase().'/Output/'.$filename, $content);
        }
        $this->assertJsonStringEqualsJsonFile(__DIR__.'/App/'.self::getTestCase().'/Output/'.$filename, $content);
    }

    protected function assertApiResponse(string $content, bool $save = false, string $filename = 'response.json'): void
    {
        if ($save) {
            file_put_contents(__DIR__.'/App/'.self::getTestCase().'/Output/'.$filename, $content);
        }
        $this->assertJsonStringEqualsJsonFile(__DIR__.'/App/'.self::getTestCase().'/Output/'.$filename, $content);
    }
}
