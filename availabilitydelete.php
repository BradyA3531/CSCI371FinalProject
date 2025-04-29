<?php
    require_once("required/authenticated.php");
    require_once("required/config.php");

    if(isset($_GET['availabilityid'])){
        $availabilityid = $_GET['availabilityid'];

        $newsql = "SELECT * FROM appointment_availability WHERE availabilityid = ?";

        if ($stmt = $conn->prepare($newsql)) {
            $stmt->bind_param("i", $availabilityid);
            
            $stmt->execute();

            if($stmt){
                $result = $stmt->get_result()->fetch_assoc();
                $aptid = $result['appointmentid'];
            }    
        }

        $appdeletesql = "DELETE FROM appointments WHERE appointmentid = ?";

        if ($stmt = $conn->prepare($appdeletesql)) {
            $stmt->bind_param("i", $aptid);
            
            if ($stmt->execute()) {
            } 
        }

        $sql = "DELETE FROM availability WHERE availabilityid = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $availabilityid);
            
            $stmt->execute();

            if ($stmt) {
                header("Location: teacher_availability.php");
                exit;
            } 
            $stmt->close();
        }
        
    }
    $conn->close();
?>