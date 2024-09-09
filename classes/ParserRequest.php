<?php

class ParserRequest
{
    private $method;
    private $uri;
    private $headers;
    private $body;
    private $params = [];

    public function __construct(string $method, string $uri, array $headers, string $body)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
        parse_str($body, $this->params);
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
                //якщо порожній рядок не є останнім то тіло буде після нього
                //інакше повертаємо порожній рядок
                $body = ($i + 1 < $numberOfRows) ? $lines[$i + 1] : '';

                break;
            }

            $headers[] = $lines[$i];
        }

        return new self ($method, $uri, $headers, $body);
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

    public function getParams(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->params; // повертає все тіло як масив
        }

        return $this->params[$key] ?? null;
    }

}