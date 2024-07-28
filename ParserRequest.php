<?php

class ParserRequest
{
    private $lines = [];
    private $bodyExist;
    private $countLines;

    public function __construct($string)
    {
        $this->lines = explode(PHP_EOL, $string);
        $this->bodyExist = $this->isBodyExist($this->lines);
        $this->countLines = count($this->lines);
    }

    public function getLines(){

        return $this->lines;
    }

    public function getCountLines(){

        return $this->countLines;
    }

    private function isBodyExist ($lines){

        foreach ($lines as $line) {
            if (trim($line) === '') {
                return true; // Порожній рядок розділяє заголовки та тіло
            }
        }
        return false;
    }

    public function getBodyExist(){

        return $this->bodyExist;
    }

    function getFirstLine(){

        return $this->lines[0];
    }

}