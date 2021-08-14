<?php

require 'vendor/autoload.php';


use Cphne\PsrTests\HTTP\Request;
use Cphne\PsrTests\HTTP\Response;
use Cphne\PsrTests\Integers\AlwaysAddOne;
$path = $_SERVER["SCRIPT_FILENAME"];
$request = new Request($_SERVER);
$headers = [
    #"Content-Type" => "application/json"
];
$response = new Response(json_encode(["ok" => "yes!", "no" => "fine!", "really" => [1,2,3,4]], JSON_THROW_ON_ERROR), 404, $headers, "Not Found");
$response->send();
// set headers
// set content
return;
var_dump($request);
return;
