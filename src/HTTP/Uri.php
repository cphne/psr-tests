<?php


namespace Cphne\PsrTests\HTTP;


use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * Uri constructor.
     * @param string $scheme
     * @param string|null $user
     * @param string|null $pass
     * @param string $host
     * @param string $port
     * @param string $path
     * @param string|null $query
     * @param string|null $fragment
     */
    public function __construct(
        protected string $scheme,
        protected ?string $user,
        protected ?string $pass,
        protected string $host,
        protected string $port,
        protected string $path,
        protected ?string $query,
        protected ?string $fragment
    ) {
    }

    public static function fromServer(array $server)
    {
        $uri = self::getFullUri($server);
        $parsedUrl = parse_url($uri);
        return new static(
            $parsedUrl["scheme"],
            $parsedUrl["user"] ?? null,
            $parsedUrl["pass"] ?? null,
            $parsedUrl["host"],
            $parsedUrl["port"],
            $parsedUrl["path"],
            $parsedUrl["query"] ?? null,
            $parsedUrl["fragment"] ?? null
        );
    }


    /**
     * @inheritDoc
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority()
    {
        return $this->user . '@' . $this->host . ':' . $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo()
    {
        return $this->user . (!empty($this->pass)) ? ":" . $this->pass : "";
    }

    /**
     * @inheritDoc
     */
    public function getHost()
    {
        return strtolower($this->host) ?? "";
    }

    /**
     * @inheritDoc
     */
    public function getPort()
    {
        return $this->port ?? "";
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery()
    {
        return $this->query ?? "";
    }

    /**
     * @inheritDoc
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withScheme($scheme)
    {
        return new static(
            $scheme,
            $this->user,
            $this->pass,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = null)
    {
        return new static(
            $this->scheme,
            $user,
            $password,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function withHost($host)
    {
        return new static(
            $this->scheme,
            $this->user,
            $this->pass,
            $host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function withPort($port)
    {
        return new static(
            $this->scheme,
            $this->user,
            $this->pass,
            $this->host,
            $port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function withPath($path)
    {
        return new static(
            $this->scheme,
            $this->user,
            $this->pass,
            $this->host,
            $this->port,
            $path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query)
    {
        return new static(
            $this->scheme,
            $this->user,
            $this->pass,
            $this->host,
            $this->port,
            $this->path,
            $query,
            $this->fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function withFragment($fragment)
    {
        return new static(
            $this->scheme,
            $this->user,
            $this->pass,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $fragment
        );
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->scheme . "\\" .
            $this->host . ":" . $this->port . "/" .
            $this->path . "?" . $this->query . '#' .
            $this->fragment;
    }

    protected static function getFullUri(array $server): string
    {
        $use_forwarded_host = true;
        $ssl = (!empty($server['HTTPS']) && $server['HTTPS'] === 'on');
        $sp = strtolower($server['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $server['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($use_forwarded_host && isset($server['HTTP_X_FORWARDED_HOST'])) ? $server['HTTP_X_FORWARDED_HOST'] : (isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : null);
        $host = $host ?? ($server['SERVER_NAME'] . $port);
        return $protocol . '://' . $host . $server['REQUEST_URI'];
    }
}
