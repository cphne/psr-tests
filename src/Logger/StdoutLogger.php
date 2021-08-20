<?php


namespace Cphne\PsrTests\Logger;


/**
 * Class StdoutLogger
 * @package Cphne\PsrTests\Logger
 */
class StdoutLogger extends AbstractLogger
{

    /**
     * @param string $message
     */
    protected function flushMessage(string $level, string $message)
    {
        $fh = fopen('php://stderr', "wb");
        fwrite($fh, $message . PHP_EOL);
    }
}
