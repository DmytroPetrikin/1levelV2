<?php
const START_DIRECTORY = "D:/";

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
    $statuscode = getStatusCode($headers, $uri);
    $statusMessage = getStatusMessage($statuscode);
    $body = getResult($statuscode, $uri, $headers);
    outputHttpResponse($statuscode, $statusMessage, $headers, $body);

}

function getResult($code, $uri, $headers)
{
    switch ($code) {
        case "200":

            return file_get_contents(START_DIRECTORY . getHost($headers) . $uri);
        case "403":

            return "Access denied";
        case "404":

            return "Not Found";
        default:

            return "Unknown Status";
    }
}

function getstatusmessage($code)
{
    switch ($code) {
        case "200":

            return "OK";
        case "403":

            return "Access denied";
        case "404":

            return "Not Found";
        default:

            return "Unknown Status";
    }
}

function getStatusCode($headers, $uri)
{
    if (checkHost($headers)) {

        return "404";
    }
    if (checkDirectory($uri, $headers)) {

        return "403";
    }
    if (checkFile($uri, $headers)) {

        return "404";
    }

    return "200";
}

function checkFile($uri, $headers)
{
    $host = getHost($headers);

    return !file_exists(START_DIRECTORY . $host . $uri);
}

function checkDirectory($uri, $headers)
{
    $host = getHost($headers);
    $file = dirname($uri);

    return !is_dir(START_DIRECTORY . $host . $file);
}


function checkHost($headers)
{
    $host = getHost($headers);

    if (str_contains($host, "student.shpp.me") ||
        str_contains($host, "another.shpp.me")) {

        return false;
    }

    return true;
}

/**
 * @throws HostNotFoundException
 */
function getHost($headers)
{

    foreach ($headers as $subarray) {

        if ($subarray[0] === 'Host') {

            return $subarray[1];
        }

        throw new HostNotFoundException("The Host not found in the headers");
    }
}

class HostNotFoundException extends Exception
{
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

$mystr = "GET /123 HTTP/1.1
Host: student.shpp.me
Accept: image/gif, image/jpeg, */*
Accept-Language: en-us
Accept-Encoding: gzip, deflate
User-Agent: Mozilla/4.0";

$http = parseTcpStringAsHttpRequest($mystr);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);