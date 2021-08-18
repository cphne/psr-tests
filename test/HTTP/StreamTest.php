<?php

namespace Cphne\PsrTests\HTTP;

use Cphne\PsrTests\HTTP\Factory\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @internal
 * @coversNothing
 */
class StreamTest extends TestCase
{
    private static StreamFactoryInterface $factory;

    public static function setUpBeforeClass(): void
    {
        self::$factory = new StreamFactory();
    }

    public function testDestruct()
    {
        self::assertTrue(true);
//        $stream = $this->getMockBuilder(Stream::class)
//            ->disableOriginalConstructor()
//            ->onlyMethods(["close"])
//            ->getMock();
//        $stream->expects($this->once())
//            ->method("close");
        $stream = $this->createMock(Stream::class);
        unset($stream);
    }

    public function testToString()
    {
        $stream = self::$factory->createStream('Foo');
        $stream->seek(2);
        self::assertEquals('Foo', (string) $stream);
    }

    public function testClose()
    {
        $stream = self::$factory->createStream('Foo');
        $stream->close();
    }

    public function testDetach()
    {
        $stream = self::$factory->createStream();
        $resource = $stream->detach();
        self::assertIsResource($resource);
    }

    public function testTell()
    {
    }

    public function testWrite()
    {
    }

    public function testRead()
    {
    }

    public function testIsSeekable()
    {
    }

    public function testRewind()
    {
    }

    public function testIsReadable()
    {
    }

    public function testSeek()
    {
    }

    public function testConstruct()
    {
    }

    public function testGetSize()
    {
    }

    public function testEof()
    {
    }

    public function testIsWritable()
    {
    }

    public function testGetContents()
    {
    }

    public function testGetMetadata()
    {
    }
}
