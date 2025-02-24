<?php
function logout() : array
{
    session_destroy();

    return ['ok'=> true];
}