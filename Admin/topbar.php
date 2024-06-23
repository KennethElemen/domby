<?php

function signOut() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the landing page (index.php)
    header("Location: ../../index.php");
    exit();
}

// Check if the 'Signout' parameter is set in the URL
if (isset($_GET['Signout'])) {
    // Call the signOut function to perform the signout process
    signOut();
}
include_once '../../includes/config/dbconn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's email from the session
function getEmailIDByTenantID1($dbConnection) {
    // Initialize session if not started
  
    // Check if TenantID is set in the session
    if (isset($_SESSION['AdminID'])) {
        $tenantID = $_SESSION['AdminID'];

        // Use prepared statement to prevent SQL injection
        $stmt = $dbConnection->prepare("SELECT Email FROM admins WHERE AdminID = ?");
        $stmt->bind_param("s", $tenantID);
        $stmt->execute();

        // Get the result
        $emailIDResult = $stmt->get_result()->fetch_assoc();

        // Close the statement
        $stmt->close();

        // Check if a result is found
        if ($emailIDResult) {
            // Return the EmailID if needed in the calling code
            return $emailIDResult['Email'];
        }
    }

    // If TenantID is not set in the session or no result is found, return null
    return null;
}

// Get the logged-in user's email
$loggedInEmail = getEmailIDByTenantID1($conn);

// Close the database connection
$conn->close();
?>
<style>
    .max-height-120 {
    max-height: 150px; /* Set your desired max height */
    width: auto; /* Maintain aspect ratio */
    margin-top:20px;
}

</style>
<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row ">
     <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <!-- Replace "DormBell" text with your logo image -->
        <img src="../../assets/images/Logo12.svg" alt="DormBell" class="img-fluid max-height-120 mx-auto">
    </div>
    
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-text">
                        <p class="mb-1 text-black"><?php echo $loggedInEmail; ?></p>
                    </div>
                </a>
                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?Signout=1">
                        <i class="mdi mdi-logout mr-2 text-primary"></i> Signout
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>

