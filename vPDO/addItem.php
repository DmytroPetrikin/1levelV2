<?php
function addItem(string $text, int $userId, PDO $db)
{
    $text = htmlspecialchars($text);
    $addItemStatement = $db->prepare("INSERT INTO " . TABLE_NAME_FOR_TODO . " (" . COLUMN_TODO_TEXT . ", " . COLUMN_TODO_CHECKED . ", " . COLUMN_USER_ID . ") VALUES (:text, :checked, :userId)");
// Прив'язка значень
    $addItemStatement->bindValue(':text', $text, PDO::PARAM_STR);
    $addItemStatement->bindValue(':checked', INT_VALUE_FALSE, PDO::PARAM_INT); // Використання константи
    $addItemStatement->bindValue(':userId', $userId, PDO::PARAM_INT); // Прив'язка ID користувача

    // Виконання запиту
    if ($addItemStatement->execute()) {
        // Отримання ID останнього вставленого запису
        $id = $db->lastInsertId();

        return ['success' => 'Item added successfully', 'id' => $id];
    }

    return ['error' => 'Failed to add item']; // Якщо жоден рядок не був змінений
}

