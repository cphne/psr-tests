<?php

namespace HTTP;

use Cphne\PsrTests\HTTP\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 * @coversNothing
 */
class FactoryTest extends TestCase
{
    private Factory $factory;

    public function setUp(): void
    {
        $this->factory = new Factory();
        parent::setUp();
    }

    /**
     * @dataProvider createResponseProvider
     *
     * @param $expectedPhrase
     */
    public function testCreateResponse(int $code, string $phrase, int $expectedCode, string $expectedPhrase)
    {
        $response = $this->factory->createResponse($code, $phrase);
        self::assertSame($expectedCode, $response->getStatusCode());
        self::assertSame($expectedPhrase, $response->getReasonPhrase());
    }

    public function createResponseProvider()
    {
        yield [200, '', 200, 'OK'];
        yield [404, 'Not Found.', 404, 'Not Found.'];
        yield [500, '', 500, 'Internal Server Error'];
    }

    /**
     * @dataProvider createUriProvider
     *
     * @param mixed $expected
     */
    public function testCreateUri(string $actual, string $expected)
    {
        $uri = $this->factory->createUri($actual);
        self::assertSame($expected, (string) $uri);
    }

    public function createUriProvider()
    {
        yield ['http://localhost:8000', 'http://localhost:8000'];
        yield ['https://localhost?id=10', 'https://localhost?id=10'];
        yield ['sft://domain.dom:22#test', 'sft://domain.dom:22#test'];
        yield ['smtp://user:pass@provider.com:443/mailserver', 'smtp://user:pass@provider.com:443/mailserver'];
    }

    public function testCreateRequest()
    {
        $request = $this->factory->createRequest('GET', 'http://example.com/test');
        self::assertSame('GET', $request->getMethod());
        self::assertSame('http://example.com/test', (string) $request->getUri());
        $request = $this->factory->createRequest('POST', $this->factory->createUri('http://example.com/test'));
        self::assertSame('POST', $request->getMethod());
        self::assertSame('http://example.com/test', (string) $request->getUri());
    }

    public function testCreateRequestException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$uri must be either one of string or '.UriInterface::class);
        $this->factory->createRequest('POST', null);
    }
}
