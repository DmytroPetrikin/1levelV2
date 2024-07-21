<?php


// не звертайте на цю функцію уваги
// вона потрібна для того щоб правильно зчитати вхідні дані
function readHttpLikeInput()
{
    $f = fopen('php://stdin', 'r');
    $store = "";
    $toread = 0;

    while ($line = fgets($f)) {
        $store .= preg_replace("/\r/", "", $line);

        if (preg_match('/Content-Length: (\d+)/', $line, $m))
            $toread = $m[1] * 1;

        if ($line === PHP_EOL)

            break;
    }

    if ($toread > 0)
        $store .= fread($f, $toread);

    return $store;
}

$contents = readHttpLikeInput();

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

$http = parseTcpStringAsHttpRequest($contents);
echo json_encode($http, JSON_PRETTY_PRINT);
