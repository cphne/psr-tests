<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Events;

/**
 * Class AbstractStoppableEvent
 * @package Cphne\PsrTests\Events
 */
class AbstractStoppableEvent implements \Psr\EventDispatcher\StoppableEventInterface
{

    private bool $propagationStopped = false;

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * @return $this
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;
        return $this;
    }
}
