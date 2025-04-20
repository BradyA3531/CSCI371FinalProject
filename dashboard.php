<?php
require_once("required/authenticated.php");
require_once("required/config.php");

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];

include("includes/header.php")

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <div class="content">
   <?php if($userrole === "student"): ?>
      student view
   <?php else:?>
      teacher view
   <?php endif; ?>
   </div>
</body>
</html>

<?php 
include("includes/footer.php")

?>
