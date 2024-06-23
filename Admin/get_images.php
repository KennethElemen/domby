<?php
 include '../../includes/config/dbconn.php';

$roomId = $_GET['roomId'];

// Establish a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Fetch image URLs for the specific room ID from the database
$sql = "SELECT images FROM room_management WHERE roomID = '$roomId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $images = explode(',', $row['images']);
    echo implode(',', $images);
} else {
    echo "No images found";
}

// Close the database connection
$conn->close();
?>