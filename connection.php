<?php
$server = 'localhost';
$user = 'root';
$pass = '';
$baza = 'bookstore';

$con = new mysqli($server, $user, $pass, $baza);


if (!$con->connect_error) {
    return true;
} else {
    die("Connection error" . $con->connect_error);
}
