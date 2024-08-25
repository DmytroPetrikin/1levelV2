<?php
$password = "1";
$hashpass = password_hash($password, PASSWORD_DEFAULT);

echo $hashpass;
