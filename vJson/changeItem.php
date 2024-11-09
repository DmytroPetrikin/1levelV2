<?php
require_once '../config.php';
require_once '../validation.php';

function changeItem(array $newTodoItem): array
{
    $todolist = json_decode(file_get_contents(TODO_FILE), true);

    foreach ($todolist as &$todo) {
        if (in_array($newTodoItem[COLUMN_TODO_ID], $todo)) {
            //захист від xss
            $todo[COLUMN_TODO_TEXT] = htmlspecialchars($newTodoItem[COLUMN_TODO_TEXT]);
            $todo[COLUMN_TODO_CHECKED] = $newTodoItem[COLUMN_TODO_CHECKED];
            file_put_contents(TODO_FILE, json_encode($todolist, JSON_PRETTY_PRINT));

            return ['ok' => true];
        }
    }

    throw new Exception('No record to change found');
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    isTodoFileMissing();
    isValueMissing($data, COLUMN_TODO_CHECKED, COLUMN_TODO_TEXT, COLUMN_TODO_ID);
    echo json_encode(changeItem($data));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}






