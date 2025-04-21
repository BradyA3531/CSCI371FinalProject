<?php
require_once("required/authenticated.php");

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];

include("includes/header.php");
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
        <div class="account-details">
            <h5>User ID: <?php echo $userid?></h5>
            <h5>User Role: <?php echo $userrole?><h5>
            <h5>Username: <?php echo $username?></h5>
            <h5>Email: <?php echo $email?></h5>
        </div>
    </div>
</body>
</html>

<?php
include("includes/footer.php");
?>


