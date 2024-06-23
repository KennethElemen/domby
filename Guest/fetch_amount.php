<?php
// Include your database connection file
include '../includes/config/dbconn.php';
// Assuming $conn is your database connection

// Check if the selected MonthYear and email ID are received via POST
if (isset($_POST['monthYear']) && isset($_POST['email'])) {
    $selectedMonthYear = $_POST['monthYear'];
    $loggedInEmail = $_POST['email'];

    // Fetch the amount based on the selected MonthYear and logged-in email ID
    $stmt = $conn->prepare("SELECT Amount FROM paymentschedule WHERE MonthYear = ? AND EmailID = ? AND Status = 'Pending'");
    $stmt->bind_param("ss", $selectedMonthYear, $loggedInEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Return the fetched amount as the response
        echo $row['Amount'];
    } else {
        // Handle the case where no amount is found for the selected MonthYear and email ID
        echo '0'; // or any default value you prefer
    }

    $stmt->close();
} else {
    // Handle the case where the selected MonthYear and/or email ID are not received
    echo '0'; // or any default value you prefer
}

// Close the database connection
$conn->close();
?>
