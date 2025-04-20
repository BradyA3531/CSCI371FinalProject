<?php
session_start();

if(session_status() != PHP_SESSION_ACTIVE ||
    !isset($_SESSION['username']) ||
    !isset($_SESSION['userrole']) ||
    !isset($_SESSION['userid'])
){
    header('Location: login.php');
    exit();
}