<?php


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
    $lines = explode(PHP_EOL, $string);
    $firstLine = explode(' ', $lines[0]);
    $method = $firstLine[0];
    $uri = $firstLine[1];
    $headers = [];
    $numberOfRows = count($lines);

    for ($i = 1; $i < $numberOfRows; $i++) { // заповнили масив хедерів

        if ($lines[$i] === '') {
            $body = ($lines[$i] < $numberOfRows) ?  $lines[$numberOfRows - 1] :  '';
            break;
        }
        $headers[] = $lines[$i];
    }

    return [
        "method" => $method,
        "uri" => $uri,
        "headers" => $headers,
        "body" => $body,
    ];
}


function getHeaders($string)
{
    $lines = explode("\n", $string);
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

    return explode("\n\n", $string, 2)[1];
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
Bost: blabla
Vost: gaga21

body112";

$http = parseTcpStringAsHttpRequest($mystr);
echo json_encode($http, JSON_PRETTY_PRINT);
