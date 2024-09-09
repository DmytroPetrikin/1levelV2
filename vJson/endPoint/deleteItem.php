<?php
require_once 'config.php';
require_once 'validation.php';

function deleteItem(array $idItem): array
{
    $todoList = json_decode(file_get_contents(TODO_FILE), true);

    foreach ($todoList as $key => &$todoItem) {
        if ($todoItem['id'] == $idItem['id']) {
            unset($todoList[$key]);
        }

        if ($todoItem['id'] > $idItem['id']) {
            $todoItem['id']--;
        }
    }

    reduceLastId();
    file_put_contents(TODO_FILE, json_encode($todoList, JSON_PRETTY_PRINT));

    return ['ok' => true];
}

function reduceLastId()
{
    $lastId = intval(file_get_contents(ID_FILE));

    if ($lastId != 0) {
        file_put_contents(ID_FILE, --$lastId);
    }
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

    echo json_encode(deleteItem($data));
}catch (Exception $e){
    echo json_encode(['ok' => false]);
}