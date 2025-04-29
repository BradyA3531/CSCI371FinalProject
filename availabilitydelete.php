<?php
    require_once("required/authenticated.php");
    require_once("required/config.php");

    if(isset($_GET['availabilityid'])){
        $availabilityid = $_GET['availabilityid'];

        $sql = "DELETE FROM availability WHERE availabilityid = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $availabilityid);
            
            if ($stmt->execute()) {
                header("Location: teacher_availability.php");
                exit;
            } else {
                echo "Error deleting record: " . $conn->error;
            }
            $stmt->close();
        }else{
            echo "Error preparing statement: " . $conn->error;
        }
        
    }else {
        echo "No Evailabilityid provided.";
    }
    $conn->close();
?>