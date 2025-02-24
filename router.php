<?php
header("Access-Control-Allow-Origin: http://frontvrout.local");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

require_once 'config.php';
require_once 'configForRouter.php';
require_once 'validation.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Відповідаємо на запит OPTIONS
    http_response_code(RESPONSE_CODE_OK);

    exit();
}

try {
    session_start();
    $connect = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $_GET[ACTION];
    require_once ACTIONS[$action];
    isValueMissing($data, VALIDATION_DATA[$action]); //TODO проблема в цій перевірці

    $response = match ($action) {
        LOGIN => loginUser($connect, $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]),
        LOGOUT => logout(),
        REGISTER => registerUser($connect, $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]),
        ADD_ITEM => addItem($data[COLUMN_TODO_TEXT], $_SESSION[COLUMN_USER_ID], $connect),
        GET_ITEMS => getItems($connect, $_SESSION[COLUMN_USER_ID]),
        DELETE_ITEM => deleteItem($connect, $data[COLUMN_TODO_ID], $_SESSION[COLUMN_USER_ID]),
        CHANGE_ITEM => changeItem($connect, $data[COLUMN_TODO_ID], $data[COLUMN_TODO_TEXT], $data[COLUMN_TODO_CHECKED], $_SESSION[COLUMN_USER_ID]),
        default => function () {
            throw new Exception("Not found", RESPONSE_CODE_NOT_FOUND);
        }
    };
    echo json_encode($response);
} catch (Exception $error) {
    http_response_code($error->getCode() ?: RESPONSE_CODE_INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => $error->getMessage()]);
}