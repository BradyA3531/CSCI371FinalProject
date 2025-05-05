<?php
require_once("required/authenticated.php");
require_once("required/config.php");

$studentsStmt = "SELECT
                    u.username, u.userid,
                    CASE
                        WHEN ua.userid IS NOT NULL THEN TRUE
                        ELSE FALSE
                END AS has_appointment
                FROM
                    users u
                LEFT JOIN
                    user_appointments ua ON u.userid = ua.userid
                WHERE u.userrole = 'student'
                AND u.userid != ?;";

$stmt = $conn->prepare($studentsStmt);

if ($stmt) {
    $stmt->bind_param("i", $_SESSION["userid"]);

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

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $projectName = $_POST["projectname"];
    $timeslot = $_POST["timeslot"];

    if (isset($_POST["group_members"])) {
        $groupMembers = $_POST["group_members"];
    }

    $aptInsertStmt = "INSERT INTO appointments (project_name) VALUES (?)";

    $stmt = $conn->prepare($aptInsertStmt);

    if ($stmt) {
        $stmt->bind_param("s", $projectName);

        $stmt->execute();

        $appointmentid = $stmt->insert_id;
    }

    $aptAvlInsertStmt = "INSERT INTO appointment_availability (appointmentid, availabilityid) VALUES (?,?)";

    $stmt = $conn->prepare($aptAvlInsertStmt);

    if ($stmt) {
        $stmt->bind_param("ii", $appointmentid, $timeslot);

        $stmt->execute();
    }

    $userAptInsertStmt = "INSERT INTO user_appointments (userid, appointmentid) VALUES (?,?)";

    $stmt = $conn->prepare($userAptInsertStmt);

    if ($stmt) {

        if($_SESSION['userrole'] == 'student')
        {
            $stmt->bind_param("ii", $_SESSION["userid"], $appointmentid);

            $stmt->execute();
        }
        if ($groupMembers) {
            foreach ($groupMembers as $member) {
                $stmt->bind_param("ii", $member, $appointmentid);
                $stmt->execute();
            }
        }
    }

    header("Location: dashboard.php");
    exit();
}

include("includes/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <div class="content">
        <div class="container d-flex justify-content-center">
            <form method="POST" class="scheduleappointment">
                <h2>Schedule an Appointment</h2>
                <div class="form-group">
                    <label for="projectname" class="form-label">Project Name</label>
                    <input type="text" class="form-control" id="projectname" name="projectname" placeholder="Project Name" required>
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
                                <option value="<?php echo $row["userid"] ?>" <?php if ($row["has_appointment"]) echo "disabled" ?>>
                                    <?php echo $row["username"] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <p class="form-text">Hold down Ctrl (or Cmd on Mac) to select multiple options.</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>


<?php include("includes/footer.php"); ?>