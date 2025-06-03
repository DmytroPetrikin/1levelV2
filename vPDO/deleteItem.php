<?php
function deleteItem(int $itemId, int $userId)
{
    $deleteItemStatement = DataBase::getInstance()->prepare("DELETE FROM todos WHERE id = :id AND user_id = :userId");
    $deleteItemStatement->bindParam(":id", $itemId, PDO::PARAM_INT);
    $deleteItemStatement->bindParam(":userId", $userId, PDO::PARAM_INT);

    if ($deleteItemStatement->execute() &&
        $deleteItemStatement->rowCount() > 0) {
        return ['ok'=> true];
    }

    throw new Exception('No item found with the given ID');
}