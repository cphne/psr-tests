<?php


namespace Cphne\PsrTests\Container;


/**
 * Interface ServiceCainInterface
 * @package Cphne\PsrTests\Container
 */
interface ServiceCainInterface
{

    /**
     *
     */
    public const TAG_MIDDLEWARE = "middleware";

    /**
     * @return array
     */
    public function getChains(): array;
}
