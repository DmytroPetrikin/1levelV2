<?php
function changeItem(PDO $db, int $id, string $newText, bool $checked, int $userId)
{
    $newText = htmlspecialchars($newText);
    $changeItemStmt = $db->prepare("UPDATE todos SET text = :newText, checked = :checked WHERE id = :id AND user_id = :userId");
    $checkedValue = $checked ? INT_VALUE_TRUE : INT_VALUE_FALSE;
    $changeItemStmt->bindParam(":id", $id, PDO::PARAM_INT);
    $changeItemStmt->bindParam(":newText", $newText, PDO::PARAM_STR);
    $changeItemStmt->bindParam(":checked", $checkedValue, PDO::PARAM_INT);
    $changeItemStmt->bindParam(":userId", $userId, PDO::PARAM_INT);

    if ($changeItemStmt->execute() &&
        $changeItemStmt->rowCount() > 0) {
        return ['success' => 'Item updated successfully'];
    }

    return ['error' => 'No item found with the given ID'];
}