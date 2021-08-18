<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use Cphne\PsrTests\HTTP\Request;
use Cphne\PsrTests\HTTP\Response;

$factory = new \Cphne\PsrTests\HTTP\Factory\StreamFactory();
$path = $_SERVER['SCRIPT_FILENAME'];
$request = new Request($_SERVER, getallheaders(), $factory->createStream(''));
$headers = array(//"Content-Type" => "application/json"
);
$response = new Response(
    $factory->createStream(json_encode(array('ok' => 'yes!', 'no' => 'fine!', 'really' => array(1, 2, 3, 4)), JSON_THROW_ON_ERROR)),
    404,
    $headers,
    'Not Found'
);
$response->send();
