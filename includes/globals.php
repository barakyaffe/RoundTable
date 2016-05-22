<?php
//starts the output buffer, prevernt `headers already sent` warning
ob_start();
//set time zone
//date_default_timezone_set('Asia/Jerusalem');
date_default_timezone_set("Israel");
//force utf-8
header('Content-Type: text/html; charset=utf-8');
//start the session
session_start();
//force show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'connections.php';