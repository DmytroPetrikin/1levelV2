<?php
require_once 'config.php';
require_once 'validation.php';

function getItems()
{
    $jsonFile = file_get_contents(TODO_FILE);

    return ['items' => json_decode($jsonFile, true)];

}

try {
    if (isTodoFileMissing()) {
        throw new Exception('TODO file does not exist', 404);
    }

    var_dump(getItems());
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}