<?php
require_once 'config.php';

function isTodoFileMissing()
{
    return !file_exists(TODO_FILE);
}

function isIdFileMissing()
{
    return !file_exists(ID_FILE);
}

function isIdMissing($array)
{
    return !isset($array['id']);
}

function isValueTextMissing($data)
{
    return !isset($data['text']);
}






