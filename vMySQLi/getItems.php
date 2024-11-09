<?php
require_once '../validation.php';
require_once '../config.php';
function getItems(mysqli $db)
{
    $getTodoItemsStmt = $db->query("SELECT * FROM " . TABLE_NAME_FOR_TODO);
    isStatementMissing($getTodoItemsStmt);

    return ["items" => $getTodoItemsStmt->fetch_all(MYSQLI_ASSOC)];
}

try {
    $connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    isDataBaseConnectingMissing($connect);
    echo json_encode(getItems($connect));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($connect) && $connect) {
        $connect->close();
    }
}