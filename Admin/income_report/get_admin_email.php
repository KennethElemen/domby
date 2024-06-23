<?php
// Include database connection
include '../../includes/config/dbconn.php';

// Function to get the email of the admin
function getAdminEmail($conn) {
    $sql = "SELECT Email FROM admins LIMIT 1"; // Assuming only one admin for simplicity
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Email'];
    } else {
        return null;
    }
}

// Get admin email



?>
