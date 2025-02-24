<?php
function logout()
{
    session_destroy();

    throw new Exception(true);
}