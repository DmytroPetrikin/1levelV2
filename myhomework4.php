<?php

require_once 'HttpStatusCodes.php';
require_once "ParserRequest.php";
require_once "User.php";
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

function outputHttpResponse($statusCode, $statusMessage)
{
    $body = getBodyMessage($statusCode, $statusMessage);
    $response = "HTTP/1.1 $statusCode $statusMessage" . PHP_EOL;
    $response .= "Date: " . date("l, d F  Y h:i:sa") . PHP_EOL;
    $response .= "Server: Apache/2.2.14 (Win32)" . PHP_EOL;
    $response .= "Content-Length: " . strlen($body) . PHP_EOL;
    $response .= "Connection: Closed" . PHP_EOL;
    $response .= "Content-Type: text/html; charset=utf-8" . PHP_EOL;
    $response .= PHP_EOL . $body;

    echo $response;
}

function processHttpRequest($method, $uri, $headers, $body)
{

    try {
        $user = User:: createUser($body);

        if (!checkUri($uri) || !checkContType($headers)) {//якщо неправильний урі або контент тайп
            throw new Exception("Bad Request", HttpStatusCodes::BAD_REQUEST);
        }

        if (!checkPassTxt()) {
            throw new Exception("Internal Server Error", HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }

        if (!checkLogin($user)) {
            throw new Exception("Bad Request", HttpStatusCodes::BAD_REQUEST);
        }

        if (!checkPassword($user)) {//якщо неправильний  пароль
            throw new Exception("Unauthorized", HttpStatusCodes::UNAUTHORIZED);
        }
        outputHttpResponse(HttpStatusCodes::OK, "Found");
    } catch (Exception $e) {
        outputHttpResponse($e->getCode(), $e->getMessage());
    }
}

function getBodyMessage($statuscode, $statusMessage)
{
    $color = ($statuscode == HttpStatusCodes::OK) ? "green" : "red";

    return '<h1 style="color:' . $color . '">' . $statusMessage . '</h1>';
}

function checkPassword($user)
{

    return $user->getPassword() === $user->getUserData()['password'];
}

function checkPassTxt()
{

    return file_exists(FILE);
}

function checkLogin($user)
{
    $user->searchUserData(FILE);

//якщо масив порожній відповідно функція searchUserData не знайшла відповідний логін в файлі
    return !empty($user->getUserData());
}

function checkUri($uri)
{

    return $uri === MY_URI;
}

function checkContType($headers)
{

    return getContType($headers) === MY_CONT_TYPE;
}

function getContType($headers)
{

    foreach ($headers as $header) {
        $header = explode(': ', $header, 2);

        if ($header[0] === "Content-Type") {

            return $header[1];
        }
    }

    throw new Exception("The Content-Type was not found in the headers.", HttpStatusCodes::BAD_REQUEST);
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

$testString = "POST /api/checkLoginAndPassword HTTP/1.1
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: 35

login=login1&password=1";

$http = parseTcpStringAsHttpRequest($testString);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);