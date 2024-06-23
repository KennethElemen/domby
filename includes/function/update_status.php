<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentId = $_POST['payment_id'];
    $newStatus = $_POST['new_status'];
    $reason = $_POST['Reason']; // Get the reason from the request

    // Check if the new status is "Rejected" and reason is empty
    if ($newStatus === 'Rejected' && empty($reason)) {
        echo "Reason is required for Rejected status. Operation stopped.";
        exit; // Stop the operation
    }

    include '../../includes/config/dbconn.php';

    // Log received data
    error_log("PaymentID: $paymentId, NewStatus: $newStatus, Reason: $reason");

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the status and reason in the database based on the payment ID
    $updateSql = "UPDATE payment SET Status = ?, Reason = ? WHERE PaymentID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssi", $newStatus, $reason, $paymentId);

    if ($updateStmt->execute()) {
        echo "Status and reason updated successfully";
    } else {
        echo "Error updating status and reason: " . $updateStmt->error;
    }

    // Close the prepared statement
    $updateStmt->close();
    $conn->close();
}


?>
