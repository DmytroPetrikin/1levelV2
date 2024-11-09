<?php
function deleteItem(PDO $db, int $itemId, int $userId)
{
    $deleteItemStatement = $db->prepare("DELETE FROM " . TABLE_NAME_FOR_TODO . " WHERE " . COLUMN_TODO_ID . " = :id AND " . COLUMN_USER_ID . " = :userId ");
    $deleteItemStatement->bindParam(":id", $itemId, PDO::PARAM_INT);
    $deleteItemStatement->bindParam(":userId", $userId, PDO::PARAM_INT);

    if ($deleteItemStatement->execute() &&
        $deleteItemStatement->rowCount() > 0) {
        return ['ok' => true];
    }

    return ['error' => 'No item found with the given ID'];
}