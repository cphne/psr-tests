<?php

namespace Cphne\PsrTests\Server;

use Cphne\PsrTests\HTTP\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestHandler.
 */
class RequestHandler implements \Psr\Http\Server\RequestHandlerInterface
{

    public function __construct(
        private Factory $factory
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->factory->createResponse();
        $response = $response->withBody(
            $this->factory->createStream(sprintf('<p>%s</p>', "ok!"))
        )
            ->withAddedHeader('Content-Type', 'text/html');

        $this->sendResponse($response);

        return $response;
    }

    private function sendResponse(ResponseInterface $response): bool
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            header($name . ': ' . implode(', ', $value), true, $response->getStatusCode());
        }
        echo (string)$response->getBody();

        return true;
    }
}
