<?php
// Additional code for starting the session and including necessary files
session_start();
require_once('../config/dbconn.php');
require_once('../function/resetp_notif.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (!empty($newPassword) && !empty($confirmPassword) && ($newPassword === $confirmPassword)) {
        $matchedPassword = $newPassword; // Store the matched password in a variable

        // Hash the matched password securely with SHA-256
        $hashedPassword = hash('sha256', $matchedPassword);

        $email = isset($_SESSION['submitted_email']) ? $_SESSION['submitted_email'] : '';

        if (!empty($email)) {
            $stmtAdmin = mysqli_prepare($conn, "SELECT * FROM admins WHERE email = ?");
            mysqli_stmt_bind_param($stmtAdmin, "s", $email);
            mysqli_stmt_execute($stmtAdmin);
            $resultAdmin = mysqli_stmt_get_result($stmtAdmin);

            // For tenants, use the column name 'EmailID'
            $stmtTenant = mysqli_prepare($conn, "SELECT * FROM tenants WHERE EmailID = ?");
            mysqli_stmt_bind_param($stmtTenant, "s", $email);
            mysqli_stmt_execute($stmtTenant);
            $resultTenant = mysqli_stmt_get_result($stmtTenant);

            if (mysqli_num_rows($resultAdmin) == 1) {
                $userType = 'admin';
            } elseif (mysqli_num_rows($resultTenant) == 1) {
                $userType = 'tenant';
            } else {
                echo 'Error: Email not found in either admins or tenants table.';
                exit();
            }

            switch ($userType) {
                case 'admin':
                    $stmt = mysqli_prepare($conn, "UPDATE admins SET password = ? WHERE email = ?");
                    break;

                case 'tenant':
                    $stmt = mysqli_prepare($conn, "UPDATE tenants SET password = ? WHERE EmailID = ?");
                    break;

                default:
                    echo 'Error: Invalid user type.';
                    exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $email);

            if (mysqli_stmt_execute($stmt)) {
                if (sendPasswordResetNotification($email)) {
                    header('Location: ../../errorpage/resetSuccessful.php');
                    exit();
                } else {
                    echo 'Error sending password reset notification email.';
                }
            } else {
                echo 'Error updating password: ' . mysqli_error($conn);
            }

            mysqli_stmt_close($stmtAdmin);
            mysqli_stmt_close($stmtTenant);
            mysqli_stmt_close($stmt);
        } else {
            echo 'Error: Reset email not found in session. Email in session: ' . $email;
        }
    } else {
        echo 'Error: New password and confirm password do not match.';
    }
} else {
    header('Location: ../../Guest/otp.php');
    exit();
}
?>
