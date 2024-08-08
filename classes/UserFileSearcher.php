<?php
require_once "User.php";
class UserFileSearcher
{
    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function findUserByLogin(string $login)
    {
        $file = fopen($this->fileName, "r");

        while (($line = fgets($file)) !== false) {
            $data = explode(':', $line, 2);

            if ($data[0] === $login) {
                fclose($file);

                return User::createUser($login, trim($data[1])) ;
            }
        }
        fclose($file);

        return null;
    }
}