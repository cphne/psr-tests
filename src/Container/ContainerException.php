<?php

namespace Cphne\PsrTests\Container;

use Throwable;

class ContainerException extends \Exception implements \Psr\Container\ContainerExceptionInterface
{
    public function __construct($message = '', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
