<?php
function loginUser(PDO $bd, string $login, string $password): array
{
    $login = htmlspecialchars($login);
    checkSpecialCharactersPassword($password);
    $userSearchStatement = $bd->prepare("SELECT " . COLUMN_USER_ID . ", " . COLUMN_USER_PASSWORD . " FROM " . TABLE_NAME_FOR_USERS . " WHERE " . COLUMN_USER_LOGIN . " = :login");
    $userSearchStatement->bindParam(':login', $login, PDO::PARAM_STR);
    $userSearchStatement->execute();

    if ($user = $userSearchStatement->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $user[COLUMN_USER_PASSWORD])) {
            $_SESSION[COLUMN_USER_ID] = $user[COLUMN_USER_ID];

            return ['ok' => true];
        }
    }

    return ['error' => 'Invalid login or password.'];
}
