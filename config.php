<?php
const ID_FILE = 'last_id.txt';
const TODO_FILE = 'todo.json';
const DB_HOST = "localhost";
const DB_USER = "admin_todo";
const DB_PASSWORD = "12345";
const DB_NAME = "todo_list_db";
const TABLE_NAME_FOR_TODO = "todos";
const COLUMN_TODO_TEXT = "text";
const COLUMN_TODO_ID = "id";
const COLUMN_TODO_CHECKED = "checked";
const TABLE_NAME_FOR_USERS = "users";
const COLUMN_USER_ID = "user_id";
const COLUMN_USER_LOGIN = "login";
const COLUMN_USER_PASSWORD = "pass";
const INT_VALUE_TRUE = 1;
const INT_VALUE_FALSE = 0;
const LOGIN = 'login';
const LOGOUT = 'logout';
const REGISTER = 'register';
const ADD_ITEM = 'addItem';
const CHANGE_ITEM = 'changeItem';
const DELETE_ITEM = 'deleteItem';
const GET_ITEMS = 'getItems';
const ACTION = 'action';
const ACTIONS = [
    LOGIN => 'authentication/login.php',
    LOGOUT => 'authentication/logout.php',
    REGISTER => 'authentication/register.php',
    ADD_ITEM => 'vPDO/addItem.php',
    CHANGE_ITEM => 'vPDO/changeItem.php',
    DELETE_ITEM => 'vPDO/deleteItem.php',
    GET_ITEMS => 'vPDO/getItems.php',
];
?>