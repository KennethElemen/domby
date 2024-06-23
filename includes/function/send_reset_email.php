<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

require '../config/dbconn.php';
require '../../includes/config/mailer.php';

function sanitizeInput($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}

$email = sanitizeInput($conn, $_POST['email']);

$stmt_admin = mysqli_prepare($conn, "SELECT AdminID FROM admins WHERE Email = ?");
mysqli_stmt_bind_param($stmt_admin, "s", $email);
mysqli_stmt_execute($stmt_admin);
$result_admin = mysqli_stmt_get_result($stmt_admin);

$stmt_tenant = mysqli_prepare($conn, "SELECT TenantID FROM tenants WHERE EmailID = ?");
mysqli_stmt_bind_param($stmt_tenant, "s", $email);
mysqli_stmt_execute($stmt_tenant);
$result_tenant = mysqli_stmt_get_result($stmt_tenant);

if (mysqli_num_rows($result_admin) == 1 || mysqli_num_rows($result_tenant) == 1) {
    $otp = mt_rand(100000, 999999);
    $_SESSION['otp_timestamp'] = time() + 120;

    $recipient = $email;
    $subject = 'Password Reset OTP';

    $message = '
    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title>Password Reset OTP Email Template</title>
        <style type="text/css">
            a:hover {text-decoration: underline !important;}
        </style>
    </head>

    <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
        <!-- 100% body table -->
        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
            style="font-family: sans-serif;">
            <tr>
                <td>
                    <table style="background-color: #f2f3f8; max-width: 670px; margin: 0 auto;" width="100%" border="0"
                        align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="height: 80px;">&nbsp;</td>
                        </tr>
                       
                        <tr>
                            <td style="height: 20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                    style="max-width: 670px; background: #fff; border-radius: 3px; text-align: center; -webkit-box-shadow: 0 6px 18px 0 rgba(0,0,0,.06); -moz-box-shadow: 0 6px 18px 0 rgba(0,0,0,.06); box-shadow: 0 6px 18px 0 rgba(0,0,0,.06);">
                                    <tr>
                                        <td style="height: 40px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 35px;">
                                            <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px; font-family:  sans-serif;">Password Reset OTP</h1>
                                            <p style="font-size: 15px; color: #455056; margin: 8px 0 0; line-height: 24px;">
                                                Your OTP for password reset</p>
                                            <p style="font-size: 15px; color: #455056; margin-top: 24px; line-height: 24px;">
                                                If you did not request this OTP, please contact our support team immediately.
                                            </p>
                                            <p style="font-size: 15px; color: #455056; line-height: 24px;">
                                                Thank you for choosing our service.
                                            </p>
                                            <a href="javascript:void(0);"
                                                style="background: #20e277; text-decoration: none !important; font-weight: 500; margin-top: 35px; color: #fff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; display: inline-block; border-radius: 50px;">  <strong>' . $otp . '</strong></p></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height: 40px;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 20px;">&nbsp;</td>
                        </tr>
                     
                        <tr>
                            <td style="height: 80px;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--/100% body table-->
    </body>

    </html>';

    if (sendEmail($recipient, $subject, $message)) {
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_email'] = $email;

        header('Location: ../../Guest/otp.php');
        exit();
    } else {
        header("Location: ../errorpage/paymentunsuccessful.php");
        exit();
    }
} else {
    header("Location: ../../errorpage/errorMail.php");
    exit();
}
?>
