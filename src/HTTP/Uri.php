<?php

declare(strict_types=1);

namespace Cphne\PsrTests\HTTP;

use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 * @package Cphne\PsrTests\HTTP
 */
class Uri implements UriInterface
{

    private array $portMapping = [
        "acap" => 674,
        "afp" => 548,
        "dict" => 2628,
        "dns" => 53,
        "file" => null,
        "ftp" => 21,
        "git" => 9418,
        "gopher" => 70,
        "http" => 80,
        "https" => 443,
        "imap" => 143,
        "ipp" => 631,
        "ipps" => 631,
        "irc" => 194,
        "ircs" => 6697,
        "ldap" => 389,
        "ldaps" => 636,
        "mms" => 1755,
        "msrp" => 2855,
        "msrps" => null,
        "mtqp" => 1038,
        "nfs" => 111,
        "nntp" => 119,
        "nntps" => 563,
        "pop" => 110,
        "prospero" => 1525,
        "redis" => 6379,
        "rsync" => 873,
        "rtsp" => 554,
        "rtsps" => 322,
        "rtspu" => 5005,
        "sftp" => 22,
        "smb" => 445,
        "snmp" => 161,
        "ssh" => 22,
        "steam" => null,
        "svn" => 3690,
        "telnet" => 23,
        "ventrilo" => 3784,
        "vnc" => 5900,
        "wais" => 210,
        "ws" => 80,
        "wss" => 443,
        "xmpp" => null
    ];

    /**
     * Uri constructor.
     * @param string $scheme
     * @param string|null $user
     * @param string|null $pass
     * @param string $host
     * @param int|null $port
     * @param string $path
     * @param string|null $query
     * @param string|null $fragment
     */
    public function __construct(
        protected string $scheme,
        protected ?string $user,
        protected ?string $pass,
        protected string $host,
        protected ?int $port,
        protected string $path,
        protected ?string $query,
        protected ?string $fragment
    ) {
    }

    /**
     * @param array $server
     * @return static
     */
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
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        $authority = $this->user . '@' . $this->host; // . ':' . $this->port;
        if ($this->isStandardPort()) {
            return $authority;
        }
        return $authority . ":" . $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        $userInfo = $this->user ?? "";
        if (!empty($this->pass)) {
            $userInfo .= ":" . $this->pass;
        }
        return $userInfo;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getHost(): string
    {
        return strtolower($this->host) ?? "";
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        return ($this->isStandardPort()) ? null : $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query ?? "";
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function withScheme($scheme): static
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
    #[Pure] public function withUserInfo($user, $password = null): static
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
    #[Pure] public function withHost($host): static
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
    public function withPort($port): static
    {
        if ($port < 0 || $port > 65353) {
            throw new \InvalidArgumentException("Port must be inside the established range.");
        }
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
    #[Pure] public function withPath($path): static
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
    #[Pure] public function withQuery($query): static
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
    #[Pure] public function withFragment($fragment): static
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
    #[Pure] public function __toString(): string
    {
        $uri = $this->scheme . "://";
        $userInfo = $this->getUserInfo();
        $uri .= $userInfo . ((!empty($userInfo)) ? "@" : "");
        $uri .= $this->host;
        $uri .= (!empty($this->port)) ? ":" . $this->port : "";
        $uri .= !str_starts_with($this->path, "/") && !empty($this->path) ? "/" : "";
        $uri .= $this->path;
        $uri .= (!empty($this->query)) ? "?" . $this->query : "";
        $uri .= (!empty($this->fragment)) ? "#" . $this->fragment : "";
        return $uri;
    }

    #[Pure] protected static function getFullUri(array $server): string
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

    #[Pure] protected function isStandardPort(): bool
    {
        return !empty($this->port) && !empty($this->scheme) && $this->portMapping[strtolower(
            $this->scheme
        )] === $this->port;
    }
}
