<?php

require_once "classes/ParserRequest.php";
// не звертайте на цю функцію уваги
// вона потрібна для того щоб правильно зчитати вхідні дані
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
//
//$contents = readHttpLikeInput();

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
Bost: blabla
Vost: gaga21

body112";

$http = parseTcpStringAsHttpRequest($mystr);
echo json_encode($http, JSON_PRETTY_PRINT);
