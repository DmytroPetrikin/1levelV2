<?php

require_once 'config.php';

$sqlQueries = [
    "CREATE DATABASE IF NOT EXISTS " . DB_NAME
    ,
    "USE " . DB_NAME
    ,
    "CREATE TABLE IF NOT EXISTS `" . TABLE_NAME_FOR_USERS . "` (
   `" . COLUMN_USER_ID . "` INT AUTO_INCREMENT PRIMARY KEY,
    `" . COLUMN_USER_LOGIN . "` VARCHAR(255) NOT NULL,
    `" . COLUMN_USER_PASSWORD . "` VARCHAR(255) NOT NULL)"
    ,
    "CREATE TABLE IF NOT EXISTS `" . TABLE_NAME_FOR_TODO . "` (
    `" . COLUMN_TODO_ID . "` INT AUTO_INCREMENT PRIMARY KEY,
    `" . COLUMN_TODO_TEXT . "` VARCHAR(255) NOT NULL,
    `" . COLUMN_TODO_CHECKED . "` BOOLEAN DEFAULT 0,
    `" . COLUMN_USER_ID . "` INT, 
    FOREIGN KEY (`" . COLUMN_USER_ID . "`) REFERENCES `" . TABLE_NAME_FOR_USERS . "`(`" . COLUMN_USER_ID . "`)
    ON DELETE CASCADE)"
];

try {
    $connect = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASSWORD);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($sqlQueries as $sqlQuery) {
        $connect->exec($sqlQuery);
    }

    echo "Database and tables have been created.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
