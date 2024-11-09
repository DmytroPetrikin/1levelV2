<?php
function registerUser(PDO $db, string $login, string $password)
{
    // Перевірка чи існує вже такий користувач
    $checkUserStatement = $db->prepare("SELECT * FROM " . TABLE_NAME_FOR_USERS . " WHERE " . COLUMN_USER_LOGIN . " = :login");
    $checkUserStatement->bindParam(":login", $login, PDO::PARAM_STR);
    $checkUserStatement->execute();

    if ($checkUserStatement->rowCount() > 0) {
        return ['error' => 'User already exists'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $registerStatement = $db->prepare("INSERT INTO " . TABLE_NAME_FOR_USERS . " (" . COLUMN_USER_LOGIN . ", " . COLUMN_USER_PASSWORD . ") VALUES (:login, :password)");
    $registerStatement->bindParam(":login", $login, PDO::PARAM_STR);
    $registerStatement->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

    if ($registerStatement->execute()) {
        return ["ok" => true];
    }

    return ["error" => 'Failed to register user'];
}