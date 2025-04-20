<?php
require_once("required/authenticated.php");

echo $_SESSION["username"], "\n";
echo $_SESSION["userrole"], "\n";
echo $_SESSION["userid"];
