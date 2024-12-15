<?php
function registerUser(PDO $db, string $login, string $password)
{
    $login = htmlspecialchars($login);
    // Перевірка чи існує вже такий користувач
    $checkUserStatement = $db->prepare("SELECT * FROM users WHERE login = :login");
    $checkUserStatement->bindParam(":login", $login, PDO::PARAM_STR);
    $checkUserStatement->execute();

    if ($checkUserStatement->rowCount() > 0) {
        return ['error' => 'User already exists'];
    }

    checkSpecialCharactersPassword($password);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $registerStatement = $db->prepare("INSERT INTO users (login, pass) VALUES (:login, :password)");    $registerStatement->bindParam(":login", $login, PDO::PARAM_STR);
    $registerStatement->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

    if ($registerStatement->execute()) {
        return ["ok" => true];
    }

    return ["error" => 'Failed to register user'];
}