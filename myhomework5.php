<?php

require_once 'HttpStatusCodes.php';
const HOST_BASE_DIRECTORIES = [
    'student.shpp.me' => 'student',
    'another.shpp.me' => 'another'
];
const MY_DIRECTORY = '/Applications/XAMPP/xamppfiles/htdocs/';

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
        outputHttpResponse(getStatusCode($headers, $uri), "Found", $headers, getFileContent($headers, $uri));
    } catch (Exception $e) {
        outputHttpResponse($e->getCode(), $e->getMessage(), $headers, 'Error: ' . $e->getMessage());
    }
}

function getFileContent($headers, $uri)
{
    $host = getHost($headers);
    return file_get_contents(MY_DIRECTORY . $host . $uri);
}

function getStatusCode($headers, $uri)
{
    $host = getHost($headers);

    if (checkHost($host)) {
        throw new Exception("Bad request", HttpStatusCodes::BAD_REQUEST);
    }

    if (checkFileExist($host, $uri)) {
        throw new Exception("File not exist", HttpStatusCodes::FILE_NOT_EXISTS);
    }

    return HttpStatusCodes::OK;
}

function checkFileExist($host, $uri): bool
{
    return !file_exists(MY_DIRECTORY . $host . $uri);
}

function checkHost(mixed $host)
{
    return !in_array($host, HOST_BASE_DIRECTORIES);
}


function getHost($headers)
{

    foreach ($headers as $subarray) {

        if ($subarray[0] === 'Host') {

            return HOST_BASE_DIRECTORIES[$subarray[1]];
        }
    }
    throw new Exception("The Host not found in the headers", 404);
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
    $lines = explode(PHP_EOL, $string);
    $headers = [];
    $numberOfRows = count($lines);

    for ($i = 1; $i < $numberOfRows; $i++) {

        if (str_contains($lines[$i], ':')) {
            $headers[] = explode(": ", $lines[$i]);
        }
    }

    return $headers;
}

function getBody($string)
{
    if (str_contains($string, PHP_EOL . PHP_EOL)) {

        return explode(PHP_EOL . PHP_EOL, $string, 2)[1];
    }

    return '';

}

function getUri($string)
{
    return explode(" ", $string)[1];
}

function getMethod($string)
{

    return explode(" ", $string)[0];
}

$mystr = "GET /123  HTTP/1.1
Host: student.shpp.me
Accept: image/gif, image/jpeg, */*
Accept-Language: en-us
Accept-Encoding: gzip, deflate
User-Agent: Mozilla/4.0";

$http = parseTcpStringAsHttpRequest($mystr);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);