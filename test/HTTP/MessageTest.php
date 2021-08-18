<?php

namespace HTTP;

use Cphne\PsrTests\HTTP\Factory\StreamFactory;
use Cphne\PsrTests\HTTP\Message;
use Cphne\PsrTests\HTTP\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;



class MessageTest extends TestCase
{

    public function test__construct()
    {
        $message = new Message(
            (new StreamFactory())->createStream("foo"),
            ["header" => "header-value"]
        );
        self::assertSame("foo", $message->getBody()->getContents());
        self::assertSame("1.1", $message->getProtocolVersion());
        self::assertEquals(["header" => ["header-value"]], $message->getHeaders());
    }

    /**
     * @dataProvider getProtocolVersionProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testGetProtocolVersion(string $input, string $expected)
    {
        $message = new Message($this->getBodyMock(), [], $input);
        self::assertSame($expected, $message->getProtocolVersion());
    }

    public function getProtocolVersionProvider()
    {
        yield ["1.1", "1.1"];
        yield ["1.0", "1.0"];
        yield ["3.9", "3.9"];
    }

    public function testWithProtocolVersion()
    {
        $message = new Message($this->getBodyMock(), [], "1.0");
        $newMessage = $message->withProtocolVersion("1.1");
        self::assertEquals("1.0", $message->getProtocolVersion());
        self::assertInstanceOf(Message::class, $newMessage);
        self::assertEquals("1.1", $newMessage->getProtocolVersion());
    }


    public function testGetHeaders()
    {
        $message = new Message(
            $this->getBodyMock(), [
                                    "Header-Key" => "value1, value2",
                                    "heaDeR-Key" => "value"
                                ]
        );
        $headers = $message->getHeaders();
        self::assertCount(2, $headers);
        self::assertEquals(
            [
                "Header-Key" => ["value1", "value2"],
                "heaDeR-Key" => ["value"]
            ],
            $headers
        );
    }

    /**
     * @dataProvider hasHeaderProvider
     *
     * @param $headerCaseSensitive
     * @param $header
     * @param $expected
     */
    public function testHasHeader($headerCaseSensitive, $header, $expected)
    {
        $message = new Message($this->getBodyMock(), [$headerCaseSensitive => "FooValue"]);
        self::assertEquals($expected, $message->hasHeader($header));
    }

    public function hasHeaderProvider(): \Generator
    {
        yield ["Content-Type", "conTent-tyPe", true];
        yield ["Content-Type", "foo", false];
        yield ["test", "test", true];
    }

    /**
     * @dataProvider getHeaderProvider
     *
     * @param array $headers
     * @param string $key
     * @param array $expected
     */
    public function testGetHeader(array $headers, string $key, array $expected)
    {
        $message = new Message($this->getBodyMock(), $headers);
        self::assertEquals($expected, $message->getHeader($key));
    }

    public function getHeaderProvider()
    {
        yield [["Content-Type" => "application/json"], "content-type", ["application/json"]];
        yield [["Content-Type" => "application/pdf"], "Content-Type", ["application/pdf"]];
        yield [["Content-Type" => "application/json"], "user-agent", []];
    }

    /**
     * @dataProvider getHeaderLineProvider
     *
     * @param array $headers
     * @param string $key
     * @param string $expected
     */
    public function testGetHeaderLine(array $headers, string $key, string $expected)
    {
        $message = new Message($this->getBodyMock(), $headers);
        self::assertEquals($expected, $message->getHeaderLine($key));
    }

    public function getHeaderLineProvider()
    {
        yield [["Type" => "value1, value2"], "type", "value1, value2"];
        yield [["Type" => "value1, value2"], "foo", ""];
        yield [["Type" => "value1"], "Type", "value1"];
    }


    public function testWithHeader()
    {
        $message = new Message($this->getBodyMock(), ["Ok" => "okValue"]);
        $newMessage = $message->withHeader("ok", "Foo");
        self::assertInstanceOf(Message::class, $newMessage);
        self::assertEquals(["Ok" => ["okValue"]], $message->getHeaders());
        self::assertEquals(["Ok" => ["Foo"]], $newMessage->getHeaders());
    }

    public function testWithHeaderException()
    {
        $message = new Message($this->getBodyMock(), ["Ok" => "okValue"]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Header foo does not exist in original array");
        $message->withHeader("foo", "bar, baz");
    }

    /**
     * @dataProvider withAddedHeaderProvider
     *
     * @param array $base
     * @param array $baseExpected
     * @param string $toAddKey
     * @param array|string $toAddValue
     * @param array $expected
     */
    public function testWithAddedHeader(
        array $base,
        array $baseExpected,
        string $toAddKey,
        array|string $toAddValue,
        array $expected
    ): void {
        $message = new Message($this->getBodyMock(), $base);
        $newMessage = $message->withAddedHeader($toAddKey, $toAddValue);
        self::assertEquals($baseExpected, $message->getHeaders());
        self::assertEquals($expected, $newMessage->getHeaders());
    }

    public function withAddedHeaderProvider()
    {
        yield [
            ["Key" => "value1"],
            ["Key" => ["value1"]],
            "key",
            "value2",
            ["Key" => ["value1", "value2"]]
        ];
        yield [
            ["Key" => "value1"],
            ["Key" => ["value1"]],
            "key",
            ["value2", "value3"],
            ["Key" => ["value1", "value2", "value3"]]
        ];
        yield [
            ["Key" => "value1"],
            ["Key" => ["value1"]],
            "foo",
            "bar",
            ["Key" => ["value1"], "foo" => ["bar"]]
        ];
    }

    /**
     * @dataProvider withoutHeaderProvider
     *
     * @param array $base
     * @param array $baseExpected
     * @param string $keyToRemove
     * @param array $expected
     */
    public function testWithoutHeader(array $base, array $baseExpected, string $keyToRemove, array $expected)
    {
        $message = new Message($this->getBodyMock(), $base);
        $newMessage = $message->withoutHeader($keyToRemove);
        self::assertEquals($baseExpected, $message->getHeaders());
        self::assertEquals($expected, $newMessage->getHeaders());
    }

    public function withoutHeaderProvider()
    {
        yield [
            ["Foo" => "Bar"],
            ["Foo" => ["Bar"]],
            "foo",
            []
        ];
        yield [
            ["Foo" => "Bar", "Bar" => "foo"],
            ["Foo" => ["Bar"], "Bar" => ["foo"]],
            "Foo",
            ["Bar" => ["foo"]]
        ];
        yield [
            ["Foo" => "Bar"],
            ["Foo" => ["Bar"]],
            "Bar",
            ["Foo" => ["Bar"]]
        ];
    }

    public function testGetBody()
    {
        $message = new Message((new StreamFactory())->createStream("test"));
        $body = $message->getBody();
        self::assertEquals("test", $body->getContents());
    }

    public function testWithBody()
    {
        $factory = new StreamFactory();
        $message = new Message($factory->createStream("foo"));
        $newMessage = $message->withBody($factory->createStream("bar"));
        self::assertEquals("foo", $message->getBody()->getContents());
        self::assertEquals("bar", $newMessage->getBody()->getContents());
    }

    protected function getBodyMock(): StreamInterface
    {
        return $this->createMock(Stream::class);
    }

}
