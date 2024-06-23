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

$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    error_log("Connection failed: " . $dbConnection->connect_error);
    die();
}

function determineBillingContent($remainingDays) {
    if ($remainingDays == 0) {
        return "Your rent is due today. Please make sure to submit your payment to avoid any inconvenience.";
    } elseif ($remainingDays == 3) {
        return "Just 3 days left before your rent is due. Kindly prepare your payment.";
    } elseif ($remainingDays == 7) {
        return "Only 1 week left before your rent is due. If you have any concerns regarding your payment, please contact the admin.";
    } else {
        return "Your rent is due soon. Please ensure your payment is submitted on time. Remaining days: $remainingDays";
    }
}

function sendBillingReminder($userEmail, $billingContent) {
    global $dbConnection;

    // Check if a billing reminder has already been sent today
    $todayDate = date("Y-m-d");
    $queryCheckReminder = "SELECT * FROM Billing_notif WHERE email = ? AND last_sent_date = ?";
    $stmtCheckReminder = $dbConnection->prepare($queryCheckReminder);
    $stmtCheckReminder->bind_param("ss", $userEmail, $todayDate);
    if (!$stmtCheckReminder->execute()) {
        echo "Failed to execute statement: " . $stmtCheckReminder->error . "\n";
        return;
    }
    $resultCheckReminder = $stmtCheckReminder->get_result();
    $stmtCheckReminder->close();

    if ($resultCheckReminder->num_rows > 0) {
        echo "Billing reminder already sent today for $userEmail. Skipping reminder.\n";
        return;
    }

    // If no billing reminder has been sent today, proceed with sending the reminder email
    $subject = 'Reminder: Rent Payment Due';

    // Construct email message
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="Billing Reminder">
            <style type="text/css">
                a:hover {
                    text-decoration: underline !important;
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f2f3f8; font-family: Arial, sans-serif;">
            <table cellspacing="0" align="center" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="font-family: Arial, sans-serif;">
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 670px; margin: 0 auto; background-color: #ffffff; border-radius: 3px; box-shadow: 0 6px 18px 0 rgba(0,0,0,.06);">
                            <tr>
                                <td style="height: 40px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 0 35px;">
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Rent Payment Reminder</h1>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Hello, $userEmail
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        $billingContent
                                    </p>
                                     <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Log in to Dormbell:
                                    </p>
                                    <center>
                                        <a href="https://dormbell.online/Guest/login.php" style="background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;">Log in</a>
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 40px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
HTML;

    $headers = 'From: DormBell' . "\r\n" .
               'X-Mailer: PHP/' . phpversion() . "\r\n" .
               'MIME-Version: 1.0' . "\r\n" .
               'Content-Type: text/html; charset=utf-8';

    // Send email
    if (mail($userEmail, $subject, $message, $headers)) {
        echo "Billing reminder sent successfully to $userEmail.\n";

        // Insert into billing notification logs
        $queryInsertReminder = "INSERT INTO Billing_notif (email, last_sent_date) VALUES (?, ?)";
        $stmtInsertReminder = $dbConnection->prepare($queryInsertReminder);
        $stmtInsertReminder->bind_param("ss", $userEmail, $todayDate);
        if (!$stmtInsertReminder->execute()) {
            echo "Failed to insert into billing notification logs: " . $stmtInsertReminder->error . "\n";
        }
        $stmtInsertReminder->close();
    } else {
        echo "Failed to send billing reminder to $userEmail.\n";
    }
}

// Fetch records from tenantprofile where status is pending
$queryProfiles = "SELECT EmailID, rent_due_date FROM paymentschedule WHERE status = 'pending'";
$resultProfiles = $dbConnection->query($queryProfiles);

if ($resultProfiles && $resultProfiles->num_rows > 0) {
    while ($rowProfile = $resultProfiles->fetch_assoc()) {
        $userEmail = $rowProfile['EmailID'];
        $rentDueDate = new DateTime($rowProfile['rent_due_date']);

        // Get current date
        $currentDate = new DateTime();

        // Calculate remaining days
        $remainingDays = $currentDate->diff($rentDueDate)->format('%a');

        // If remaining days is less than 7, send billing reminder
        if ($remainingDays < 7) {
            $billingContent = determineBillingContent($remainingDays);
            sendBillingReminder($userEmail, $billingContent);
        }
    }
} else {
    echo "No pending profiles found.\n";
}

$dbConnection->close();
?>
