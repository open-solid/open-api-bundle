<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSolid\Tests\OpenApiBundle\Functional;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\EventListener\ControllerAttributesListener;

class NativeRequestMappingTest extends AbstractWebTestCase
{
    public function testMapUploadedFile(): void
    {
        $client = self::createClient();
        $client->request('POST', '/upload', [], ['file' => new UploadedFile(__FILE__, 'upload.txt', 'text/plain', null, true)]);

        self::assertResponseIsSuccessful();
        $this->assertSame('upload.txt', $client->getResponse()->getContent());
    }

    public function testControllerAttributesRunBeforeThePayloadIsMapped(): void
    {
        if (!class_exists(ControllerAttributesListener::class)) {
            $this->markTestSkipped('Controller attribute events require Symfony 8.1 or later.');
        }

        $client = self::createClient();
        $client->request('POST', '/closed', [], [], ['CONTENT_TYPE' => 'application/json'], '{"name": ');

        self::assertResponseStatusCodeSame(403);
    }
}
