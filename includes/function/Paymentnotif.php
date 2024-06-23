<?php
$servername = "localhost";
$username = "u533384272_Dormbell";
$password = "D[1PUy4lO@";
$dbname = "u533384272_Dormbell";

// Function to handle errors
function errorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Error: [$errno] $errstr in $errfile at line $errline");
}

// Set error handler
set_error_handler("errorHandler");

// Function to send email notification to admin
function sendAdminNotification($adminEmail, $pendingPaymentsCount) {
    // Construct email...
    // Construct email
    $subject = 'New Payment Notification';
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
          
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
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Paymnet Notif</h1>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                     New Payment Alert:
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                      Hello there! 🎉 Great news! 🎉 You have just received a new payment. Please review the payment! 💼💰✨

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

    // Set email headers
    $headers = 'From: DormBell' . "\r\n" .
               'X-Mailer: PHP/' . phpversion() . "\r\n" .
               'MIME-Version: 1.0' . "\r\n" .
               'Content-Type: text/html; charset=utf-8';

    // Send email
    if (mail($adminEmail, $subject, $message, $headers)) {
        error_log("Email sent successfully to admin.");
    } else {
        error_log("Failed to send email to admin.");
    }
}

// Function to get pending payments count and send notification
function getPendingPaymentsCount($servername, $username, $password, $dbname) {
    // Connect to the database
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        error_log("Connection failed: " . $dbConnection->connect_error);
        return;
    }

    // Query to get count of pending payments
    $pendingPaymentsQuery = "SELECT COUNT(*) as pending_payments FROM payment WHERE status = 'pending'";
    $pendingPaymentsResult = mysqli_query($dbConnection, $pendingPaymentsQuery);

    if (!$pendingPaymentsResult) {
        error_log('Error: ' . mysqli_error($dbConnection));
        mysqli_close($dbConnection);
        return;
    }

    // Fetch pending payments count
    $pendingPaymentsRow = mysqli_fetch_assoc($pendingPaymentsResult);
    $pendingPaymentsCount = $pendingPaymentsRow['pending_payments'];

    // Close database connection
    mysqli_close($dbConnection);

    // Check if the previous count file exists
    $prevCountFile = '/home/u533384272/domains/dormbell.online/public_html/includes/function/prev_pending_payments_count.txt';
    if (!file_exists($prevCountFile)) {
        // Create the file if it doesn't exist
        file_put_contents($prevCountFile, $pendingPaymentsCount);
    }

    // Read the previous count from the file
    $prevCount = intval(file_get_contents($prevCountFile));

    // Compare current count with previous count
    if ($pendingPaymentsCount > $prevCount) {
        // Send notification if there are new pending payments
        // Connect again to get admin's email
        $dbConnection = new mysqli($servername, $username, $password, $dbname);
        $adminEmailQuery = "SELECT Email FROM admins LIMIT 1";
        $adminEmailResult = mysqli_query($dbConnection, $adminEmailQuery);

        if (!$adminEmailResult) {
            error_log('Error: ' . mysqli_error($dbConnection));
            mysqli_close($dbConnection);
            return;
        }

        // Fetch the admin's email
        $adminEmailRow = mysqli_fetch_assoc($adminEmailResult);
        $adminEmail = $adminEmailRow['Email'];

        // Close database connection
        mysqli_close($dbConnection);

        // Check if admin email is empty
        if (empty($adminEmail)) {
            error_log("Admin email not found or empty.");
            return;
        }

        // Send notification
        sendAdminNotification($adminEmail, $pendingPaymentsCount);

        // Update the previous count in the file
        file_put_contents($prevCountFile, $pendingPaymentsCount);
    } else if ($pendingPaymentsCount < $prevCount) {
        // Update previous count file if pending payments count decreases
        file_put_contents($prevCountFile, $pendingPaymentsCount);
    } else {
        // Log a message indicating there are no changes in pending payments count
        error_log("No changes in pending payments count.");
    }
}

// Call the function to check pending payments count and send notification
getPendingPaymentsCount($servername, $username, $password, $dbname);
?>
