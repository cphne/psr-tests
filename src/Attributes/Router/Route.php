<?php


namespace Cphne\PsrTests\Attributes\Router;

/**
 * Class Route
 * @package Cphne\PsrTests\Attributes\Router
 */
#[\Attribute]
class Route
{
    /**
     * Route constructor.
     * @param string $route
     */
    public function __construct(
        private string $route
    ) {
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }


}
