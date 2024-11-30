<?php
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
const VALIDATION_DATA = [
    LOGIN => [COLUMN_USER_LOGIN, COLUMN_USER_PASSWORD],
    LOGOUT => [],
    REGISTER => [COLUMN_USER_LOGIN, COLUMN_USER_PASSWORD],
    ADD_ITEM => [COLUMN_TODO_TEXT],
    CHANGE_ITEM => [COLUMN_TODO_ID, COLUMN_TODO_TEXT, COLUMN_TODO_CHECKED],
    DELETE_ITEM => [COLUMN_TODO_ID],
    GET_ITEMS => [],
];

