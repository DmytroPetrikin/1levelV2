<?php

require_once "ParserRequest.php";
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
    $parserRequest = new ParserRequest($string);
    $methodAndUri = explode(" ", $parserRequest->getFirstLine());

    return [
        "method" => $methodAndUri[0],
        "uri" => $methodAndUri[1],
        "headers" => getHeaders($parserRequest),
        "body" => getBody($parserRequest)
    ];
}

function getBody(ParserRequest $parserRequest)
{
    $lines = $parserRequest->getLines();
    $countRows = $parserRequest->getCountLines();

    for ($i = 1; $i < $countRows; $i++) {

        if (trim($lines[$i]) === '' && $i + 1 < $countRows) {

            return trim($lines[$i + 1]);
        }
    }

    return "";
}

function getHeaders($parserRequest)
{
    $lines = $parserRequest->getLines();
    $headers = [];
    $countRows = $parserRequest->getCountLines();

    for ($i = 1; $i < $countRows; $i++) {

        if (trim($lines[$i]) === '') {

            break;
        }
        $headers[] = explode(": ", $lines[$i]);
    }

    return $headers;
}

$mystr = "GET /sum?nums=1,2,5 HTTP/1.1
Host: student.shpp.me
Bost: blabla
Vost: gaga21

body112";

$http = parseTcpStringAsHttpRequest($mystr);
echo json_encode($http, JSON_PRETTY_PRINT);
