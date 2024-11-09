<?php
require_once '../config.php';
require_once '../validation.php';
function deleteItem(array $idItem): array
{
    $todoList = json_decode(file_get_contents(TODO_FILE), true);

    foreach ($todoList as $key => &$todoItem) {
        if ($todoItem[COLUMN_TODO_ID] == $idItem[COLUMN_TODO_ID]) {
            unset($todoList[$key]);
        }
    }

    file_put_contents(TODO_FILE, json_encode($todoList, JSON_PRETTY_PRINT));

    return ['ok' => true];
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    isTodoFileMissing();
    isValueMissing($data, COLUMN_TODO_ID);
    echo json_encode(deleteItem($data));
} catch (Exception $e) {
    echo json_encode(['ok' => false]);
}