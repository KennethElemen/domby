<?php
include '../../includes/config/dbconn.php';

$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_submit'])) {
    // Retrieve data from the form and sanitize
    $updateTypeOfStay = mysqli_real_escape_string($dbConnection, $_POST['type_of_stay']);
    $updateCheckInDate = date('Y-m-d', strtotime($_POST['update_check_in_date']));
    $updateCheckOutDate = date('Y-m-d', strtotime($_POST['update_check_out_date']));
    $tenantID = mysqli_real_escape_string($dbConnection, $_POST['tenant_id']);

    // Update the database
    $updateQuery = "UPDATE tenantprofile SET Type_of_stay = '$updateTypeOfStay', check_in_date = '$updateCheckInDate', check_out_date = '$updateCheckOutDate' WHERE ID = $tenantID";

    if ($dbConnection->query($updateQuery) === TRUE) {
        // Set a session variable to indicate successful update
        session_start();
        $_SESSION['updateSuccess'] = true;
        // Redirect to the customer_management.php page
        header("Location: ../../admin/customer_management/customer_management.php");
        exit();
    } else {
        echo "Error updating record: " . $dbConnection->error;
    }
} else {
    echo "Invalid request. Please submit the form.";
}

$dbConnection->close();
?>
