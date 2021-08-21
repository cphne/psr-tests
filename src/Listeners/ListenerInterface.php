<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Listeners;

/**
 * Interface ListenerInterface
 * @package Cphne\PsrTests\Listeners
 */
interface ListenerInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array;
}
