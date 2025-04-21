<?php
require_once("required/config.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT userid, username, password, userrole, email
            FROM users
            WHERE email = ?";

    $stmt = $conn->prepare($sql);

    if($stmt){
        $stmt->bind_param("s",$email);

        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        $stmt->close();
        $result->free();

        if($user && password_verify($password, $user['password'])){
                session_start();
                $_SESSION["username"] = $user['username'];
                $_SESSION["userrole"] = $user['userrole'];
                $_SESSION["userid"] = $user['userid'];
                $_SESSION["email"] = $user["email"];
                header('Location: dashboard.php');
                exit();
        } 
        else {
            $error_message = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
</head>

<body>
    <div class="container login-container w-25">
        <h2 class="text-center mb-4">Login</h2>
        <?php if(!empty($error_message)):?>
        <p class="text-danger"><?php echo $error_message?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Email</label>
                <input type="email" class="form-control" id="username" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>

</html>