<?php 
require_once("required/authenticated.php");
require_once("required/config.php");

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

$studentsStmt = "SELECT u.userid
                FROM appointments a
                JOIN user_appointments ua ON a.appointmentid = ua.appointmentid
                JOIN users u ON ua.userid = u.userid
                WHERE a.appointmentid = ?";

$stmt = $conn->prepare($studentsStmt);

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $students = $stmt->get_result()->fetch_assoc();

    $stmt->close();
}

$deleteAppointmentStmt = "DELETE FROM appointments
                          WHERE appointmentid = ?; ";

$stmt = $conn->prepare($deleteAppointmentStmt);

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    header("Location: deleteresponse.php?success=true&message=successfully+deleted+the+appointment");
    exit();
} else {
    header("Location: deleteresponse.php?success=false&message=Deletion+failed");
    exit();
}

?>