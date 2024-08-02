<?php

class ParserRequest
{
    private $method;
    private $uri;
    private $headers;
    private $body;

    public function __construct($method, $uri, $headers, $body)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function parseRequest($request)
    {

        $lines = explode(PHP_EOL, $request);
        $firstLine = explode(' ', $lines[0]);
        $method = $firstLine[0];
        $uri = $firstLine[1];
        $headers = [];
        $body = '';
        $numberOfRows = count($lines);

        for ($i = 1; $i < $numberOfRows; $i++) { // заповнили масив хедерів

            if ($lines[$i] === '') {
                $body = ($lines[$i] < $numberOfRows) ? $lines[$numberOfRows - 1] : '';
                break;
            }
            $headers[] = $lines[$i];
        }

        return new ParserRequest($method, $uri, $headers, $body);
    }

    public function getMethod()
    {

        return $this->method;
    }

    public function getUri()
    {

        return $this->uri;
    }

    public function getHeaders()
    {

        return $this->headers;
    }

    public function getBody()
    {

        return $this->body;
    }

}