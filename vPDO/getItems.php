<?php
function getItems(PDO $db, int $userId)
{
    $getTodoItemsStmt = $db->prepare("SELECT * FROM todos WHERE user_id = :userId");
    $getTodoItemsStmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $getTodoItemsStmt->execute(); // Виконання запиту

    return ["items" => $getTodoItemsStmt->fetchAll(PDO::FETCH_ASSOC)];
}