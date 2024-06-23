<?php
session_start();

require '../config/dbconn.php';

function sanitizeInput($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOTP = sanitizeInput($conn, $_POST['otp']);
    $expectedOTP = $_SESSION['reset_otp'];
    $timestamp = $_SESSION['otp_timestamp'];

    $currentTimestamp = time();
    $timeDifference = $timestamp - $currentTimestamp;

    if ($enteredOTP == $expectedOTP && $timeDifference > 0 && $timeDifference <= 120) {
        // OTP is correct and within the valid time window
        // Proceed with further actions or redirect as needed
        // For example, redirect to the password reset page
        header('Location: ../../Guest/resetpassword.php');
        exit();
    } else {
        // Incorrect OTP or expired
        // Redirect to an error page or display an error message
        header('Location: ../../errorpage/error-404.php');
        exit();
    }
} else {
    // If the request method is not POST, redirect to an error page
    header('Location: ../../errorpage/error-404.php');
    exit();
}
?>
