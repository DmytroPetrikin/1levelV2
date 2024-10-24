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
// Створення таблиці для туду-листа
$sql = "CREATE TABLE IF NOT EXISTS `" . DB_TABLE_NAME . "` (
    `" . DB_NAME_COLL_ID . "` INT AUTO_INCREMENT PRIMARY KEY,
    `" . DB_NAME_COLL_TEXT . "` VARCHAR(255) NOT NULL,
    `" . DB_NAME_COLL_CHECKED . "` BOOLEAN DEFAULT 0
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}

// Закриття підключення
$conn->close();
