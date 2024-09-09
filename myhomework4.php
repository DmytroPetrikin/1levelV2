<?php

require_once 'classes/HttpStatusCodes.php';
require_once "classes/ParserRequest.php";
require_once "classes/User.php";
require_once "classes/UserFileSearcher.php";
const MY_URI = "/api/checkLoginAndPassword";
const MY_CONT_TYPE = "application/x-www-form-urlencoded";
const FILE = "resources/passwords";

function outputHttpResponse($statusCode, $statusMessage)
{
    $body = getBodyMessage($statusCode, $statusMessage);
    $response = "HTTP/1.1 $statusCode $statusMessage" . PHP_EOL .
    "Date: " . date("l, d F  Y h:i:sa") . PHP_EOL .
    "Server: Apache/2.2.14 (Win32)" . PHP_EOL .
    "Content-Length: " . strlen($body) . PHP_EOL .
    "Connection: Closed" . PHP_EOL .
    "Content-Type: text/html; charset=utf-8" . PHP_EOL .
    PHP_EOL . $body;

    echo $response;
}

function processHttpRequest($request)
{
    try {
        $parserRequest = ParserRequest::parseRequest($request);

        if (isInvalidUri($parserRequest->getUri()) ||
            isInvalidContentType($parserRequest-> getHeaders())) {//якщо неправильний урі або контент тайп
            throw new Exception("Bad Request", HttpStatusCodes::BAD_REQUEST);
        }

        if (isPasswordFileMissing()) {
            throw new Exception("Internal Server Error", HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }

        $userFileSearcher = new UserFileSearcher(FILE);
        $user = $userFileSearcher->findUserByLogin($parserRequest->getParams('login'));

        if (isPasswordIncorrect($parserRequest->getParams('password'), $user->getPassword())) {//якщо неправильний  пароль
            throw new Exception("Unauthorized", HttpStatusCodes::UNAUTHORIZED);
        }

        outputHttpResponse(HttpStatusCodes::OK, "Found");
    } catch (Exception $e) {
        outputHttpResponse($e->getCode(), $e->getMessage());
    }
}

function isPasswordIncorrect($password, $hashedPassword)
{
    return !password_verify($password, $hashedPassword);
}

function getBodyMessage($statusCode, $statusMessage)
{
    $color = ($statusCode == HttpStatusCodes::OK) ? "green" : "red";

    return '<h1 style="color:' . $color . '">' . $statusMessage . '</h1>';
}

function isPasswordFileMissing()
{
    return !file_exists(FILE);
}

function isInvalidUri($uri)
{
    return $uri !== MY_URI;
}

function isInvalidContentType($headers)
{
    return getContType($headers) !== MY_CONT_TYPE;
}

function getContType($headers)
{
    foreach ($headers as $header) {
        $header = explode(': ', $header, 2);

        if ($header[0] === "Content-Type") {
            return $header[1];
        }
    }

    return null;
}

$testString = "POST /api/checkLoginAndPassword HTTP/1.1
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: 35

login=login1&password=1";

processHttpRequest($testString);