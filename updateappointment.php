<?php
require_once("required/authenticated.php");
require_once("required/config.php");

$appointmentStmt = "SELECT * FROM appointments WHERE appointmentid = ?;";

$stmt = $conn->prepare($appointmentStmt);
if ($stmt) {
    $stmt->bind_param("i", $_GET["id"]);
    $stmt->execute();

    $appointment = $stmt->get_result()->fetch_assoc();
}

$studentsStmt = "SELECT 
                        u.*,
                        MAX(CASE WHEN ua.appointmentid IN (?) THEN 1 ELSE 0 END) AS is_member
                    FROM users u
                    LEFT JOIN user_appointments ua ON u.userid = ua.userid
                    WHERE 
                        u.userrole = 'student'
                        AND u.userid != ?
                        AND (
                            ua.appointmentid IN (?)
                            OR ua.appointmentid IS NULL
                        )
                    GROUP BY u.userid";

$stmt = $conn->prepare($studentsStmt);

if ($stmt) {
    $stmt->bind_param("iii", $appointment["appointmentid"], $_SESSION["userid"], $appointment["appointmentid"]);
    $stmt->execute();

    $students = $stmt->get_result();
}

$availabilityStmt = "SELECT
                        a.availabilityid,
                        a.timeslot,
                        CASE
                            WHEN aa.availabilityid IS NOT NULL THEN TRUE
                            ELSE FALSE
                        END AS has_appointment
                    FROM
                        availability a
                    LEFT JOIN
                        appointment_availability aa ON a.availabilityid = aa.availabilityid;";

$stmt = $conn->prepare($availabilityStmt);

if ($stmt) {
    $stmt->execute();

    $timeslots = $stmt->get_result();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $projectName = $_POST["projectname"];
    $timeslot = $_POST["timeslot"];

    if (isset($_POST["group_members"])) {
        $groupMembers = $_POST["group_members"];
    }

    $newMemberInsertStmt = "INSERT INTO user_appointments (userid, appointmentid)
                            SELECT ?, ?
                            FROM dual
                            WHERE NOT EXISTS (
                                SELECT 1 
                                FROM user_appointments 
                                WHERE userid = ? AND appointmentid = ?
                            )";

    $stmt = $conn->prepare($newMemberInsertStmt);

    if ($stmt) {
        foreach ($groupMembers as $member) {
            $stmt->bind_param('iiii', $member, $appointment["appointmentid"], $member, $appointment["appointmentid"]);
            $stmt->execute();
        }
    }

    $studentsStmt = $query = "SELECT u.*
                                FROM users u
                                INNER JOIN user_appointments ua ON u.userid = ua.userid
                                WHERE ua.appointmentid = ?
                                AND u.userid != ?;";

    $stmt = $conn->prepare($studentsStmt);

    if($stmt){
        $stmt->bind_param('ii',$appointment["appointmentid"], $_SESSION["userid"]);
        $stmt->execute();

        $studentsUpdate = $stmt->get_result();
    }

    $deleteUserAptStmt = "DELETE FROM user_appointments WHERE userid = ?";

    $stmt = $conn->prepare($deleteUserAptStmt);

    if($stmt){
        while($student = $studentsUpdate->fetch_assoc()){
            if(!in_array($student["userid"], $groupMembers)){
                $stmt->bind_param('i', $student["userid"]);
                $stmt->execute();
            }
        }
    }

    $projectNameUpdateStmt = "UPDATE appointments SET project_name = ? WHERE appointmentid = ?";

    $stmt = $conn->prepare($projectNameUpdateStmt);

    if($stmt){
        $stmt->bind_param('si', $projectName, $appointment["appointmentid"]);
        $stmt->execute();
    }

    $timeslotUpdateStmt = "UPDATE appointment_availability SET availabilityid = ? WHERE appointmentid = ?";

    $stmt = $conn->prepare($timeslotUpdateStmt);

    if($stmt){
        $stmt->bind_param('ii', $timeslot, $appointment["appointmentid"]);
        $stmt->execute();
    }

    header("Location: dashboard.php");
    exit();

}

include("includes/header.php");
?>


<body>
    <div class="content">
        <div class="container d-flex justify-content-center">
            <form method="POST" class="scheduleappointment">
                <h2>Update an Appointment</h2>
                <div class="form-group">
                    <label for="projectname" class="form-label">Project Name</label>
                    <input type="text" class="form-control" id="projectname" name="projectname" placeholder="Project Name" value="<?php echo $appointment["project_name"] ?>" required>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="timeslot" class="form-label">Select Timeslot</label>
                        <select name="timeslot" id="timeslot" class="form-select" aria-label="Default select example" required>
                            <?php while ($row = $timeslots->fetch_assoc()): ?>
                                <option value="<?php echo $row["availabilityid"] ?>" <?php if ($row["has_appointment"]) echo "disabled" ?>>
                                    <?php echo $row["timeslot"] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="bootstrapMultiselect" class="form-label">Select Group Members (you are already added by default)</label>
                        <select class="form-select" id="group_members" name="group_members[]" multiple>
                            <?php while ($row = $students->fetch_assoc()): ?>
                                <option value="<?php echo $row["userid"] ?>" <?php if ($row["is_member"]) echo "selected" ?>>
                                    <?php echo $row["username"] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <p class="form-text">Hold down Ctrl (or Cmd on Mac) to select multiple options.</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a type="submit" class="btn btn-danger" href="dashboard.php">Cancel</a>
            </form>
        </div>
    </div>
</body>


<?php include("includes/footer.php"); ?>