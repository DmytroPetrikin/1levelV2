<?php
function logout()
{
    $_SESSION = array();
    session_destroy();

    return ['ok' => true];
}