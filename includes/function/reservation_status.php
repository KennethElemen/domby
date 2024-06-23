<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phpmailer/src/Exception.php';
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';

function processReservations() {
    include '../../includes/config/dbconn.php';

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $reservedIDsFile = __DIR__ . '/reserved_ids.txt';

    // Check if the file exists
    if (!file_exists($reservedIDsFile)) {
        touch($reservedIDsFile);
    }

    $reservedIDs = file($reservedIDsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Load sent email statuses
    $sentEmailStatusFile = __DIR__ . '/sent_email_status.json';
    $sentEmailStatus = file_exists($sentEmailStatusFile) ? json_decode(file_get_contents($sentEmailStatusFile), true) : [];

    $sql = "SELECT * FROM reservations";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reservation_id = $row['ReservationID'];
            $status = $row['status'];
            $email = $row['email'];

            if ($status == 'Accepted' && !in_array($reservation_id, $reservedIDs) && !isset($sentEmailStatus[$reservation_id])) {
                sendAcceptedEmail($email, $reservation_id);
                // Add the reservation ID to the list of sent emails
                file_put_contents($reservedIDsFile, $reservation_id . PHP_EOL, FILE_APPEND);
                // Mark email as sent
                $sentEmailStatus[$reservation_id] = true;
            } elseif ($status == 'Rejected' && !isset($sentEmailStatus[$reservation_id])) {
                sendRejectedEmail($email);
                // Mark email as sent
                $sentEmailStatus[$reservation_id] = true;
                // Delete the rejected reservation
                deleteReservation($reservation_id, $conn);
            } elseif (empty($status)) {
                $room_number = $row['room_number'];
                $max_occupants = getMaxOccupants($room_number);

                if ($max_occupants !== null) {
                    $occupants_count = getOccupantsCount($room_number, $conn);

                    if ($occupants_count >= $max_occupants) {
                        sendRoomFullyBookedEmail($email);
                        // Delete the reservation
                        deleteReservation($reservation_id, $conn);
                    }
                }
            }
        }
    }

    // Save sent email statuses
    file_put_contents($sentEmailStatusFile, json_encode($sentEmailStatus));

    $conn->close();
}

function sendAcceptedEmail($recipientEmail, $reservationId) {
    $subject = 'Reservation Accepted';
    $message = '
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
            <title>Reservation Accepted</title>
            <style type="text/css">
                a:hover {text-decoration: underline !important;}
            </style>
        </head>
        <body style="font-family: \'Open Sans\', sans-serif; background-color: #f2f3f8; padding: 20px; border-radius: 10px; max-width: 670px; margin: 0 auto; text-align: center; -webkit-box-shadow: 0 6px 18px 0 rgba(0,0,0,.06); -moz-box-shadow: 0 6px 18px 0 rgba(0,0,0,.06); box-shadow: 0 6px 18px 0 rgba(0,0,0,.06);">
            <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8">
                <tr>
                    <td>
                        <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="height:80px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="height:20px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                        <tr>
                                            <td style="height:40px;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:0 35px;">
                                                <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:\'Rubik\',sans-serif;">Dear Guest,</h1>
                                                
                                                <p style="color:#1e1e2d; font-size:16px;">Great news! Your reservation has been accepted. Click the link below to validate your email in order to send proof of payment:</p>
                                                
                                                <p style="font-size: 16px; color: #20e277;"><a href="https://dormbell.online/Guest/emailvalidation.php" style="text-decoration: none; color: #20e277; font-weight: bold;">Send Proof of Payment Here</a></p>
        
                                                <p style="color:#1e1e2d; font-size:16px;">If you have any questions or need further assistance, feel free to contact us.</p>
        
                                                <p style="color:#1e1e2d; font-size:16px;">Thank you for choosing DormBell!</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="height:40px;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            <tr>
                                <td style="height:20px;">&nbsp;</td>
                            </tr>
                           
                            <tr>
                                <td style="height:80px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

    sendEmail($recipientEmail, $subject, $message);
}

