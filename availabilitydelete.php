<?php
    require_once("required/authenticated.php");
    require_once("required/config.php");

    if(isset($_GET['availabilityid'])){
        $availabilityid = $_GET['availabilityid'];

        $newsql = "SELECT * FROM appointment_availability WHERE availabilityid = ?";

        $appdeletesql = "DELETE FROM appointments WHERE availabilityid = ?";

        if ($stmt = $conn->prepare($newsql)) {
            $stmt->bind_param("i", $availabilityid);
            
            $stmt->execute();

            if($stmt){
                $result = $stmt->get_result()->fetch_assoc();
                $aptid = $result['appointmentid'];
            }

        
        }else{
            echo "Error preparing statement: " . $conn->error;
        }

        if ($stmt = $conn->prepare($appdeletesql)) {
            $stmt->bind_param("i", $aptid);
            
            if ($stmt->execute()) {
            } else {
                echo "Error deleting record: " . $conn->error;
            }
            $stmt->close();
        }else{
            echo "Error preparing statement: " . $conn->error;
        }

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