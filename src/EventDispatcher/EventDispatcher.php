<?php

declare(strict_types=1);

namespace Cphne\PsrTests\EventDispatcher;

use Cphne\PsrTests\Container\ServiceCainInterface;
use JetBrains\PhpStorm\ArrayShape;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class EventDispatcher
 * @package Cphne\PsrTests\EventDispatcher
 */
class EventDispatcher implements \Psr\EventDispatcher\EventDispatcherInterface, ServiceCainInterface
{

    private array $providers = [];

    /**
     * @inheritDoc
     */
    public function dispatch(object $event): object
    {
        $listeners = [];
        foreach ($this->providers as $provider) {
            /* @var ListenerProviderInterface $provider */
            $listeners[] = $provider->getListenersForEvent($event);
        }
        $listeners = array_merge([], ...$listeners);
        $method = sprintf('handle%s', $this->getClassName($event));
        foreach ($listeners as $listener) {
            $listener->$method($event);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }
        return $event;
    }

    #[ArrayShape([ServiceCainInterface::TAG_LISTENER_PROVIDER => 'string'])] public function getChains(): array
    {
        return [
            ServiceCainInterface::TAG_LISTENER_PROVIDER => 'addProvider'
        ];
    }

    public function addProvider(ListenerProviderInterface $listenerProvider): void
    {
        $this->providers[] = $listenerProvider;
    }

    /**
     * @param object $event
     * @return string
     */
    private function getClassName(object $event): string
    {
        $fqdn = get_class($event);
        $parts = explode("\\", $fqdn);
        return array_pop($parts);
    }
}
