<?php
$servername = "localhost";
$username = "u533384272_Dormbell";
$password = "D[1PUy4lO@";
$dbname = "u533384272_Dormbell";

// Function to handle errors
function errorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Error: [$errno] $errstr in $errfile at line $errline");
    die();
}

// Set error handler
set_error_handler("errorHandler");

echo "Connecting to database...\n";
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    error_log("Connection failed: " . $dbConnection->connect_error);
    die("Failed to connect to database: " . $dbConnection->connect_error);
}

echo "Connected to database successfully.\n";

function sendEmail($userEmail, $message) {
    global $dbConnection;
}

// Fetch current date
$currentDate = new DateTime();

// Fetch records from reservation table
echo "Fetching reservations older than 3 days...\n";
$queryReservations = "SELECT * FROM reservations WHERE status = 'accepted' AND DATEDIFF(NOW(), date) > 3";
$resultReservations = $dbConnection->query($queryReservations);

// Check if query execution was successful
if (!$resultReservations) {
    echo "Failed to fetch reservations: " . $dbConnection->error . "\n";
    $dbConnection->close();
    die();
}

// Check if any reservations were found
if ($resultReservations->num_rows === 0) {
    echo "No accepted reservations older than 3 days found.\n";
    $dbConnection->close();
    exit();
}

// Process fetched reservations
while ($rowReservation = $resultReservations->fetch_assoc()) {
    $userEmail = $rowReservation['email'];
    $reservationDate = new DateTime($rowReservation['reservation_date']);
    
    // Send email notification
    echo "Sending email to tenant: $userEmail\n";
    $message = "Dear Tenant,\n\n";
    $message .= "We regret to inform you that your reservation made on " . $reservationDate->format('Y-m-d') . " has been canceled due to non-payment of the down payment.\n";
    $message .= "Best Regards,\nYour Dorm Management Team";
    sendEmail($userEmail, $message);
    
    // Echo tenant information
    echo "Removing reservation for tenant: $userEmail\n";
    
    // Remove the reservation
    $queryRemoveReservation = "DELETE FROM reservations WHERE email = ?";
    $stmtRemoveReservation = $dbConnection->prepare($queryRemoveReservation);
    
    // Check if the preparation of the statement was successful
    if (!$stmtRemoveReservation) {
        echo "Failed to prepare statement: " . $dbConnection->error . "\n";
        continue;
    }
    
    $stmtRemoveReservation->bind_param("s", $userEmail);
    
    // Check if the parameters binding was successful
    if (!$stmtRemoveReservation->execute()) {
        echo "Failed to remove reservation: " . $stmtRemoveReservation->error . "\n";
        $stmtRemoveReservation->close();
        continue;
    }
    
    $stmtRemoveReservation->close();
}

$dbConnection->close();
?>
