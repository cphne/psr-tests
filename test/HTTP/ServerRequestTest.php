<?php

namespace HTTP;

use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\HTTP\ServerRequest;
use Cphne\PsrTests\HTTP\UploadedFile;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequestTest extends TestCase
{
    public function testGetServerParams()
    {
        $request = $this->getSut(serverParams: ["foo" => "bar"]);
        self::assertEquals(["foo" => "bar"], $request->getServerParams());
    }

    public function testCookies()
    {
        $request = $this->getSut();
        $request = $request->withCookieParams(["nomnomnom" => "cookies"]);
        self::assertEquals(["nomnomnom" => "cookies"], $request->getCookieParams());
    }

    public function testGetQueryParams()
    {
        $request = $this->getSut(uri: "http://example.com?id=1&name=test");
        self::assertSame(["id" => "1", "name" => "test"], $request->getQueryParams());
    }

    public function testWithQueryParams()
    {
        $request = $this->getSut(uri: "http://example.com?id=1");
        $newRequest = $request->withQueryParams(["foo" => "bar"]);
        self::assertSame(["id" => "1"], $request->getQueryParams());
        self::assertSame(["foo" => "bar"], $newRequest->getQueryParams());
    }

    public function testUploadedFiles()
    {
        $files = [$this->createMock(UploadedFile::class), $this->createMock(UploadedFile::class)];
        $request = $this->getSut();
        self::assertSame([], $request->getUploadedFiles());
        $newRequest = $request->withUploadedFiles($files);
        self::assertSame([], $request->getUploadedFiles());
        self::assertCount(2, $newRequest->getUploadedFiles());
    }


    public function testGetParsedBody()
    {
        $factory = new Factory();
        $body = $factory->createStream(json_encode(["foo" => "bar"], JSON_THROW_ON_ERROR));
        $request = $this->getSut(method: "PUT", body: $body, headers: ["Content-Type" => "application/json"]);
        self::assertSame(["foo" => "bar"], $request->getParsedBody());
    }

    public function testWithParsedBody()
    {
        $factory = new Factory();
        $body = $factory->createStream(json_encode(["foo" => "bar"], JSON_THROW_ON_ERROR));
        $request = $this->getSut(method: "PUT", body: $body, headers: ["Content-Type" => "application/json"]);
        $newRequest = $request->withParsedBody(["bar" => "foo"]);
        self::assertSame(["foo" => "bar"], $request->getParsedBody());
        self::assertSame(["bar" => "foo"], $newRequest->getParsedBody());
    }

    public function testWithAttribute()
    {
        $request = $this->getSut();
        $newRequest = $request->withAttribute("foo", "bar");
        self::assertSame([], $request->getAttributes());
        self::assertSame(["foo" => "bar"], $newRequest->getAttributes());
    }


//    public function testGetAttributes()
//    {
//        // see testWithAttribute
//    }

    public function testWithoutAttribute()
    {
        $request = $this->getSut()
            ->withAttribute("foo", "bar")
            ->withAttribute("ok", "test");
        $newRequest = $request->withoutAttribute("ok");
        self::assertSame(["foo" => "bar", "ok" => "test"], $request->getAttributes());
        self::assertSame(["foo" => "bar"], $newRequest->getAttributes());
    }


    private function getSut(
        string $method = "",
        string|UriInterface $uri = "",
        ?StreamInterface $body = null,
        array $headers = [],
        string $protocolVersion = "1.1",
        ?array $serverParams = null
    ) {
        $factory = new Factory();
        return new ServerRequest(
            method: $method,
            uri: (is_string($uri)) ? $factory->createUri($uri) : $uri,
            body: $body ?? $factory->createStream(""),
            headers: $headers ?? [],
            protocolVersion: $protocolVersion,
            serverParams: $serverParams ?? []
        );
    }

}
