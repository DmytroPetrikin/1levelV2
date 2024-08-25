<?php

require_once 'classes/HttpStatusCodes.php';
require_once 'classes/ParserRequest.php';
const HOST_BASE_DIRECTORIES = [
    'student.shpp.me' => 'resources/student',
    'another.shpp.me' => 'resources/another'
];
const MY_DIRECTORY = '/Applications/XAMPP/xamppfiles/htdocs/1levelV2/';

//function readHttpLikeInput()
//{
//    $f = fopen('php://stdin', 'r');
//    $store = "";
//    $toread = 0;
//    while ($line = fgets($f)) {
//        $store .= preg_replace("/\r/", "", $line);
//        if (preg_match('/Content-Length: (\d+)/', $line, $m))
//            $toread = $m[1] * 1;
//        if ($line == "\r\n")
//            break;
//    }
//    if ($toread > 0)
//        $store .= fread($f, $toread);
//    return $store;
//}
//
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
        $nameFolder = getFolderByHost($headers);

        if (checkFileMissing($nameFolder, $uri)) {
            throw new Exception("Bad request", HttpStatusCodes::BAD_REQUEST);
        }

        outputHttpResponse(HttpStatusCodes::OK, "Found", $headers, getFileContent($nameFolder, $uri));
    } catch (Exception $e) {
        outputHttpResponse($e->getCode(), $e->getMessage(), $headers, 'Error: ' . $e->getMessage());
    }
}

function getFileContent($nameFolder, $uri)
{
    return file_get_contents(MY_DIRECTORY . $nameFolder . $uri);
}

function checkFileMissing($nameFolder, $uri): bool
{
    return !file_exists(MY_DIRECTORY . $nameFolder . $uri);
}

function getFolderByHost($headers)
{
    foreach ($headers as $header) {
        $header = explode(': ', $header, 2);

        if ($header[0] === "Host") {

            return HOST_BASE_DIRECTORIES[$header[1]];
        }
    }
}


function parseTcpStringAsHttpRequest($string)
{
    $parser = ParserRequest:: ParseRequest($string);

    return [
        "method" => $parser->getMethod(),
        "uri" => $parser->getUri(),
        "headers" => $parser->getHeaders(),
        "body" => $parser->getBody(),
    ];
}

$mystr = "GET /1  HTTP/1.1
Host: student.shpp.me
Accept: image/gif, image/jpeg, */*
Accept-Language: en-us
Accept-Encoding: gzip, deflate
User-Agent: Mozilla/4.0";

$http = parseTcpStringAsHttpRequest($mystr);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);