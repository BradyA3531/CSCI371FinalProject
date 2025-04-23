<?php

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];

$aptStmt = "SELECT a.appointmentid, a.project_name
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

    if ($appointment) {
        $studentsStmt = "SELECT u.username
                FROM appointments a
                JOIN user_appointments ua ON a.appointmentid = ua.appointmentid
                JOIN users u ON ua.userid = u.userid
                WHERE a.appointmentid = ?";

        $stmt = $conn->prepare($studentsStmt);

        if ($stmt) {
            $stmt->bind_param("i", $appointment["appointmentid"]);
            $stmt->execute();

            $students = $stmt->get_result();

            $stmt->close();
        }

        $availabilityStmt = "SELECT
                            av.timeslot
                        FROM availability av
                        JOIN appointment_availability aa ON av.availabilityid = aa.availabilityid
                        WHERE aa.appointmentid = ?; ";

        $stmt = $conn->prepare($availabilityStmt);

        if ($stmt) {
            $stmt->bind_param("i", $appointment["appointmentid"]);
            $stmt->execute();

            $availability = $stmt->get_result()->fetch_assoc();

            $stmt->close();
        }
    }
}
?>

<?php if ($appointment): ?>
    <div class="studentview">
        <div class="container student-container">
            <h1>Project: <?php echo $appointment["project_name"] ?></h1>
            <div class="row">
                <div class="col">
                    <h5>Presentation Timeslot</h5>
                    <p><?php echo $availability["timeslot"] ?></p>
                </div>
                <div class="col">
                    <h5>Group Members</h5>
                    <?php while ($row = $students->fetch_assoc()): ?>
                        <p><?php echo $row["username"] ?></p>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="row d-flex justify-content-end">
                <a class="btn btn-primary w-25" href="updateappointment.php?id=<?php echo $appointment["appointmentid"] ?>">Update</a>
                <a class="btn btn-danger w-25" href="deleteappointment.php?id=<?php echo $appointment["appointmentid"] ?>" onclick="return confirm('Are you sure you want to delete the appointment for <?php echo $appointment['project_name'] ?>?');">Delete</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="text-center">
        <h3 class="text-center">No appointment has been scheduled...</h3>
        <a type="button" class="btn btn-light" href="scheduleappointment.php">Schedule appointment?</a>
    </div>
<?php endif; ?>