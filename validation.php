<?php
require_once 'config.php';

function isTodoFileMissing()
{
    if (!file_exists(TODO_FILE)) {
        throw new Exception('TODO file does not exist', 404);
    }
}

function isDataBaseConnectingMissing(mysqli $connect)
{
    if ($connect->connect_errno) {
        die("Connection failed: " . $connect->connect_error);
    }
}

function isStatementMissing($stmt)
{
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . mysqli_error($stmt), 500);
    }
}

function isValueMissing($data, ...$values)
{
    foreach ($values as $value) {
        if (!isset($data[$value])) {
            throw new Exception("No value selected", 400);
        }
    }
}




