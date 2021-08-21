<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Server;

use Cphne\PsrTests\Container\ServiceCainInterface;
use Cphne\PsrTests\EventDispatcher\EventDispatcher;
use Cphne\PsrTests\Events\RequestEvent;
use Cphne\PsrTests\Events\ResponseEvent;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Logger\StdoutLogger;
use JetBrains\PhpStorm\ArrayShape;
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
        private StdoutLogger $logger,
        private EventDispatcher $dispatcher,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new RequestEvent($request));
        $this->response = $this->factory->createResponse();
        try {
            foreach ($this->middleware as $middleware) {
                /* @var MiddlewareInterface $middleware */
                $this->response = $middleware->process($request, $this);
                if ($this->response->isProcessingFinished()) {
                    break;
                }
            }
            $this->dispatcher->dispatch(new ResponseEvent($this->response));
            $this->sendResponse($this->response);
        } catch (\Throwable $exception) {
            $code = $exception->getCode();
            if ($code === 0) {
                $code = 500;
            }
            $content = sprintf(
                '<h1>%s - %s</h1><h2>%s</h2><hr>%s',
                $code,
                get_class($exception),
                $exception->getMessage(),
                $exception->getTraceAsString()
            );
            $response = $this->factory->createResponse($code)
                ->withBody($this->factory->createStream($content));
            $this->dispatcher->dispatch(new ResponseEvent($response));
            $this->sendResponse($response);
        }

        return $this->response;
    }


    #[ArrayShape([self::TAG_MIDDLEWARE => 'string'])] public function getChains(): array
    {
        return [self::TAG_MIDDLEWARE => 'addMiddleware'];
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
