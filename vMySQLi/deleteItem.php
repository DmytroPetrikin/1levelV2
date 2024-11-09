<?php
require_once '../validation.php';
require_once '../config.php';

function deleteItem(mysqli $db, int $itemId)
{
    $deleteItemStatement = $db->prepare("DELETE FROM " . TABLE_NAME_FOR_TODO . " WHERE " . COLUMN_TODO_ID . " = ?");
    isStatementMissing($deleteItemStatement);
    $deleteItemStatement->bind_param("i", $itemId);

    if ($deleteItemStatement->execute()) {
        // Перевірка кількості змінених рядків
        if ($deleteItemStatement->affected_rows > 0) {
            return ['success' => 'Item deleted successfully'];
        }

        return ['error' => 'No item found with the given ID'];
    }

    throw new Exception("Failed to execute statement: " . $deleteItemStatement->error, 500);
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    isValueMissing($data, COLUMN_TODO_ID);
    $connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    isDataBaseConnectingMissing($connect);
    echo json_encode(deleteItem($connect, $data[COLUMN_TODO_ID]));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($connect) && $connect) {
        $connect->close();
    }
}