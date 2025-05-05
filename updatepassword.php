
<?php 
require_once("required/config.php");

// **Important:** Specify the user ID you want to update and the new plain-text password
$userIdToUpdate = 4; // Replace with the actual user ID
$newPlainPassword = "pass"; // Replace with the new password

// Hash the new password using PHP's password_hash() (RECOMMENDED)
$hashedPassword = password_hash($newPlainPassword, PASSWORD_DEFAULT);

// Prepare the SQL statement
$sql = "UPDATE users SET password = ? WHERE userid = ?";

// Create a prepared statement
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind the parameters
    $stmt->bind_param("si", $hashedPassword, $userIdToUpdate); // "s" for string (hash), "i" for integer (userid)

    // Execute the prepared statement
    if ($stmt->execute()) {
        $rowsAffected = $stmt->affected_rows;
        if ($rowsAffected > 0) {
            echo "Password for user ID " . $userIdToUpdate . " updated successfully using bcrypt (password_hash()).";
        } else {
            echo "No user found with ID " . $userIdToUpdate . ".";
        }
    } else {
        // Error executing the statement
        echo "Error updating password: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // Error preparing the statement
    echo "Error preparing statement: " . $conn->error;
}

// Close the database connection
$conn->close();

?>