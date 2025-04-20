<?php
require_once("required/authenticated.php");

unset($_SESSION['userid']);
unset($_SESSION['username']);
unset($_SESSION['userrole']);

session_destroy();

header("Location: login.php");
exit();
