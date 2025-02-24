<?php
function loginUser(PDO $bd, string $login, string $password): array
{
    $login = htmlspecialchars($login);
    validatePassword($password);
    $userSearchStatement = $bd->prepare("SELECT user_id, pass FROM users WHERE login = :login");
    $userSearchStatement->bindParam(':login', $login, PDO::PARAM_STR);
    $userSearchStatement->execute();

    if ($user = $userSearchStatement->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $user[COLUMN_USER_PASSWORD])) {
            $_SESSION[COLUMN_USER_ID] = $user[COLUMN_USER_ID];

            return ['ok'=>true];
        }
    }

    throw new Exception('Invalid login or password.');
}
