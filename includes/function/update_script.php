<?php
include '../../includes/config/dbconn.php';

function sendContractEmail($email, $contractContent) {
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
                  
                            <td style="padding: 0 35px;">
                                <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Contract Details</h1>
                                <hr>
                                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                    Congratulations on taking the next step towards your stay with us!
                                </p>
                                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                    Your contract details are ready for download. Please review them carefully.
                                </p>
                                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                    To download your contract, simply click the button below:
                                </p>
                                <center>
                                    <a href="https://dormbell.online/Guest/confirmation.php" style="background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;">Download Contract</a>
                                </center>
                            </td>
                        </tr>


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



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_submit'])) {
    $email = $_POST['email'];
    $typeOfStay = $_POST['type_of_stay'];
    $checkInDate = $_POST['update_check_in_date'];
    $checkOutDate = $_POST['update_check_out_date'];
    $newRate = $_POST['rate'];

    // Create a new database connection
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Update the tenant information in the tenantprofile table
$updateTenantProfileQuery = "UPDATE tenantprofile SET type_of_stay = ?, check_in_date = ?, check_out_date = ? WHERE email = ?";
$updateTenantProfileStatement = $dbConnection->prepare($updateTenantProfileQuery);
$updateTenantProfileStatement->bind_param("ssss", $typeOfStay, $checkInDate, $checkOutDate, $email);
$updateTenantProfileStatement->execute();

// Check for errors
if ($updateTenantProfileStatement->errno) {
    echo "Tenant Profile Update Error: " . $updateTenantProfileStatement->error;
}

// Update the tenant information in the reservations table
$updateReservationsQuery = "UPDATE reservations SET type_of_stay = ?, check_in_date = ?, check_out_date = ? WHERE email = ?";
$updateReservationsStatement = $dbConnection->prepare($updateReservationsQuery);
$updateReservationsStatement->bind_param("ssss", $typeOfStay, $checkInDate, $checkOutDate, $email);
$updateReservationsStatement->execute();

// Check for errors
if ($updateReservationsStatement->errno) {
    echo "Reservations Update Error: " . $updateReservationsStatement->error;
}


 // Send contract email
    sendContractEmail($email, $contractContent);
    
// Close the database connection
$dbConnection->close();

}

// Redirect back to the tenant list page after the update
header("Location: ../../Admin/customer_management/customer_management.php");
exit();
?>
