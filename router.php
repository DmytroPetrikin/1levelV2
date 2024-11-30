<?php
header("Access-Control-Allow-Origin: http://frontvrout.local");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

require_once 'config.php';
require_once 'configForRouter.php';
require_once 'validation.php';

function connectBd()
{
    $connect = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connect;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Відповідаємо на запит OPTIONS
    http_response_code(200);

    exit();
}

try {
    session_start();
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $_GET[ACTION];
    require_once ACTIONS[$action];
    isValueMissing($data, VALIDATION_DATA[$action]); //TODO проблема в цій перевірці
    $response = match ($action) {
        LOGIN => loginUser(connectBd(), $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]),
        LOGOUT => logout(),
        REGISTER => registerUser(connectBd(), $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]),
        ADD_ITEM => addItem($data[COLUMN_TODO_TEXT], $_SESSION[COLUMN_USER_ID], connectBd()),
        GET_ITEMS => getItems(connectBd(), $_SESSION[COLUMN_USER_ID]),
        DELETE_ITEM => deleteItem(connectBd(), $data[COLUMN_TODO_ID], $_SESSION[COLUMN_USER_ID]),
        CHANGE_ITEM => changeItem(connectBd(), $data[COLUMN_TODO_ID], $data[COLUMN_TODO_TEXT], $data[COLUMN_TODO_CHECKED], $_SESSION[COLUMN_USER_ID]),
        default => ['error' => 'Unknown request']
    };
    echo json_encode($response);
} catch (Exception $error) {
    echo json_encode(['error' => $error->getMessage()]);
}