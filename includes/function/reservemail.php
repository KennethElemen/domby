<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust the path to autoload.php based on your directory structure
require __DIR__ . '/../phpmailer/src/Exception.php';
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';

function getAdminEmail() {
    require '../includes/config/dbconn.php';
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $admin_email = '';

    // SQL query to fetch email from admins table
    $sql = "SELECT Email FROM admins LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of the first row
        $row = $result->fetch_assoc();
        $admin_email = $row["Email"];
    }

    // Close connection
    $conn->close();

    return $admin_email;
}

// Get the admin email
$admin_email = getAdminEmail();

if (isset($_POST['Name'])) {
    $full_name = $_POST['Name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $type_of_stay = $_POST['type_of_stay'];
    $room_number = $_POST['room_number'];
    $duration = $_POST['duration'];

    // Construct the email body with the form data for the guest
    $email_body_guest = "
    <!doctype html>
    <html lang='en-US'>

    <head>
        <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
        <title>Pending Reservation</title>
    </head>
    <style>
        a:hover {text-decoration: underline !important;}
    </style>

    <body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
        <table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
            style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
            <tr>
                <td>
                    <table style='background-color: #f2f3f8; max-width:670px; margin:0 auto;' width='100%' border='0'
                        align='center' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td style='height:80px;'>&nbsp;</td>
                        </tr>
                        
                        <tr>
                            <td style='height:20px;'>&nbsp;</td>
                        </tr>
                        <!-- Email Content -->
                        <tr>
                            <td>
                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                                    style='max-width:670px; background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding:0 40px;'>
                                    <tr>
                                        <td style='height:40px;'>&nbsp;</td>
                                    </tr>
                                    <!-- Title -->
                                    <tr>
                                        <td style='padding:0 15px; text-align:center;'>
                                            <h1 style='color:#1e1e2d; font-weight:400; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Pending Reservation</h1>
                                            <span style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; 
                                            width:100px;'></span>
                                        </td>
                                    </tr>
                                    <tr>
                                    <p style='font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;'>
                                    Dear Guest,<br>Your reservation is currently pending. Please await confirmation from the landlord,
                                    who will contact you on the provided contact number with further information before accepting the request.<br><br>

                                    Thank you for your patience during this process. If you have any immediate concerns or queries, feel free to reach out to us.</p>
                                    </tr>

                                    <tr>
                                    <td style='font-size:15px; color:#455056; font-weight:bold;'><br><br>Reservation Details:
                                    </td>
                                    </tr>
                                    <!-- Details Table -->
                                    <tr>
                                        <td>
                                            <table cellpadding='0' cellspacing='0'
                                                style='width: 100%; border: 1px solid #ededed'>
                                                <tbody>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Full Name:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $full_name</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Email:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $email</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Contact Number:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $contact_number</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Check-in Date:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $check_in_date</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Check-out Date:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $check_out_date</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Type of Stay:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $type_of_stay</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Room Number:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056; '>
                                                            $room_number</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Duration of Stay:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056; '>
                                                            $duration</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                           Reschedule</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056; '>
                                                            <p><a href='https://dormbell.online/Guest/reschedule.php'>Click here</a></p></td>
                                                    </tr>
                                                     <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                           Cancel Reservation</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056; '>
                                                            <p><a href='https://dormbell.online/Guest/cancelation.php'>Click here</a></p></td>
                                                    </tr>
                                                </tbody>
                                                
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='height:40px;'>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style='height:20px;'>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

    </html>
    ";
       $email_body_sender = "
    <!doctype html>
    <html lang='en-US'>

    <head>
        <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
        <title>Reservation Confirmation Email Template</title>
    </head>
    <style>
        a:hover {text-decoration: underline !important;}
    </style>

    <body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
        <table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
            style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
            <tr>
                <td>
                    <table style='background-color: #f2f3f8; max-width:670px; margin:0 auto;' width='100%' border='0'
                        align='center' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td style='height:80px;'>&nbsp;</td>
                        </tr>
                        
                        <tr>
                            <td style='height:20px;'>&nbsp;</td>
                        </tr>
                        <!-- Email Content -->
                        <tr>
                            <td>
                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                                    style='max-width:670px; background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding:0 40px;'>
                                    <tr>
                                        <td style='height:40px;'>&nbsp;</td>
                                    </tr>
                                    <!-- Title -->
                                    <tr>
                                        <td style='padding:0 15px; text-align:center;'>
                                            <h1 style='color:#1e1e2d; font-weight:400; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Reservation Notification</h1>
                                            <span style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; 
                                            width:100px;'></span>
                                        </td>
                                    </tr>
                                    <!-- Details Table -->
                                    <tr>
                                        <td>
                                            <table cellpadding='0' cellspacing='0'
                                                style='width: 100%; border: 1px solid #ededed'>
                                                <tbody>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Full Name:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $full_name</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Email:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $email</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Contact Number:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $contact_number</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Check-in Date:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $check_in_date</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Check-out Date:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $check_out_date</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Type of Stay:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056;'>
                                                            $type_of_stay</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Room Number:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056; '>
                                                            $room_number</td>
                                                    </tr>
                                                     <tr>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:500; color:rgba(0,0,0,.64)'>
                                                            Duration of Stay:</td>
                                                        <td
                                                            style='padding: 10px; border-bottom: 1px solid #ededed; color: #455056; '>
                                                            $duration</td>
                                                    </tr>
                                                   <tr>
                                                        <td colspan='2' style='text-align: center;'>
                                                            <a href='https://dormbell.online/' style='background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;'>Login to Dormbell</a>
                                                        </td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='height:40px;'>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style='height:20px;'>&nbsp;</td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
        </table>
    </body>

    </html>
    ";

    try {
       // Create a new PHPMailer instance for the guest
$mail_guest = new PHPMailer(true);

// Set up SMTP for the guest
$mail_guest->isSMTP();
$mail_guest->Host = 'smtp.gmail.com';
$mail_guest->SMTPAuth = true;
$mail_guest->Username = 'belldorm21@gmail.com'; // Replace with your email
$mail_guest->Password = 'ydku xdny mubi sdoc'; // Replace with your email password
$mail_guest->SMTPSecure = 'tls'; // Use 'tls' instead of 'ssl'
$mail_guest->Port = 587; // Change the port to 587

// Set sender and recipient for the guest
$mail_guest->setFrom('belldorm21@gmail.com'); // Replace with your email
$mail_guest->addAddress($email);

// Set email subject and body for the guest
$mail_guest->isHTML(true);
$mail_guest->Subject = 'Pending Reservation';
$mail_guest->Body = $email_body_guest;

// Send the email to the guest
$mail_guest->send();

// Create a new PHPMailer instance for the admin
$mail_admin = new PHPMailer(true);

// Set up SMTP for the admin
$mail_admin->isSMTP();
$mail_admin->Host = 'smtp.gmail.com';
$mail_admin->SMTPAuth = true;
$mail_admin->Username = 'belldorm21@gmail.com'; // Replace with your email
$mail_admin->Password = 'ydku xdny mubi sdoc'; // Replace with your email password
$mail_admin->SMTPSecure = 'tls'; // Use 'tls' instead of 'ssl'
$mail_admin->Port = 587; // Change the port to 587

// Set sender and recipient for the admin
$mail_admin->setFrom('belldorm21@gmail.com'); // Replace with your email
$mail_admin->addAddress($admin_email); // Use the fetched admin email

// Set email subject and body for the admin
$mail_admin->isHTML(true);
$mail_admin->Subject = 'Reservation Notification';
$mail_admin->Body =  $email_body_sender;

// Send the email to the admin
$mail_admin->send();

        // Redirect to the desired page after successful form submission
        header("Location: ../../errorpage/Successful.php");
        exit;
    } catch (Exception $e) {
        // Log or display the error message
        error_log('Email error: ' . $e->getMessage(), 0);
         header("Location: ../../errorpage/unsuccessful.php");
    }
}
?>
