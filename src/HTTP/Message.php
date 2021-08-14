<?php


namespace Cphne\PsrTests\HTTP;


use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{

    use MessageTrait;

    // SERVER constants
    public const SERVER_PROTOCOL_VERSION = "SERVER_PROTOCOL";


    public function __construct(array $server = [], array $headers = [], StreamInterface|string|null $body = null)
    {
        $this->server = $server;
        $this->setProtocolVersion($server[self::SERVER_PROTOCOL_VERSION]);
        $this->headers = (!empty($headers)) ? $headers : getallheaders(
        ); // TODO each value MUST be an array of strings for that header.
        $this->setBody($body);
    }


}
