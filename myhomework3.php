<?php

require_once 'classes/HttpStatusCodes.php';
require_once "classes/ParserRequest.php";

//function readHttpLikeInput()
//{
//    $f = fopen('php://stdin', 'r');
//    $store = "";
//    $toread = 0;
//
//    while ($line = fgets($f)) {
//        $store .= preg_replace("/\r/", "", $line);
//
//        if (preg_match('/Content-Length: (\d+)/', $line, $m))
//            $toread = $m[1] * 1;
//
//        if ($line === PHP_EOL)
//
//            break;
//    }
//
//    if ($toread > 0)
//        $store .= fread($f, $toread);
//
//    return $store;
//}

//$contents = readHttpLikeInput();

function outputHttpResponse($statuscode, $statusmessage, $headers, $body)
{
    $response = "HTTP/1.1 $statuscode $statusmessage" . PHP_EOL;
    $response .= "Date: " . date("l, d F  Y h:i:sa") . PHP_EOL;
    $response .= "Server: Apache/2.2.14 (Win32)" . PHP_EOL;
    $response .= "Content-Length: " . strlen($body) . PHP_EOL;
    $response .= "Connection: Closed" . PHP_EOL;
    $response .= "Content-Type: text/html; charset=utf-8" . PHP_EOL;
    $response .= PHP_EOL . "$body";

    echo $response;
}

function processHttpRequest($method, $uri, $headers, $body)
{

    try {

        if (explode("?", $uri)[0] != "/sum") {
            throw new Exception("Not Found", HttpStatusCodes::NOT_FOUND);
        }

        if (!str_contains($uri, '?nums=') || !str_contains($method, 'GET')) {
            throw new Exception("Bad Request", HttpStatusCodes::BAD_REQUEST);
        }
        outputHttpResponse(HTTPStatusCodes::OK, "OK", $headers, getResult($uri));
    } catch (Exception $ex) {
        outputHttpResponse($ex->getCode(), $ex->getMessage(), $headers, $ex->getMessage());
    }
}

function getResult($uri)
{
    $sumQueryAndNumbers = explode('=', $uri);
    $nums = explode(',', $sumQueryAndNumbers[1]);

    return array_sum($nums);
}

function parseTcpStringAsHttpRequest($string)
{
    $parseRequest = ParserRequest::parseRequest($string);

    return [
        "method" => $parseRequest->getMethod(),
        "uri" => $parseRequest->getUri(),
        "headers" => $parseRequest->getHeaders(),
        "body" => $parseRequest->getBody(),
    ];
}

$mystr = "GET /sum?nums=1,2,5 HTTP/1.1
Host: student.shpp.me
";

$http = parseTcpStringAsHttpRequest($mystr);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);