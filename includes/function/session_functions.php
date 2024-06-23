<?php
function checkSession($conn) {
    session_start();

    if (isset($_SESSION['user_type'])) {
        $user_type = $_SESSION['user_type'];

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
    }

    return false; // Invalid session or user type
}
?>
