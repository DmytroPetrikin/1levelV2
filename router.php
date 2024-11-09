<?php
header("Access-Control-Allow-Origin: http://frontvrout.local");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
require_once 'config.php';
require_once 'validation.php';

function connectBdUsers()
{
    $connect = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connect;
}

function connectBdToDos()
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
    //мені звичайно більше подобається варіант зі switch бо тут
    //приходиться юзати анонімні функції
    $response = match ($_GET[ACTION]) {
        LOGIN => (function () use ($data) {
            require_once ACTIONS[LOGIN];
            isValueMissing($data, COLUMN_USER_LOGIN, COLUMN_USER_PASSWORD);

            return loginUser(connectBdUsers(), $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]);
        })(),
        LOGOUT => (function () {
            require_once ACTIONS[LOGOUT];

            return logout();
        })(),
        REGISTER => (function () use ($data) {
            require_once ACTIONS[REGISTER];
            isValueMissing($data, COLUMN_USER_LOGIN, COLUMN_USER_PASSWORD); // Перевірка наявності значень

            return registerUser(connectBdUsers(), $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]);
        })(),
        ADD_ITEM => (function () use ($data) {
            require_once ACTIONS[ADD_ITEM];
            isValueMissing($data, COLUMN_TODO_TEXT);

            return addItem($data[COLUMN_TODO_TEXT], $_SESSION[COLUMN_USER_ID], connectBdToDos());
        })(),
        GET_ITEMS => (function () use ($data) {
            require_once ACTIONS[GET_ITEMS];

            return getItems(connectBdToDos(), $_SESSION[COLUMN_USER_ID]);
        })(),
        DELETE_ITEM => (function () use ($data) {
            require_once ACTIONS[DELETE_ITEM];
            isValueMissing($data, COLUMN_TODO_ID);

            return deleteItem(connectBdToDos(), $data[COLUMN_TODO_ID], $_SESSION[COLUMN_USER_ID]);
        })(),
        CHANGE_ITEM => (function () use ($data) {
            require_once ACTIONS[CHANGE_ITEM];
            isValueMissing($data, COLUMN_TODO_ID, COLUMN_TODO_TEXT, COLUMN_TODO_CHECKED);

            return changeItem(connectBdToDos(), $data[COLUMN_TODO_ID], $data[COLUMN_TODO_TEXT], $data[COLUMN_TODO_CHECKED], $_SESSION[COLUMN_USER_ID]);
        })(),
        default => ['error' => 'Unknown request']
    };
    echo json_encode($response);
//    switch ($_GET[ACTION]) {
//        case LOGIN : // ++
//            include ACTIONS[LOGIN];
//            isValueMissing($data, COLUMN_USER_LOGIN, COLUMN_USER_PASSWORD);
//            echo json_encode(loginUser(connectBdUsers(), $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]));
//            break;
//        case LOGOUT ://++
//            require_once ACTIONS[LOGOUT];
//            echo json_encode(logout());
//            break;
//        case REGISTER : //++
//            require_once ACTIONS[REGISTER];
//            isValueMissing($data, COLUMN_USER_LOGIN, COLUMN_USER_PASSWORD); // Перевірка наявності значень
//            echo json_encode(registerUser(connectBdUsers(), $data[COLUMN_USER_LOGIN], $data[COLUMN_USER_PASSWORD]));
//            break;
//        case ADD_ITEM ://++
//            require_once ACTIONS[ADD_ITEM];
//            isValueMissing($data, COLUMN_TODO_TEXT);
//            echo json_encode(addItem($data[COLUMN_TODO_TEXT], $_SESSION[COLUMN_USER_ID], connectBdToDos()));
//            break;
//        case GET_ITEMS :
//            require_once ACTIONS[GET_ITEMS];
//            echo json_encode(getItems(connectBdToDos(), $_SESSION[COLUMN_USER_ID]));
//            break;
//        case DELETE_ITEM :
//            require_once ACTIONS[DELETE_ITEM];
//            isValueMissing($data, COLUMN_TODO_ID);
//            echo json_encode(deleteItem(connectBdToDos(), $data[COLUMN_TODO_ID], $_SESSION[COLUMN_USER_ID]));
//            break;
//        case CHANGE_ITEM :
//            require_once ACTIONS[CHANGE_ITEM];
//            isValueMissing($data, COLUMN_TODO_ID, COLUMN_TODO_TEXT, COLUMN_TODO_CHECKED);
//            echo json_encode(changeItem(connectBdToDos(), $data[COLUMN_TODO_ID], $data[COLUMN_TODO_TEXT], $data[COLUMN_TODO_CHECKED], $_SESSION[COLUMN_USER_ID]));
//            break;
//        default :
//            echo json_encode(['error' => 'Unknown request.']);
//    }
} catch (Exception $error) {
    echo json_encode(['error' => $error->getMessage()]);
}






