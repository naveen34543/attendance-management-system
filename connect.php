<?php

$con = new mysqli('localhost', 'root', '', 'studentattendance');
if ($con->connect_error) {
    die('Connect Error (' . $con->connect_errno . ') ' . $con->connect_error);
}

?>
