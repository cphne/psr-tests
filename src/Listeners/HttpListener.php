<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Listeners;

use Cphne\PsrTests\Events\RequestEvent;
use Cphne\PsrTests\Events\ResponseEvent;
use Cphne\PsrTests\Logger\StdoutLogger;

/**
 * Class HttpListener
 * @package Cphne\PsrTests\Listeners
 */
class HttpListener implements ListenerInterface
{
    /**
     * HttpListener constructor.
     * @param StdoutLogger $logger
     */
    public function __construct(private StdoutLogger $logger)
    {
    }

    /**
     * @param RequestEvent $requestEvent
     */
    public function handleRequestEvent(RequestEvent $requestEvent): void
    {
        $this->logger->info(__METHOD__);
    }

    /**
     * @param ResponseEvent $responseEvent
     */
    public function handleResponseEvent(ResponseEvent $responseEvent): void
    {
        $this->logger->info(__METHOD__);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class, ResponseEvent::class];
    }


}
