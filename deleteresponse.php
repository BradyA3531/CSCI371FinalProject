<?php 

if(!isset($_GET('success')) || !isset($_GET('message'))){
    header("Location: dashboard.php");
}


echo "Success: ", $_GET('success');
echo "Message: ", $_GET('message');
?>

