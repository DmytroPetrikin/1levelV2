<?php
require_once '../validation.php';
require_once '../config.php';

function addItem(string $text, mysqli $db)
{
    $text = htmlspecialchars($text);
    $addItemStatement = $db->prepare("INSERT INTO " . TABLE_NAME_FOR_TODO . " (" . COLUMN_TODO_TEXT . ", " . COLUMN_TODO_CHECKED . ") VALUES (?, 0)");
    isStatementMissing($addItemStatement);
    $addItemStatement->bind_param("s", $text);

    // Виконання запиту
    if ($addItemStatement->execute()) {
        // Перевірка кількості змінених рядків
        if ($addItemStatement->affected_rows > 0) {
            return ['success' => 'Item added successfully', 'id' => $addItemStatement->insert_id];
        }

        return ['error' => 'Failed to add item']; // Якщо жоден рядок не був змінений
    }

    throw new Exception("Failed to execute statement: " . $addItemStatement->error, 500);
}

try {
    //Отримання введеного користувачем тексту
    $data = json_decode(file_get_contents('php://input'), true);
    //Перевірка на існування тексту в отриманих даних
    isValueMissing($data, COLUMN_TODO_TEXT);
    //Підключення до бази даних
    $connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    //Перевірка підключення
    isDataBaseConnectingMissing($connect);
    //Додавання ітема та отримання його id
    echo json_encode(addItem($data[COLUMN_TODO_TEXT], $connect));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($connect) && $connect) {
        $connect->close();
    }
}