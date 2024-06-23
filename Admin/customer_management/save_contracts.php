<?php
include '../../includes/config/dbconn.php';

date_default_timezone_set('Asia/Manila');

// Create a database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Function to send contract email
function sendContractEmail($email, $contractContent) {
    // Your email content and sending logic here
    $subject = 'Contract Details';

    // Constructing the HTML message
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
                            <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Contract Details</h1>
                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                Here are the contract details:
                            </p>
                            <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                $contractContent
                            </p>
                        </td>
                    </tr>
                    <tr>
                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                    To download your contract, simply click the button below:
                                </p>
                                <center>
                                    <a href="https://dormbell.online/Guest/confirmation.php" style="background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;">Download Contract</a>
                                </center>
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
                    text: 'Contract details sent successfully.'
                });
             </script>";
    } else {
        // Failed to send email
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Send Email',
                    text: 'Failed to send contract details. Please try again later.'
                });
             </script>";
    }
}

// Get the POST data
$tenantEmail = isset($_POST['tenant_email']) ? $_POST['tenant_email'] : '';
$contractContent = isset($_POST['contract_content']) ? $_POST['contract_content'] : '';
$Name = isset($_POST['Name']) ? $_POST['Name'] : '';

// Get the current date and time
$currentDate = date('Y-m-d H:i:s');

// Insert the contract into the contracts table along with the date and time
$stmt = $dbConnection->prepare("INSERT INTO contracts (EmailID, contract_content, tenant_name, contract_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $tenantEmail, $contractContent, $Name, $currentDate);
$stmt->execute();
$stmt->close();

// Call the function to send contract email
sendContractEmail($tenantEmail, $contractContent);

// Close the database connection
$dbConnection->close();
?>
