<?php

namespace Cphne\PsrTests\Services\Deserializer;

use Throwable;

class DeserializerException extends \Exception implements DeserializerExceptionInterface
{
    public function __construct($message = '', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
