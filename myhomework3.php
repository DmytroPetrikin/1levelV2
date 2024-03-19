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
    $statuscode = getStatusCode($method, $uri);
    $statusmessage = getStatusMessage($statuscode);
    outputHttpResponse($statuscode, $statusmessage, $headers, getResult($uri, $statuscode));
}

function getResult($uri, $statusmessage)
{
    if ($statusmessage !== "OK") {

        return $statusmessage;
    }
    $sumQueryAndNumbers = explode('=', $uri);
    $nums = explode(',', $sumQueryAndNumbers[1]);

    return array_sum($nums);
}

function getStatusMessage($statuscode)
{
    switch ($statuscode){
        case 404:

            return "Not Found";
        case 400:

            return "Bad Request";
        default:

            return "OK";
    }
}

function getStatusCode($method, $uri)
{
    if (start(explode("?", $uri)) != "/sum") {

        return "404";
    }
    if (!str_contains($uri, '?nums=') || !str_contains($method, 'GET')) {

        return "400";
    }

    return "200";
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
    //тут такий варіант не працює, бо як я зрозумів з умови body є не завжди а тим паче
    //порожній рядок
    //return explode("\n\n", $string, 2)[1];

    $lines = explode("\n", $string);
    $body = end($lines);
    //загалом не дуже розумію як знайти body якщо не буде порожнього рядка
    //бо моя перевірка чи немає : не дуже вдала
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