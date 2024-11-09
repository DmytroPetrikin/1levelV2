<?php
require_once '../validation.php';
require_once '../config.php';

function changeItem(mysqli $db, int $id, string $newText, int $checked)
{
    $newText = htmlspecialchars($newText);
    $changeItemStmt = $db->prepare("
UPDATE " . TABLE_NAME_FOR_TODO . " 
SET " . COLUMN_TODO_TEXT . " = ?, " . COLUMN_TODO_CHECKED . " = ? 
WHERE " . COLUMN_TODO_ID . " = ?;
");
    isStatementMissing($changeItemStmt);
    $changeItemStmt->bind_param("sii", $newText, $checked, $id);

    if ($changeItemStmt->execute()) {
        if ($changeItemStmt->affected_rows > 0) {
            return ['success' => 'Item updated successfully'];
        }

        return ['error' => 'No item found with the given ID'];
    }

    throw new Exception("Failed to execute statement: " . $changeItemStmt->error, 500);
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    isValueMissing($data, COLUMN_TODO_ID, COLUMN_TODO_TEXT, COLUMN_TODO_CHECKED);
    $connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    isDataBaseConnectingMissing($connect);
    echo json_encode(changeItem($connect, $data[COLUMN_TODO_ID], $data[COLUMN_TODO_TEXT], $data[COLUMN_TODO_CHECKED]));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($connect) && $connect) {
        $connect->close();
    }
}