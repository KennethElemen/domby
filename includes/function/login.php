<?php
require_once('includes/config/dbconn.php');

session_start();

// Define constants for login attempt tracking
define('MAX_LOGIN_ATTEMPTS', 5); // Maximum allowed login attempts
define('LOGIN_ATTEMPT_TIMEOUT', 180); // Timeout in seconds (3 minutes)

function login($email, $password, $conn)
{
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Check if the user has exceeded the maximum login attempts
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $remainingTime = LOGIN_ATTEMPT_TIMEOUT - (time() - $_SESSION['last_login_attempt']);
        return "brute_force_protected|$remainingTime";
    }

    $adminQuery = "SELECT * FROM admins WHERE email=? AND password=?";
    $adminStmt = mysqli_prepare($conn, $adminQuery);
    mysqli_stmt_bind_param($adminStmt, "ss", $email, $password);
    mysqli_stmt_execute($adminStmt);
    $adminResult = mysqli_stmt_get_result($adminStmt);

    $tenantQuery = "SELECT * FROM tenants WHERE EmailID=? AND password=?";
    $tenantStmt = mysqli_prepare($conn, $tenantQuery);
    mysqli_stmt_bind_param($tenantStmt, "ss", $email, $password);
    mysqli_stmt_execute($tenantStmt);
    $tenantResult = mysqli_stmt_get_result($tenantStmt);

    if (mysqli_num_rows($adminResult) > 0) {
        $_SESSION['login_attempts'] = 0; // Reset login attempts on successful login
        $adminData = mysqli_fetch_assoc($adminResult);
        $_SESSION['AdminID'] = $adminData['AdminID'];
        $_SESSION['user_type'] = 'admin';
        return "admin";
    } elseif (mysqli_num_rows($tenantResult) > 0) {
        $_SESSION['login_attempts'] = 0; // Reset login attempts on successful login
        $tenantData = mysqli_fetch_assoc($tenantResult);
        $_SESSION['TenantID'] = $tenantData['TenantID'];
        $_SESSION['user_type'] = 'tenant';
        return "tenant";
    } else {
        // Increment login attempts only if not already set
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 1;
        }

        // Set last login attempt time only if not already set
        if (!isset($_SESSION['last_login_attempt'])) {
            $_SESSION['last_login_attempt'] = time();
        }

        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    $loginResult = login($email, $password, $conn);

    if (strpos($loginResult, 'brute_force_protected') === 0) {
        list(, $remainingTime) = explode('|', $loginResult);
        echo 'alert("Brute force protection activated. Please try again after ' . $remainingTime . ' seconds.");';
    } elseif ($loginResult === "admin") {
        session_regenerate_id(true);
        header("Location: Admin/dashboard/dashboard.php");
        exit();
    } elseif ($loginResult === "tenant") {
        session_regenerate_id(true);
        header("Location: tenants/dashboard/dashboard.php");
        exit();
    } else {
        // Incorrect credentials, check if brute force protection is activated
        list(, $remainingTime) = explode('|', $loginResult);
        $remainingTime = max(0, $remainingTime); // Ensure the remaining time is not negative

        if ($remainingTime > 0) {
            // Brute force protection is active
            header("Location: errorpage/brute.php");
            exit();
        } else {
            // Redirect to loginfailed page
            header("Location: errorpage/loginfailed.php");
            exit();
        }
    }
}
?>
