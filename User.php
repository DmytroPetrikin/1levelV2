<?php

const LOGIN_PREFIX = 'login=';
const PASSWORD_PREFIX = '&password=';
class User
{
    private $login;
    private $password;
    private $userData = [];

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public static function createUser($string)
    {
        $data = explode(PASSWORD_PREFIX, $string, 2);
        $login = explode(LOGIN_PREFIX, $data[0], 2)[1];
        $password = $data[1];
        return new User($login, $password);
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function searchUserData($fileName)
    {
        $file = fopen($fileName, "r");

        if ($file) {

            while (($line = fgets($file)) !== false) {
                $data = explode(':', $line, 2);

                if ($data[0] === $this->login) {
                    $this->userData['login'] = $data[0];
                    $this->userData['password'] = $data[1];

                    break;
                }
            }
        }
        fclose($file);
    }

    public function getUserData()
    {
        return $this->userData;
    }

}