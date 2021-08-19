<?php

namespace Cphne\PsrTests\Exceptions;

use Throwable;

/**
 * Class NotImplementedException.
 */
class NotImplementedException extends \RuntimeException
{
    /**
     * NotImplementedException constructor.
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct($message = 'Feature is not implemented yet.', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
