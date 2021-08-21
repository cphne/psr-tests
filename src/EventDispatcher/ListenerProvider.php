<?php

declare(strict_types=1);

namespace Cphne\PsrTests\EventDispatcher;

use Cphne\PsrTests\Container\Container;
use Cphne\PsrTests\Services\Finder\ClassFinder;

/**
 * Class ListenerProvider
 * @package Cphne\PsrTests\EventDispatcher
 */
class ListenerProvider implements \Psr\EventDispatcher\ListenerProviderInterface
{
    private array $mapping = [];

    /**
     * ListenerProvider constructor.
     * @param ClassFinder $finder
     * @param Container $container
     */
    public function __construct(ClassFinder $finder, private Container $container)
    {
        $listeners = $finder->find('Listeners');
        foreach ($listeners as $listener) {
            $this->mapping[$listener] = $listener::getSubscribedEvents();
        }
    }


    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = get_class($event);
        $listeners = [];
        foreach ($this->mapping as $listener => $subscribedEvents) {
            if (in_array($eventClass, $subscribedEvents, true)) {
                if (!$this->container->has($listener)) {
                    throw new \RuntimeException('Listener should be a Service!');
                }
                $listeners[] = $this->container->get($listener);
            }
        }
        return $listeners;
    }
}
