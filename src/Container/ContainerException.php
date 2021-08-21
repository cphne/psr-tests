<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Container;

use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class ContainerException
 * @package Cphne\PsrTests\Container
 */
class ContainerException extends \Exception implements \Psr\Container\ContainerExceptionInterface
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining.
     */
    #[Pure] public function __construct($message = '', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
