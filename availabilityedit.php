<?php
require_once("required/authenticated.php");
require_once("required/config.php");

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];

$initialDate = isset($_GET['date']) ? $_GET['date'] : '';
$initialTime = isset($_GET['time']) ? $_GET['time'] : '';

include("includes/header.php");

$availabilityid = $_GET['availabilityid'];

$stmt = mysqli_prepare($conn, "SELECT * FROM availability WHERE availabilityid = ?");
mysqli_stmt_bind_param($stmt, "i", $availabilityid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $timeslot = $row["timeslot"];

} else {
    $timeslot = ""; // Default value if no record found
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time']; 
    $availabilityid = $row["availabilityid"]; // The ID of the availability to update

    if (!empty($date) && !empty($time)) {
        $datetime = $date . ' ' . $time; // Combine into 'YYYY-MM-DD HH:MM:SS'

        if (empty($availabilityid)) {
            // Insert new availability if no availability_id provided
            $stmt = $conn->prepare('INSERT INTO availability (instructorid, timeslot) VALUES (?, ?)');
            $stmt->bind_param('is', $userid, $datetime); // 'i' = integer, 's' = string
            $stmt->execute();
            echo "New availability added!";
        } else {
            // Update existing availability if availability_id is provided
            $stmt = $conn->prepare('UPDATE availability SET timeslot = ? WHERE availabilityid = ? AND instructorid = ?');
            $stmt->bind_param('sii', $datetime, $availabilityid, $userid); // 's' = string for datetime, 'i' = integer for availability_id and instructorid
            $stmt->execute();
            echo "Availability updated successfully!";
        }

        header("Location: teacher_availability.php");
        exit; // Stop further script execution after the redirect
    } else {
        echo "Date or time missing!";
    }
}

?>


<form method="POST" action="">
  <label for="date">Select Date:</label>
  <input type="date" name="date" id="date" required value="<?php echo htmlspecialchars($initialDate); ?>">

  <label for="time">Select Time:</label>
  <select name="time" id="time" required>
    <?php
    // Set the start and end time
    $startTime = strtotime("08:00 AM");
    $endTime = strtotime("04:00 PM");

    // Loop through every 20 minutes
    while ($startTime <= $endTime) {
        $formattedTime = date("H:i", $startTime);
        $selected = ($formattedTime === $initialTime) ? "selected" : "";
        echo "<option value='$formattedTime' $selected>$formattedTime</option>";
        $startTime = strtotime("+20 minutes", $startTime);
    }
    ?>
  </select>

  <button type="submit" class="btn btn-primary">Submit</button>
</form>



<?php
include("includes/footer.php")

?>