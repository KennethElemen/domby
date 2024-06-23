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

function determineContractContent($remainingDays) {
    if ($remainingDays == 0) {
        return "Today is your checkout day! We hope you had a pleasant stay at DormBell. Thank you for choosing us!";
    } elseif ($remainingDays == 3) {
        return "Just 3 days left before your checkout from DormBell. We hope you've enjoyed your stay with us!";
    } elseif ($remainingDays == 7) {
        return "Only 1 week left before your checkout from DormBell. Add more memories to your stay by extending your stay with us just contact the admin!";
    } else {
        return "Your stay is nearly ending! Don't miss out on our exclusive offer to extend your stay. Experience more moments of comfort and relaxation. Remaining days: $remainingDays";
    }
}

function sendEmail($userEmail, $contractContent) {
    global $dbConnection;

    // Check if an email has already been sent today
    $todayDate = date("Y-m-d");
    $queryCheckReminder = "SELECT * FROM reminder_logs WHERE email = ? AND last_sent_date = ?";
    $stmtCheckReminder = $dbConnection->prepare($queryCheckReminder);
    $stmtCheckReminder->bind_param("ss", $userEmail, $todayDate);
    if (!$stmtCheckReminder->execute()) {
        echo "Failed to execute statement: " . $stmtCheckReminder->error . "\n";
        return;
    }
    $resultCheckReminder = $stmtCheckReminder->get_result();
    $stmtCheckReminder->close();

    if ($resultCheckReminder->num_rows > 0) {
        echo "Email already sent today for $userEmail. Skipping email.\n";
        return;
    }

    // If email has not been sent today, proceed with sending the email
    $subject = 'Reminder: Your Stay is Ending Soon';

    // Construct email message
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="Contract Details">
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
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Your stay is almost over</h1>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Hello, $userEmail
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        $contractContent
                                    </p>
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
        echo "Email sent successfully to $userEmail.\n";

        // Insert into reminder logs
        $queryInsertReminder = "INSERT INTO reminder_logs (email, last_sent_date) VALUES (?, ?)";
        $stmtInsertReminder = $dbConnection->prepare($queryInsertReminder);
        $stmtInsertReminder->bind_param("ss", $userEmail, $todayDate);
        if (!$stmtInsertReminder->execute()) {
            echo "Failed to insert into reminder logs: " . $stmtInsertReminder->error . "\n";
        }
        $stmtInsertReminder->close();
    } else {
        echo "Failed to send email to $userEmail.\n";
    }
}

function updateTenantStatus($userEmail, $status) {
    global $dbConnection;

    // Update tenant status
    $queryUpdateStatus = "UPDATE tenants SET status = ? WHERE EmailID = ?";
    $stmtUpdateStatus = $dbConnection->prepare($queryUpdateStatus);
    $stmtUpdateStatus->bind_param("ss", $status, $userEmail);
    if (!$stmtUpdateStatus->execute()) {
        echo "Failed to update tenant status: " . $stmtUpdateStatus->error . "\n";
    }
    $stmtUpdateStatus->close();
}

// Fetch records from tenantprofile
$queryProfiles = "SELECT email, check_in_date, check_out_date FROM tenantprofile";
$resultProfiles = $dbConnection->query($queryProfiles);

if ($resultProfiles && $resultProfiles->num_rows > 0) {
    while ($rowProfile = $resultProfiles->fetch_assoc()) {
        $userEmail = $rowProfile['email'];
        $checkInDate = new DateTime($rowProfile['check_in_date']);
        $checkOutDate = new DateTime($rowProfile['check_out_date']);

        // Get current date
        $currentDate = new DateTime();

        // Calculate remaining days
        $remainingDays = $currentDate->diff($checkOutDate)->format('%r%a');

        // If remaining days is less than 7, send email
        if ($remainingDays < 7 && $remainingDays >= 0) {
            $contractContent = determineContractContent($remainingDays);
            sendEmail($userEmail, $contractContent);
        }

        // Update tenant status to 'active' if remaining days greater than 0
        if ($remainingDays > 0) {
            updateTenantStatus($userEmail, 'active');
        }
    }
} else {
    echo "No profiles found.\n";
}

$dbConnection->close();
?>
