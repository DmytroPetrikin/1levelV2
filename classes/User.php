<?php

class User
{
    public function __construct(private string $login, private string $password)
    {
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }

}