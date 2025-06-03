<?php
header("Access-Control-Allow-Origin: http://frontvrout.local");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

require_once 'config.php/config.php';
require_once 'config/configForRouter.php';
require_once 'validation.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Відповідаємо на запит OPTIONS
    http_response_code(RESPONSE_CODE_OK);

    exit();
}

try {
    session_start();
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $_GET[ACTION];
    require_once ACTIONS[$action];
    isValueMissing($data, VALIDATION_DATA[$action]);

    $response = match ($action) {
        LOGIN => loginUser($data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]),
        LOGOUT => logout(),
        REGISTER => registerUser($data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]),
        ADD_ITEM => addItem($data[COLUMN_TODO_TEXT], $_SESSION[COLUMN_USER_ID]),
        GET_ITEMS => getItems($_SESSION[COLUMN_USER_ID]),
        DELETE_ITEM => deleteItem($data[COLUMN_TODO_ID], $_SESSION[COLUMN_USER_ID]),
        CHANGE_ITEM => changeItem($data[COLUMN_TODO_ID], $data[COLUMN_TODO_TEXT], $data[COLUMN_TODO_CHECKED], $_SESSION[COLUMN_USER_ID]),
        default => function () {
            throw new Exception("Not found", RESPONSE_CODE_NOT_FOUND);
        }
    };
    echo json_encode($response);
} catch (Exception $error) {
    http_response_code($error->getCode() ?: RESPONSE_CODE_INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => $error->getMessage()]);
}