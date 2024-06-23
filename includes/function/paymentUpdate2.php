<?php
require '../../includes/config/mailer.php';
// Now you can call the sendEmail function

sendEmail('recipient@example.com', 'Test Subject', 'Test Message');

function processPayments() {
    $sentEmailsStatusFile = __DIR__ . '/sent_emails_status.json';
    $sentEmailsStatus = file_exists($sentEmailsStatusFile) ? json_decode(file_get_contents($sentEmailsStatusFile), true) : [];

    require '../../includes/config/dbconn.php';
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM payment";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $paymentId = $row['PaymentID'];
            $paymentStatus = $row['Status'];
            $email = $row['EmailID'];

            if (!empty($email) && !isset($sentEmailsStatus[$paymentStatus][$paymentId])) {
                switch ($paymentStatus) {
                    case 'Completed':
                        $reservationStatus = checkReservationStatus($conn, $email);
                        sendCompletedPaymentEmail($email, $paymentId, $row['PaymentType']);
                        if (isset($reservationStatus) && $paymentStatus == 'Completed') {
                            moveToTenantsTable($conn, $email, $row['Name']);
                        }
                        break;
                   case 'Rejected':
                        $reason = $row['Reason']; // Assuming you have the reason stored in your database
                        echo "Reason for rejection: $reason<br>"; // Echo the reason
                        sendRejectedPaymentEmail($email, $paymentId, $reason);
                        break;

                }
                $sentEmailsStatus[$paymentStatus][$paymentId] = true;
            }
        }
    }

    file_put_contents($sentEmailsStatusFile, json_encode($sentEmailsStatus));
    $conn->close();
}

function checkReservationStatus($conn, $email) {
    $stmt = $conn->prepare("SELECT status FROM reservations WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();
    return $status;
}

function moveToTenantsTable($conn, $email, $name) {
    $defaultPassword = generateDefaultPassword();

    if (!checkEmailExists($conn, $email)) {
        $stmt = $conn->prepare("INSERT INTO tenants (EmailID, Name, Password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $name, $defaultPassword);

        if ($stmt->execute()) {
            // Handle success if needed
        } else {
            // Handle error if needed
        }

        $stmt->close();
    } else {
        // Handle case where email already exists
    }
}

// Function to check if the email already exists in the tenants table
function checkEmailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tenants WHERE EmailID = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0;
}



function getPaymentDetails($paymentId) {
    // Database connection parameters
    include '../../includes/config/dbconn.php';

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to retrieve payment details
    $sql = "SELECT * FROM payment WHERE PaymentID = $paymentId";
    $result = $conn->query($sql);

    // Fetch payment details
    $paymentDetails = $result->fetch_assoc();

    $conn->close();
    
    return $paymentDetails;
}

function sendCompletedPaymentEmail($recipientEmail, $paymentId, $paymentType) {
    $defaultPassword = generateDefaultPassword();

    // Get payment details from the database based on $paymentId
    $paymentDetails = getPaymentDetails($paymentId);

    $subject = 'Payment Completed';
    $message = ''; // Initialize $message variable

    if ($paymentType == 'Down Payment') {
      $message = ' <!DOCTYPE html>
        <html lang="en-US">
        
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="New Account Email Template.">
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
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Welcome to Dormbell!</h1>
                                     <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                                Your account has been created. Below are your temporaray login credentials. <br><strong>Please change
                                                    the password immediately after login</strong>.</p>
                                            <span
                                                style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                            <center>
                                    <p style="color: #455056; font-size: 18px; line-height: 20px; margin: 0; font-weight: 500;">
                                        <strong style="font-size: 13px; color: #888888;">Email:</strong> <p class="itemtext">' . $paymentDetails['EmailID'] . '</p>
                                        <br>
                                        <strong style="font-size: 13px; color: #888888;">P@ssword:</strong> tenants123
                                    </p>
                                        
                                    </center>
                                    <center>
                                     <a href="https://dormbell.online/" style="background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;">Login to Dormbell</a>
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
        
        </html>';
    // Send rejection email
    sendEmail($recipientEmail, $subject, $message);

   
}elseif ($paymentType == 'Monthly') {
        $message = '
           <div id="invoice-POS">
                <center id="top">
                    <div class="logo"></div>
                    <div class="info"> 
                        <h2>DormBell</h2>
                    </div><!--End Info-->
                    <p>Thank you for choosing DormBell as your second home. Your trust in us is deeply appreciated, and were thrilled to continue providing you with exceptional service.</p>
                </center><!--End InvoiceTop-->

                
                </div><!--End Invoice Mid-->

                <div id="bot">
                    <div id="table">
                        <table>
                            <tr class="tabletitle">
                                <td class="item"><h2>Payment Details</h2></td>
                                <td class="Hours"></td>
                            </tr>

                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Reference ID</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['PaymentID'] . '</p></td>
                            </tr>

                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Name</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['Name'] . '</p></td>
                            </tr>
                            
                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">ROOM No.</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['RoomNumber'] . '</p></td>
                            </tr>

                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Amount</p></td>
                                <td class="tableitem"><p class="itemtext">PHP' .' '. $paymentDetails['Amount'] . '</p></td>
                            </tr>

                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Payment Type</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['PaymentType'] . '</p></td>
                            </tr>
                            
                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Mode of Payment</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['PaymentMethod'] . '</p></td>
                            </tr>

                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Date</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['Date'] . '</p></td>
                            </tr>

                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Month Paid</p></td>
                                <td class="tableitem"><p class="itemtext">' . $paymentDetails['Month'] . '</p></td>
                            </tr>
                            <tr class="service">
                                <td class="tableitem"><p class="itemtext">Status</p></td>
                                <td class="tableitem"><p class="itemtext">Completed</p></td>
                            </tr>
                        </table>
                    </div><!--End Table-->
                </div><!--End InvoiceBot-->
            </div><!--End Invoice-->
        ';
    }

    sendEmail($recipientEmail, $subject, $message);
}


function sendRejectedPaymentEmail($recipientEmail, $paymentId, $reason) {
    $subject = 'Payment Rejected';
    $message = '
    <div style="font-family: Arial, sans-serif; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px; max-width: 400px; margin: 0 auto;">
        <p style="font-size: 16px; color: #721c24; margin-bottom: 10px;">Dear Customer,</p>
        
        <p style="font-size: 16px; color: #721c24;">We regret to inform you that your payment has been rejected due to the following reason:</p>
        
        <p style="font-size: 16px; color: #721c24; background-color: #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 10px;">' . $reason . '</p>

        <p style="font-size: 16px; color: #721c24;">We apologize for any inconvenience this may have caused and appreciate your prompt attention to this issue.</p>

        <p style="font-size: 16px; color: #721c24;">Thank you for your understanding.</p>
    </div>';

    // Send rejection email
    sendEmail($recipientEmail, $subject, $message);

   
}



function removePaymentFromDatabase($paymentId) {
    // Database connection parameters
      include '../../includes/config/dbconn.php';
      
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete the record from the payment table
    $deleteSql = "DELETE FROM payment WHERE PaymentID = $paymentId";

    if ($conn->query($deleteSql)) {
        echo "Payment record removed successfully!<br>";
    } else {
        echo "Error removing payment record: " . $conn->error . "<br>";
    }

    $conn->close();
}


function generateDefaultPassword() {
    $password = 'tenants123'; // Default password
    return hash('sha256', $password);
}



// Call the function to process payments
processPayments();
?>