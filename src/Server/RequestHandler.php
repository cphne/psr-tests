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
    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $factory = new Factory();
        $response = $factory->createResponse(200);
        $response = $response->withBody(
            $factory->createStream(json_encode(['this' => 'is', 'a' => 'success']))
        )
            ->withAddedHeader('Content-Type', 'application/json')
        ;

        $this->sendResponse($response);

        return $response;
    }

    private function sendResponse(ResponseInterface $response): bool
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            header($name.': '.implode(', ', $value), true, $response->getStatusCode());
        }
        echo (string) $response->getBody();

        return true;
    }
}