function sendRejectedEmail($recipientEmail) {
    $subject = 'Reservation Rejected';
    $message = '
        <div style="font-family: Arial, sans-serif; background-color: #ffdddd; padding: 20px; border-radius: 10px; max-width: 400px; margin: 0 auto;">
            <p style="font-size: 16px; color: #cc0000;">Dear Guest,</p>
            
            <p style="font-size: 16px; color: #cc0000;">We regret to inform you that your reservation has been rejected for the following reasons:</p>
        
            <ul style="font-size: 16px; color: #cc0000;">
                <li>Non-responsive contact information provided.</li>
            </ul>
        
            <p style="font-size: 16px; color: #cc0000;">We apologize for any inconvenience this may have caused and appreciate your understanding.</p>
        
            <p style="font-size: 16px; color: #cc0000;">Thank you for considering DormBell.</p>
        </div>';

    sendEmail($recipientEmail, $subject, $message);
}

function enqueueEmailTask($recipientEmail, $subject, $message) {
    $task = json_encode(['recipientEmail' => $recipientEmail, 'subject' => $subject, 'message' => $message]);
    file_put_contents(__DIR__ . '/email_queue.json', $task . PHP_EOL, FILE_APPEND);
}

// Function to dequeue and send email tasks
function processEmailQueue() {
    $queueFile = __DIR__ . '/email_queue.json';
    if (!file_exists($queueFile)) {
        return; // Queue file doesn't exist
    }

    $tasks = file($queueFile, FILE_IGNORE_NEW_LINES);
    if (empty($tasks)) {
        return; // Queue is empty
    }

    // Clear the queue file
    file_put_contents($queueFile, '');

    foreach ($tasks as $task) {
        $taskData = json_decode($task, true);
        sendEmail($taskData['recipientEmail'], $taskData['subject'], $taskData['message']);
    }
}

function sendRoomFullyBookedEmail($recipientEmail) {
    $subject = 'Room Fully Booked';
    $message = '<div style="font-family: Arial, sans-serif; font-size: 16px; color: #333; background-color: #f5f5f5; padding: 20px; border-radius: 10px;">
                    <p>Dear Guest,</p>
                    <p>We regret to inform you that the room you had previously booked is no longer available as it has been fully booked by other guests. We apologize for any inconvenience this may cause you. However, we understand your need for accommodation and would like to offer you alternative options.</p>
                    <p>Please click <a href="https://dormbell.online/" style="color: #007bff; text-decoration: none;">here</a> to explore other available rooms and make a new booking. We assure you that our other accommodations maintain the same standards of comfort and quality that you expect.</p>
                    <p>Once again, we apologize for any inconvenience and thank you for your understanding.</p>
                    <br>
                    <p>Best regards,</p>
                    <p>DormBell</p>
                </div>';

    // Enqueue email task
    enqueueEmailTask($recipientEmail, $subject, $message);
}

function getMaxOccupants($room_number) {
    include '../../includes/config/dbconn.php';

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT max_occupants FROM room_management WHERE room_number = '$room_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['max_occupants'];
    } else {
        return null; // Room number not found or max occupants not specified
    }

    $conn->close();
}

function getOccupantsCount($room_number, $conn) {
    $sql = "SELECT COUNT(*) AS count FROM reservations WHERE room_number = '$room_number' AND status = 'Accepted'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0; // No occupants found for the room
    }
}

function deleteReservation($reservation_id, $conn) {
    $sql = "DELETE FROM reservations WHERE ReservationID = $reservation_id";
    if ($conn->query($sql) === TRUE) {
        // Reservation deleted successfully
    } else {
        echo "Error deleting reservation: " . $conn->error;
    }
}

function sendEmail($recipient, $subject, $message) {
    $mail = new PHPMailer;

    // Set the SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'belldorm21@gmail.com';
    $mail->Password = 'ydku xdny mubi sdoc';
    $mail->SMTPSecure = 'tls'; // Change to 'ssl' if needed
    $mail->Port = 587;

    // Set the sender and recipient email addresses
    $mail->setFrom('belldorm21@gmail.com');
    $mail->addAddress($recipient);

    // Set email content
    $mail->Subject = $subject;
    $mail->msgHTML($message);

    // Attempt to send the email
    if (!$mail->send()) {
        // Log error
        error_log("Email could not be sent to $recipient: " . $mail->ErrorInfo);
    }
}

// Call the function to process reservations
processReservations();

// Process email queue
processEmailQueue();
?>
