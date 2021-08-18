<?php

namespace Cphne\PsrTests\HTTP;

use Cphne\PsrTests\HTTP\Factory\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @internal
 * @coversNothing
 */
class ResponseTest extends TestCase
{
    private static StreamFactoryInterface $factory;

    public static function setUpBeforeClass(): void
    {
        self::$factory = new StreamFactory();
    }

    /**
     * @dataProvider getReasonPhraseProvider
     */
    public function testGetReasonPhrase(?string $actual, string $expected)
    {
        $response = new Response(
            self::$factory->createStream('Foo'),
            200,
            array(),
            $actual
        );
        self::assertEquals($expected, $response->getReasonPhrase());
    }

    public function getReasonPhraseProvider()
    {
        yield array('My Phrase', 'My Phrase');
        yield array(null, 'OK');
    }

    public function testGetStatusCode()
    {
        $response = new Response(self::$factory->createStream('Foo'), 404);
        self::assertEquals(404, $response->getStatusCode());
        $response = new Response(self::$factory->createStream('Foo'));
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @dataProvider withStatusProvider
     */
    public function testWithStatus(int $code, ?string $phrase, string $expectedPhrase)
    {
        $response = new Response(self::$factory->createStream('Foo'));
        $newResponse = $response->withStatus($code, $phrase);
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhrase());
        self::assertEquals($code, $newResponse->getStatusCode());
        self::assertEquals($expectedPhrase, $newResponse->getReasonPhrase());
    }

    /**
     * @return \Generator
     */
    public function withStatusProvider()
    {
        yield array(404, null, 'Not Found');
        yield array(500, 'This is an Error', 'This is an Error');
    }
}
