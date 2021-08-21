<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Container;

/**
 * Interface ServiceCainInterface
 * @package Cphne\PsrTests\Container
 */
interface ServiceCainInterface
{

    public const TAG_MIDDLEWARE = 'middleware';

    public const TAG_LISTENER_PROVIDER = 'listenerProvider';

    /**
     * @return array
     */
    public function getChains(): array;
}
