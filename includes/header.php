<?php
require_once("required/authenticated.php");

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Bootstrap Header</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <header class="bg-light py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <div class="col-md-9">
                        <a class="navbar-brand">Appointment Scheduler</a>
                    </div>
                </div>
                <div class="col-md">
                    <nav class="navbar navbar-expand-md navbar-light">
                        <div class="container-fluid">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                                    </li>
                                    <?php if ($userrole === "instructor"): ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="teacher_availability.php">Set Availability</a>
                                        </li>
                                    <?php endif; ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="accountdetails.php">Account Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
                <div class="col-md-3">
                    <div class="col">
                        <h3 class=""><?php echo $username ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </header>
</body>

</html>