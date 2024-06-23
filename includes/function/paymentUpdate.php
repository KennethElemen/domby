<?php
require '../includes/config/mailer.php';
// Now you can call the sendEmail function

sendEmail('recipient@example.com', 'Test Subject', 'Test Message');

function processPayments() {
    // File path to store sent emails status
    $sentEmailsStatusFile = __DIR__ . '/sent_emails_status.json';

    // Load sent emails status from file
    $sentEmailsStatus = file_exists($sentEmailsStatusFile)
        ? json_decode(file_get_contents($sentEmailsStatusFile), true)
        : [];

    // Database connection parameters

    
    require '../includes/config/dbconn.php';

    // Log received data
    
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

    // Check the connection
   

    $sql = "SELECT * FROM payment";
    $result = $conn->query($sql);

    

    // Save sent emails status to file
    file_put_contents($sentEmailsStatusFile, json_encode($sentEmailsStatus));

    $conn->close();
}




function sendPendingPaymentEmail($recipientEmail, $paymentId) {
    $subject = 'Pending Payment';
   $message = '
        <div style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; border-radius: 10px; max-width: 400px; margin: 0 auto;">
            <p style="font-size: 16px; color: #333;">Dear Tenant,</p>
            
            <p style="font-size: 16px; color: #333;">We want to inform you that your payment is currently pending. Please be patient while we wait for the Landlord to confirm the payment.</p>

           

            <p style="font-size: 16px; color: #333;">Thank you for choosing DormBell!</p>
        </div>';

    sendEmail($recipientEmail, $subject, $message);
}




// Call the function to process payments
processPayments();
?>