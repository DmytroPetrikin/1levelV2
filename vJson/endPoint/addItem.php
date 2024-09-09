<?php
require_once 'config.php';
require_once 'validation.php';

function initializeFiles()
{
    //створюю файл де буду зберігати останній id + 1
    if (isIdFileMissing()) {
        file_put_contents(ID_FILE, 0);
    }

    //створюю .json файл для зберігання тудушок
    if (isTodoFileMissing()) {
        touch(TODO_FILE);
    }
}

function addItem(array $array)
{
    $todoList = json_decode(file_get_contents(TODO_FILE), true);
    $id = getId();
    $todoList[] = [
        'id' => $id,
        'text' => $array['text'],
        'checked' => false,
    ];
    //записую тудушку в файл
    file_put_contents(TODO_FILE, json_encode($todoList, JSON_PRETTY_PRINT));

    return ['id' => $id];
}

function getId()
{
    $id = intval(file_get_contents(ID_FILE)) + 1;
    file_put_contents(ID_FILE, $id);

    return $id;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    initializeFiles();

    if (isValueTextMissing($data)){
        throw new Exception("No value selected", 400);
    }

    echo json_encode(addItem($data));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}




