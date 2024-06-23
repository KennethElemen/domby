<?php
include '../../includes/config/dbconn.php';

// Function to send removal email
function sendRemovalEmail($email, $reason) {
    // Your email content and sending logic here
    $subject = 'Account Removal Notification';

    // Constructing the HTML message
    $message = <<<HTML
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Account Removal Notification">
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
                            <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Account Removal Notification</h1>
                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                               Your account has been removed due to non-payment, specifically, failure to pay rent on time.
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

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: DormBell' . "\r\n" .
        'Reply-To: ' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    // Send email
    if (mail($email, $subject, $message, $headers)) {
        // Email sent successfully
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent',
                    text: 'Removal notification sent successfully.'
                });
             </script>";
    } else {
        // Failed to send email
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Send Email',
                    text: 'Failed to send removal notification. Please try again later.'
                });
             </script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_tenant_submit'])) {
    $tenantEmail = $_POST['tenant_email'];

    // Check if a reason is selected
    if (isset($_POST['removal_reason']) && !empty($_POST['removal_reason'])) {
        $reason = $_POST['removal_reason'];

        // Create a database connection
        $dbConnection = new mysqli($servername, $username, $password, $dbname);

        // Check the connection
        if ($dbConnection->connect_error) {
            die("Connection failed: " . $dbConnection->connect_error);
        }

        // Remove record from tenantprofile table
        $query1 = "DELETE FROM tenantprofile WHERE email = '$tenantEmail'";
        mysqli_query($dbConnection, $query1);

        // Remove record from tenants table
        $query2 = "DELETE FROM tenants WHERE EmailID = '$tenantEmail'";
        mysqli_query($dbConnection, $query2);

        // Remove record from reservations table
        $query3 = "DELETE FROM reservations WHERE email = '$tenantEmail'";
        mysqli_query($dbConnection, $query3);

        // Send removal notification email
        sendRemovalEmail($tenantEmail, $reason);

        $dbConnection->close();
    } else {
        // Show SweetAlert to select a reason
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Reason Required',
                    text: 'Please select a reason for account removal.'
                });
             </script>";
    }
}
?>
