<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Exceptions\HTTP;

use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class NotFoundException
 * @package Cphne\PsrTests\Exceptions\HTTP
 */
class NotFoundException extends \Exception implements HttpExceptionInterface
{
    /**
     * NotFoundException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct($message = 'Not Found.', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
