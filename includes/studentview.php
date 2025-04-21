<?php

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];

$aptStmt = "SELECT a.appointmentid, a.appointment_time, a.project_name
            FROM users u
            JOIN user_appointments ua ON u.userid = ua.userid
            JOIN appointments a ON ua.appointmentid = a.appointmentid
            WHERE u.userid = ?";

$stmt = $conn->prepare($aptStmt);

if ($stmt) {

    $stmt->bind_param("i", $userid);
    $stmt->execute();

    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();

    $stmt->close();
    $result->free();

    if($appointment){
        $studentsStmt = "SELECT u.username
                FROM appointments a
                JOIN user_appointments ua ON a.appointmentid = ua.appointmentid
                JOIN users u ON ua.userid = u.userid
                WHERE a.appointmentid = ?";

        $stmt = $conn->prepare($studentsStmt);

        if($stmt){
            $stmt->bind_param("i", $appointment["appointmentid"]);
            $stmt->execute();
        
            $result = $stmt->get_result();
            $students = $result->fetch_assoc();
        
            $stmt->close();
            $result->free();
        }
    }
}
?>

<div class="studentview">
    <?php if($appointment): ?>

    <?php else: ?>
        <div class="text-center">
            <h3 class="text-center">No appointment has been scheduled...</h3>
            <a type="button" class="btn btn-light" href="student_schedule.php">Schedule appointment?</a>
        </div>
      
    <?php endif; ?>
</div>