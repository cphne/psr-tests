<?php

namespace HTTP;

use Cphne\PsrTests\HTTP\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UriTest extends TestCase
{
    public function testGetScheme()
    {
        $uri = $this->getSut(scheme: "HTTP");
        self::assertSame('HTTP', $uri->getScheme());
    }

    public function testAuthority()
    {
        $uri = $this->getSut(scheme: "http", user: "user", host: "host", port: "80");
        $expected = "user@host";
        self::assertSame($expected, $uri->getAuthority());
        $uri = $this->getSut(scheme: "ssh", user: "user", host: "host", port: "80");
        $expected = "user@host:80";
        self::assertSame($expected, $uri->getAuthority());
        $uri = $this->getSut(scheme: "ssh", user: "user", host: "host");
        $expected = "user@host";
        self::assertSame($expected, $uri->getAuthority());
    }

    public function testGetUserInfo()
    {
        $uri = $this->getSut();
        self::assertSame("", $uri->getUserInfo());
        $uri = $this->getSut(user: "user");
        self::assertSame("user", $uri->getUserInfo());
        $uri = $this->getSut(user: "user", pass: "pass");
        self::assertSame("user:pass", $uri->getUserInfo());
    }

    /**
     * @dataProvider getHostProvider
     *
     * @param string $expected
     * @param string|null $actual
     */
    public function testGetHost(string $expected, ?string $actual)
    {
        $uri = $this->getSut(host: $actual);
        self::assertSame($expected, $uri->getHost());
    }

    public function getHostProvider()
    {
        yield ["", ""];
        yield ["example.com", "example.com"];
        yield ["example.com", "ExamPle.COM"];
    }

    /**
     * @dataProvider getPortProvider
     *
     * @param string $scheme
     * @param int|null $port
     * @param int|null $expected
     */
    public function testGetPort(string $scheme, ?int $port, ?int $expected)
    {
        $uri = $this->getSut(scheme: $scheme, port: $port);
        self::assertSame($expected, $uri->getPort());
    }

    /**
     * @return \Generator
     */
    public function getPortProvider()
    {
        yield ["HTTP", 80, null];
        yield ["SSH", 80, 80];
        yield ["HTTP", null, null];
    }

    /**
     * @dataProvider getPathProvider
     *
     * @param string $actual
     * @param string $expected
     */
    public function testGetPath(string $actual, string $expected)
    {
        $uri = $this->getSut(path: $actual);
        self::assertSame($expected, $uri->getPath());
    }

    public function getPathProvider()
    {
        yield ["", ""];
        yield ["/", "/"];
        yield ["this/is/my/path", "this/is/my/path"];
    }

    public function testGetQuery()
    {
        $uri = $this->getSut(query: "id=1&foo=bar");
        self::assertSame("id=1&foo=bar", $uri->getQuery());
    }

    public function testWithScheme()
    {
        $uri = $this->getSut(scheme: "http");
        $newUri = $uri->withScheme("ssh");
        self::assertSame("http", $uri->getScheme());
        self::assertSame("ssh", $newUri->getScheme());
    }

    /**
     * @dataProvider withUserInfoProvider
     *
     * @param string|null $user
     * @param string|null $pass
     * @param string $expected
     */
    public function testWithUserInfo(?string $user, ?string $pass, string $expected)
    {
        $uri = $this->getSut(user: "user", pass: "pass");
        $newUri = $uri->withUserInfo($user, $pass);
        self::assertSame("user:pass", $uri->getUserInfo());
        self::assertSame($expected, $newUri->getUserInfo());
    }

    public function withUserInfoProvider()
    {
        yield ["foo", null, "foo"];
        yield ["foo", "bar", "foo:bar"];
        yield [null, null, ""];
    }

    /**
     * @dataProvider withHostProvider
     *
     * @param string $actual
     * @param string $expected
     */
    public function testWithHost(string $actual, string $expected)
    {
        $uri = $this->getSut(host: "example.com");
        $newUri = $uri->withHost($actual);
        self::assertSame("example.com", $uri->getHost());
        self::assertSame($expected, $newUri->getHost());
    }

    public function withHostProvider()
    {
        yield ["test.foo", "test.foo"];
        yield ["", ""];
    }

    /**
     * @dataProvider withPortProvider
     *
     * @param int|null $actual
     * @param int|null $expected
     */
    public function testWithPort(?int $actual, ?int $expected)
    {
        $uri = $this->getSut(port: 22);
        $newUri = $uri->withPort($actual);
        self::assertSame(22, $uri->getPort());
        self::assertSame($expected, $newUri->getPort());
    }

    public function withPortProvider()
    {
        yield [80, 80];
        yield [null, null];
    }

    public function testWithPortException()
    {
        $uri = $this->getSut(port: 443);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Port must be inside the established range.");
        $uri->withPort(70000);
    }

    // TODO test other Methods

    public function testToString()
    {
        $uri = new Uri(
            "https",
            "user",
            "pass",
            "test.com",
            443,
            "/test/path",
            "id=1",
            "frag"
        );
        self::assertSame("https://user:pass@test.com:443/test/path?id=1#frag", (string) $uri);
    }


    /**
     * Get Subject under test.
     *
     * @param string $scheme
     * @param string|null $user
     * @param string|null $pass
     * @param string $host
     * @param string|null $port
     * @param string $path
     * @param string|null $query
     * @param string|null $fragment
     * @return Uri
     */
    private function getSut(
        string $scheme = '',
        ?string $user = null,
        ?string $pass = null,
        string $host = '',
        ?string $port = null,
        string $path = '',
        ?string $query = null,
        ?string $fragment = null
    ) {
        return new Uri($scheme, $user, $pass, $host, $port, $path, $query, $fragment);
    }
}
