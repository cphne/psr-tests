<?php

namespace Cphne\PsrTests\Server;

use Cphne\PsrTests\Container\ServiceCainInterface;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Logger\StdoutLogger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Class RequestHandler.
 */
class RequestHandler implements \Psr\Http\Server\RequestHandlerInterface, ServiceCainInterface
{

    private array $middleware = [];

    private ResponseInterface $response;

    public function __construct(
        private Factory $factory,
        private StdoutLogger $logger
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->response = $this->factory->createResponse();
        try {
            foreach ($this->middleware as $middleware) {
                /* @var $middleware MiddlewareInterface */
                $this->response = $middleware->process($request, $this);
                if ($this->response->isProcessingFinished()) {
                    break;
                }
            }
            $this->sendResponse($this->response);
        } catch (\Throwable $exception) {
            $code = $exception->getCode();
            if ($code === 0) {
                $code = 500;
            }
            $content = sprintf(
                "<h1>%s - %s</h1><h2>%s</h2><hr>%s",
                $code,
                get_class($exception),
                $exception->getMessage(),
                $exception->getTraceAsString()
            );
            $response = $this->factory->createResponse($code)
                ->withBody($this->factory->createStream($content));
            $this->sendResponse($response);
        }

        return $this->response;
    }


    public function getChains(): array
    {
        return [self::TAG_MIDDLEWARE => "addMiddleware"];
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;
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

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }


}
