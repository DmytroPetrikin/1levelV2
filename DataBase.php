<?php

class DataBase
{
    static private ?PDO $instance = null;//створили статичну приватну змінну в якій буде підключення

    private function __construct()//Приватний конструктор аби не змогли створити новий обʼєкт
    {
    }

    public static function getInstance(): PDO //метод який створює конект 1 раз і потім його постійно повертає
    {
        if (!self::$instance = null) {
            try {
                self::$instance = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("DB connection error: " . $e->getMessage(), 500);
            }
        }

        return self::$instance;
    }

}