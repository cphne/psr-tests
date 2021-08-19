<?php

namespace HTTP;

use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\HTTP\UploadedFile;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UploadedFileTest extends TestCase
{
    public function testMoveTo()
    {
        $body = (new Factory())->createStream("Foo");
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "testfile";
        $file = $this->getSut(body: $body);
        $file->moveTo($path);
        self::assertFileExists($path);
        self::assertStringEqualsFile($path, "Foo");
    }


    public function testGetStream()
    {
        $body = (new Factory())->createStream("Foo");
        $file = $this->getSut(body: $body);
        self::assertSame("Foo", (string)$file->getStream());
        $file->moveTo($this->tmpPath());
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("File has been moved previously.");
        $file->getStream();
    }

    public function testGetSize()
    {
        $file = $this->getSut(size: 100);
        self::assertSame(100, $file->getSize());
        $file = $this->getSut(size: null);
        self::assertNull($file->getSize());
    }

    public function testGetError()
    {
        $file = $this->getSut(error: UPLOAD_ERR_CANT_WRITE);
        self::assertSame(UPLOAD_ERR_CANT_WRITE, $file->getError());
    }

    public function testGetClientFilename()
    {
        $file = $this->getSut(clientFilename: "Filename.pdf");
        self::assertSame("Filename.pdf", $file->getClientFilename());
    }

    public function testGetClientMediaType()
    {
        $file = $this->getSut(clientMediaType: "application/pdf");
        self::assertSame("application/pdf", $file->getClientMediaType());
    }

    private function tmpPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . "testfile";
    }

    private function getSut(
        string $body = '',
        ?int $size = 0,
        int $error = UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        $body = (new Factory())->createStream($body);

        return new UploadedFile(
            $body,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }
}
