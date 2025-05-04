<?php
require_once("required/authenticated.php");
require_once("required/config.php");

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];

$aptStmt = "SELECT 
    aa.availabilityid,
    aa.appointmentid,
    a.instructorid,
    a.timeslot,
    ap.project_name,
    GROUP_CONCAT(u.username) AS usernames
FROM 
    appointment_availability aa
LEFT JOIN 
    availability a ON aa.availabilityid = a.availabilityid
LEFT JOIN 
    appointments ap ON aa.appointmentid = ap.appointmentid
LEFT JOIN 
    user_appointments ua ON aa.appointmentid = ua.appointmentid
LEFT JOIN 
    users u ON ua.userid = u.userid
WHERE
    a.instructorid = ?
GROUP BY 
    aa.availabilityid, aa.appointmentid , a.instructorid, a.timeslot, ap.project_name
ORDER BY
    a.timeslot;" ;

$stmt = $conn->prepare($aptStmt);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "i", $userid);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    echo("<table class='table table-bordered table-striped'>".
            "<tr>
            <td>Group Name</td>
            <td>Group Members</td>
            <td>Presentation Date</td>
            <td>Presentation Time</td>
            <td>Extra</td>
            </tr>");

    while ($row = mysqli_fetch_assoc($result))
        {
            list($date, $time) = explode(' ', $row["timeslot"]);

            echo("<tr><td>".$row["project_name"]."</td><td>".$row["usernames"]."</td><td>".$date."</td><td>".$time."</td>
            <td><a href='updateappointment.php?id=" . $row["appointmentid"] . "'>edit</a> <a href='deleteappointment.php?id=" . $row["appointmentid"] . "'>delete</a></td></tr>");
        }

    echo("</table>");
}
?>

