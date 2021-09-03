<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Listeners;

use Cphne\PsrTests\Events\ExceptionEvent;
use Cphne\PsrTests\HTTP\Factory;

/**
 * Class ExceptionListener
 * @package Cphne\PsrTests\Listeners
 */
class ExceptionListener implements ListenerInterface
{

    /**
     * @param ExceptionEvent $exceptionEvent
     */
    public function handleExceptionEvent(ExceptionEvent $exceptionEvent): void
    {
        $factory = new Factory();
        $throwable = $exceptionEvent->getThrowable();
        $parameters = ['code' => $throwable->getCode(), 'message' => $throwable->getMessage()];
        $content = file_get_contents('src/Exceptions/Templates/throwable.html');
        foreach ($parameters as $key => $value) {
            $replacer = sprintf('{{%s}}', $key);
            $content = str_replace($replacer, (string)$value, $content);
        }
        $code = empty($exceptionEvent->getThrowable()->getCode()) ? 500 : $exceptionEvent->getThrowable()->getCode();
        $response = $exceptionEvent->getResponse()
            ->withStatus($code)
            ->withBody($factory->createStream($content));
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
