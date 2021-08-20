<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$kernel = new \Cphne\PsrTests\Kernel\ApplicationKernel();
$kernel->boot();
$kernel->run();
$kernel->cleanup();
