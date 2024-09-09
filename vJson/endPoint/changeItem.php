<?php
require_once 'config.php';
require_once 'validation.php';

function changeItem(array $newTodoItem): array
{
    $todolist = json_decode(file_get_contents(TODO_FILE), true);

    foreach ($todolist as &$todo) {
        if (in_array($newTodoItem['id'], $todo)) {
            $todo['text'] = $newTodoItem['text'];
            $todo['checked'] = $newTodoItem['checked'];
            file_put_contents(TODO_FILE, json_encode($todolist, JSON_PRETTY_PRINT));

            return ['ok' => true];
        }
    }

    throw new Exception('No record to change found');
}

try {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (isTodoFileMissing()) {
        throw new Exception('TODO file does not exist', 404);
    }

    if (isIdMissing($data)) {
        throw new Exception('ID missing', 404);
    }

    echo json_encode(changeItem($data));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}






