<?php
require_once("required/authenticated.php");
require_once("required/config.php");

$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];
$userid = $_SESSION['userid'];

include("includes/header.php");

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date']; // Example: 2025-04-27
    $time = $_POST['time']; // Example: 14:00:00

    if (!empty($date) && !empty($time)) {
        $datetime = $date . ' ' . $time; // Combine into 'YYYY-MM-DD HH:MM:SS'

        // Now you can safely use $datetime in your SQL insert
        $stmt = $conn->prepare('INSERT INTO availability (instructorid, timeslot) VALUES (?, ?)');
        $stmt->bind_param('is', $userid, $datetime); // 'i' = integer, 's' = string
        $stmt->execute();

        // Redirect to the same page to avoid resubmission on refresh
        header("Location: ".$_SERVER['PHP_SELF']);
        exit; // Stop further script execution after the redirect
    }
}

?>

<form method="POST" action="">
  <label for="date">Select Date:</label>
  <input type="date" name="date" id="date" required>

  <label for="time">Select Time:</label>
  <select name="time" id="time" required>
    <?php
    // Set the start and end time
    $startTime = strtotime("08:00 AM");
    $endTime = strtotime("04:00 PM");

    // Loop through every 20 minutes
    while ($startTime <= $endTime) {
        $formattedTime = date("H:i", $startTime);
        echo "<option value='$formattedTime'>$formattedTime</option>";

        // Increment by 20 minutes
        $startTime = strtotime("+20 minutes", $startTime);
    }
    ?>
  </select>

  <button type="submit">Submit</button>
</form>

<h2>Current Availabilities</h2>

<table class='table table-bordered'>
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Extra</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Query to fetch the teacher's availability
        $query = "SELECT * FROM availability WHERE instructorid = ? ORDER BY timeslot";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userid); // Bind the user ID as an integer
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Loop through and display the availability records
        while ($row = mysqli_fetch_array($result)) {
            $date = date("Y-m-d", strtotime($row['timeslot'])); // Format the date
            $time = date("H:i", strtotime($row['timeslot']));  // Format the time
            echo "<tr>";
            echo "<td>$date</td>";
            echo "<td>$time</td>";
            echo "<td><a href='availabilitydelete.php?availabilityid=" . $row["availabilityid"] . "'>Delete</a>
            <a href='availabilityedit.php?availabilityid=" . $row["availabilityid"] . "'>edit</a></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
include("includes/footer.php");
?>
