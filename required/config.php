<?php

$db_host = 'rei.cs.ndsu.nodak.edu';
$db_user = 'spencer_collins_371s25';
$db_pass = 'pN8Da1eNqN0!';
$db_name = 'spencer_collins_db371s25';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
