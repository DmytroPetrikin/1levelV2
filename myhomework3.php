<?php
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
        $statuscode = getStatusCode($method, $uri);
        $statusmessage = "OK";
        $body = getResult($uri);
    } catch (Exception $ex) {
        $statuscode = $ex->getCode();
        $statusmessage = $ex->getMessage();
        $body = $statusmessage;
    }

    outputHttpResponse($statuscode, $statusmessage, $headers, $body);
}

function getResult($uri)
{
    $sumQueryAndNumbers = explode('=', $uri);
    $nums = explode(',', $sumQueryAndNumbers[1]);

    return array_sum($nums);
}

function getStatusCode($method, $uri)
{

    if (explode("?", $uri)[0] != "/sum") {

        throw new Exception("Not Found", 404);
    }
    if (!str_contains($uri, '?nums=') || !str_contains($method, 'GET')) {

       throw new Exception("Bad Request", 400);
    }

    return 200;
}


function parseTcpStringAsHttpRequest($string)
{

    return [
        "method" => getMethod($string),
        "uri" => getUri($string),
        "headers" => getHeaders($string),
        "body" => getBody($string),
        ];
}

function getHeaders($string)
{
    $lines = explode("\n", $string);
    $headers = [];

    for ($i = 1; $i < sizeof($lines); $i++) {

        if (str_contains($lines[$i], ':')) {
            $headers[] = explode(": ", $lines[$i]);
        }
    }

    return $headers;
}

function getBody($string)
{
    $lines = explode(PHP_EOL, $string);
    $body = end($lines);

    if (!strpos($body, ":")) {

        return $body;
    }

    return "";
}

function getUri($string)
{

    return explode(" ", $string)[1];
}

function getMethod($string)
{

    return explode(" ", $string)[0];
}

$mystr = "GET /sum?nums=1,2,5 HTTP/1.1
Host: student.shpp.me
";

$http = parseTcpStringAsHttpRequest($mystr);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);