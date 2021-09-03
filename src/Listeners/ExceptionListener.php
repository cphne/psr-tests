<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Listeners;

use Cphne\PsrTests\Events\ExceptionEvent;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\HTTP\Stream;

/**
 * Class ExceptionListener
 * @package Cphne\PsrTests\Listeners
 */
class ExceptionListener implements ListenerInterface
{

    public function handleExceptionEvent(ExceptionEvent $exceptionEvent) {
        $factory = new Factory();
        var_dump($exceptionEvent->getThrowable()->getMessage());
        var_dump($exceptionEvent->getThrowable()->getTrace());
        $code = empty($exceptionEvent->getThrowable()->getCode()) ? 500 : $exceptionEvent->getThrowable()->getCode();
        $response = $exceptionEvent->getResponse()
            ->withStatus($code)
            ->withBody($factory->createStream($exceptionEvent->getThrowable()->getTraceAsString()));
        $exceptionEvent->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class];
    }
}
