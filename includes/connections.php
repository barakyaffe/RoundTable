<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_SCHEMA', 'roundtable');
//connect to the db
$dbCon = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);
//if the db return a connection error
if($dbCon->connect_errno){
	die('Failed to connect to DB, reason: '.$dbCon->connect_error);
}
