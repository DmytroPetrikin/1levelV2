<?php
function getItems(PDO $db, int $userId)
{
    $getTodoItemsStmt = $db->prepare("SELECT * FROM " . TABLE_NAME_FOR_TODO . " WHERE " . COLUMN_USER_ID . " = :userId");
    $getTodoItemsStmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $getTodoItemsStmt->execute(); // Виконання запиту

    return ["items" => $getTodoItemsStmt->fetchAll(PDO::FETCH_ASSOC)];
}