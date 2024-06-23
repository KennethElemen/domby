<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

function checkSession($conn, $allowedUserTypes = [], $requireOTP = false) {
    if (!isset($_SESSION['user_type'])) {
        // Redirect to index page if no valid user is logged in
        header("Location: ../../errorpage/error-403.php");
        exit();
    }

    $user_type = $_SESSION['user_type'];

    if (!in_array($user_type, $allowedUserTypes)) {
        // Redirect to index page if the user type is not allowed for this file
        header("Location: ../../errorpage/error-403.php");
        exit();
    }

    // Check for OTP-related session variables if required
    if ($requireOTP) {
        if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email'])) {
            // Redirect to index page if OTP-related session variables are missing
            header("Location: ../../errorpage/error-404.php");
            exit();
        }
    }

    switch ($user_type) {
        case 'admin':
            if (isset($_SESSION['AdminID'])) {
                $adminID = $_SESSION['AdminID'];
                $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE AdminID = ?");
                mysqli_stmt_bind_param($stmt, "i", $adminID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    return true; // Admin session is valid
                }
            }
            break;

        case 'tenant':
            if (isset($_SESSION['TenantID'])) {
                $tenantID = $_SESSION['TenantID'];
                $stmt = mysqli_prepare($conn, "SELECT * FROM tenants WHERE TenantID = ?");
                mysqli_stmt_bind_param($stmt, "i", $tenantID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    return true; // Tenant session is valid
                }
            }
            break;
    }

    // Redirect to index page for any unexpected scenarios
    header("Location: ../../errorpage/error-404.php");
    exit();
}
?>
