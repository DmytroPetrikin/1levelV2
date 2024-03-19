<?php

const MY_URI = "/api/checkLoginAndPassword";
const MY_CONT_TYPE = "application/x-www-form-urlencoded";
const FILE = "passwords";
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
    $statuscode = getStatusCode($headers, $uri, $body);
    outputHttpResponse($statuscode, getStatusMessage($statuscode), $headers, getResult($statuscode));
}

function getResult($statuscode)
{
    return match ($statuscode) {
        '200' => '<h1 style="color:green">FOUND</h1>',
        '400' => '<h1 style="color:red">NOT FOUND</h1>',
        '404' => '<h1 style="color:red">NOT FOUND</h1>',
        '401' => '<h1 style="color:red">WRONG PASSWORD</h1>',
        '500' => '<h1 style="color:red">INTERNAL SERVER ERROR</h1>',
        default => "Unknown Status",
    };
}

function getStatusMessage($statuscode)
{
    return match ($statuscode){
        '200' => 'Ok',
        '400' => 'Bad Request',
        '404' => 'Not Found',
        '500' => 'Internal Server Error',
        default => 'Unknown Status',
    };
}

function getStatusCode($headers, $uri, $body)
{
    if (checkUriEndContentType($uri, $headers)) {//якщо неправильний урі або контент тайп

        return "400";
    }
    if (checkPassTxt()) {

        return "500";
    }
    if (checkLogin($body)) {

        return "404";
    }
    if (checkPassword($body)) {//якщо неправильний  пароль

        return "401";
    }

    return "200";
}

function checkPassword($body)
{
    $password = getPassword($body);
    $log = getLogin($body);
    $data = file_get_contents(FILE);

    if (str_contains($data, $log)) {
        $firstValuePassword = explode($log . ':', $data)[1];
        $truePassword = explode(PHP_EOL, $firstValuePassword)[0];

        return $truePassword !== $password;
    }

    return true;
}


function getPassword($body)
{

    return explode('password=', $body)[1];
}

function checkPassTxt()
{

    return !file_exists(FILE);
}

function checkLogin($body)
{
    $data = file_get_contents(FILE);

    return !str_contains($data, getLogin($body) . ':');
}

function getLogin($body)
{
    $logPass = explode('login=', $body)[1];

    return explode('&password=', $logPass)[0];
}

function checkUriEndContentType($uri, $headers)
{
    $contType = getContType($headers);

    return $uri !== MY_URI || $contType !== MY_CONT_TYPE;
}

class ContentTypeNotFoundException extends Exception
{
}

function getContType($headers)
{

    foreach ($headers as $subarray) {

        if ($subarray[0] === "Content-Type") {

            return $subarray[1];
        }
    }
    throw new ContentTypeNotFoundException("The Content-Type was not found in the headers.");
}

function start(array $explode)
{
    return $explode[0];
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

    for ($i = 1; $i < sizeof($lines); $i++) {

        if (str_contains($lines[$i], ':')) {
            $headers[] = explode(': ', $lines[$i]);
        }
    }

    return $headers;
}

function getBody($string)
{

    return explode(PHP_EOL . PHP_EOL, $string, 2)[1];
}

function getUri($string)
{

    return explode(" ", $string)[1];
}

function getMethod($string)
{

    return explode(" ", $string)[0];
}

$testString = "POST /api/checkLoginAndPassword HTTP/1.1
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: 35

login=login1&password=1";

$http = parseTcpStringAsHttpRequest($testString);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);