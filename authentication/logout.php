<?php
function logout()
{
    session_destroy();

    return ['ok' => true];
}