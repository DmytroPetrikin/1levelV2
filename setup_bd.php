<?php

require_once 'config/config.php';

$sqlQueries = [
    "CREATE DATABASE IF NOT EXISTS todo_list_db",
    "USE todo_list_db",
    "CREATE TABLE IF NOT EXISTS `users` (
        `user_id` INT AUTO_INCREMENT PRIMARY KEY,
        `login` VARCHAR(255) NOT NULL,
        `pass` VARCHAR(255) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS `todos` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `text` VARCHAR(255) NOT NULL,
        `checked` BOOLEAN DEFAULT 0,
        `user_id` INT, 
        FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
        ON DELETE CASCADE
    )"
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
