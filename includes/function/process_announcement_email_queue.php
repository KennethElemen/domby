<?php

$servername = "localhost";
$username = "u533384272_Dormbell";
$password = "D[1PUy4lO@";
$dbname = "u533384272_Dormbell";

$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    error_log("Connection failed: " . $dbConnection->connect_error);
    die();
}

// Load PHPMailer library and other dependencies
include '../config/mailer.php';

// Function to dequeue and send announcement emails
function processAnnouncementEmailQueue() {
    $queueFile = __DIR__ . '/announcement_email_queue.json';
    if (!file_exists($queueFile)) {
        error_log('Announcement queue file does not exist.');
        return; // Queue file doesn't exist
    }

    $tasks = file($queueFile, FILE_IGNORE_NEW_LINES);
    if (empty($tasks)) {
        error_log('Announcement queue is empty.');
        return; // Queue is empty
    }

    // Clear the queue file
    if (!file_put_contents($queueFile, '')) {
        error_log('Failed to clear announcement queue file.');
        return; // Error occurred while clearing the queue file
    }

    foreach ($tasks as $task) {
        $taskData = json_decode($task, true);
        if ($taskData === null) {
            error_log('Failed to decode JSON data in announcement queue.');
            continue;
        }
        // Send email to all tenants with announcement content
        sendAnnouncementEmails($taskData['title'], $taskData['content']);
    }
}

// Function to send announcement emails to all tenants
function sendAnnouncementEmails($title, $content) {
    global $dbConnection; // Access the global database connection variable

    // Email message content
    $emailSubject = "New Announcement";
    $emailBody = <<<HTML
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="New Announcement">
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
                  
                    <td style="padding: 0 35px;">
                        <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">$title</h1>
                        <hr>
                        <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                             Dear Tenant, 
                        </p>
                        <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                            A new announcement has been added:
                        </p>
                        <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                            <strong>Title:</strong> $title
                        </p>
                        <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                            <strong>Content:</strong> $content
                        </p>
                        <p>Please check it out.</p>
                        <p>Thank you.</p>
                        <center>
                            <a href="https://dormbell.online/Guest/login.php" style="background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;">Log in</a>
                        </center>
                    </td>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

    // Get all tenant emails
    $tenantEmailsQuery = $dbConnection->query("SELECT EmailID FROM tenants");
    if ($tenantEmailsQuery->num_rows > 0) {
        while ($row = $tenantEmailsQuery->fetch_assoc()) {
            // Send email to each tenant using sendEmail function
            sendEmail($row["EmailID"], $emailSubject, $emailBody);
        }
    }
}

// Call the function to process the email queue
processAnnouncementEmailQueue();
?>
