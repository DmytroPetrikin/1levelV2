<?php

require_once 'config.php';

// Підключення до MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);

// Перевірка підключення
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Створення бази даних
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;

if ($conn->query($sql) !== TRUE) {
    echo "Error creating database: " . $conn->error;
}

// Підключення до бази даних
$conn->select_db(DB_NAME);
// Створення таблиці для користувачів
$sql = "CREATE TABLE IF NOT EXISTS `" . TABLE_NAME_FOR_USERS . "` (
    `" . COLUMN_USER_ID . "` INT AUTO_INCREMENT PRIMARY KEY,
    `" . COLUMN_USER_LOGIN . "` VARCHAR(255) NOT NULL,
    `" . COLUMN_USER_PASSWORD . "` VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating users table: " . $conn->error;
}

// Створення таблиці для туду-листа з прив'язкою до користувача
$sql = "CREATE TABLE IF NOT EXISTS `" . TABLE_NAME_FOR_TODO . "` (
    `" . COLUMN_TODO_ID . "` INT AUTO_INCREMENT PRIMARY KEY,
    `" . COLUMN_TODO_TEXT . "` VARCHAR(255) NOT NULL,
    `" . COLUMN_TODO_CHECKED . "` BOOLEAN DEFAULT 0,
    `" . COLUMN_USER_ID . "` INT,  -- Поле для зберігання ID користувача
    FOREIGN KEY (`" . COLUMN_USER_ID . "`) REFERENCES `" . TABLE_NAME_FOR_USERS . "`(`" . COLUMN_USER_ID . "`)
    ON DELETE CASCADE  -- Якщо користувача буде видалено, то його туду також буде видалено
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating todos table: " . $conn->error;
}

// Закриття підключення
$conn->close();
