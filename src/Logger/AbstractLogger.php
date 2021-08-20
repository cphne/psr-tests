<?php


namespace Cphne\PsrTests\Logger;


use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

/**
 * Class AbstractLogger
 * @package Cphne\PsrTests\Logger
 */
abstract class AbstractLogger extends PsrAbstractLogger
{

    /**
     * @var array
     */
    private array $logLevels = [];

    /**
     * @inheritDoc
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->checkLevel($level);
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                // TODO Check Key is OK
                $replace['{' . $key . '}'] = $val;
            }
            $message = strstr($message, $replace);
        }
        $message = sprintf('[%s][%s] ', strtoupper($level), (new \DateTime())->getTimestamp()) . $message;
        $this->flushMessage($level, $message);
    }

    /**
     * @param string $message
     * @return mixed
     */
    abstract protected function flushMessage(string $level, string $message);

    /**
     * @param $level
     */
    private function checkLevel($level)
    {
        if (empty($this->logLevels)) {
            $reflection = new \ReflectionClass(LogLevel::class);
            $this->logLevels = $reflection->getConstants();
        }
        if (!in_array($level, $this->logLevels, true)) {
            throw new InvalidArgumentException("Log level " . $level . " is not definied.");
        }
    }
}
