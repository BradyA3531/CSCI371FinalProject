<?php
$success = $_GET['success'] ?? false; // "true"
$message = $_GET['message'] ?? '';    // "successfully deleted the appointment"

include("includes/header.php");
?>

<body>
    <div class="content">
        <div class="text-center">
            <?php if($success == true):?>
                <h3 class="text-center"><?php echo "Success: " . htmlspecialchars($message);?></h3>
            <?php else:?>
                <h3 class="text-center">Error: Deletion failed.</h3>
            <?php endif;?>
            <a type="button" class="btn btn-light" href="dashboard.php">Go Back</a>
        </div>
    </div>
</body>

<?php include("includes/footer.php") ?>