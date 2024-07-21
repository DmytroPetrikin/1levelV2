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
    try{
        $statusCode = getStatusCode($headers, $uri, $body);
        $statusMessage = "Found";
    }catch (Exception $e){
        $statusCode = $e->getCode();
        $statusMessage = $e->getMessage();
    }

    $body = getBodyMessage($statusCode,$statusMessage);
    outputHttpResponse($statusCode, $statusMessage, $headers, $body);
}

function getBodyMessage($statuscode , $statusMessage){
    $color = 'green';
    if($statuscode != 200){
        $color = 'red';
    }
    return '<h1 style="color:' . $color . '">' . $statusMessage . '</h1>';
}


function getStatusCode($headers, $uri, $body)
{
    if (checkUri($uri) && checkContType($headers)) {//якщо неправильний урі або контент тайп

        throw new Exception("Bad Request", 400);
    }

    if (checkPassTxt()) {

        throw new Exception("Internal Server Error", 500);
    }

    if (checkLogin($body)) {

        throw new Exception("Not Found", 404);
    }

    if (checkPassword($body)) {//якщо неправильний  пароль

        throw new Exception("Unauthorized", 401);
    }

    return 200;
}

function checkPassword($body)
{
    $password = getPassword($body);
    $login = getLogin($body);
    $data = file_get_contents(FILE);

    if (str_contains($data, $login)) {
        $firstValuePassword = explode($login . ':', $data)[1];
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

function checkUri($uri)
{

    return $uri !== MY_URI;
}

function checkContType($headers){
    $contType = getContType($headers);

    return $contType !== MY_CONT_TYPE;
}

function getContType($headers)
{

    foreach ($headers as $subarray) {

        if ($subarray[0] === "Content-Type") {

            return $subarray[1];
        }
    }
    throw new Exception("The Content-Type was not found in the headers.");
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