<?php 
include_once '../../includes/config/dbconn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's email from the session
function getEmailIDByTenantID3($conn) {
    // Initialize session if not started
  
    // Check if TenantID is set in the session
    if (isset($_SESSION['TenantID'])) {
        $tenantID = $_SESSION['TenantID'];

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT EmailID FROM tenants WHERE TenantID = ?");
        $stmt->bind_param("s", $tenantID);
        $stmt->execute();

        // Get the result
        $emailIDResult = $stmt->get_result()->fetch_assoc();

        // Close the statement
        $stmt->close();

        // Check if a result is found
        if ($emailIDResult) {
            // Return the EmailID if needed in the calling code
            return $emailIDResult['EmailID'];
        }
    }

    // If TenantID is not set in the session or no result is found, return null
    return null;
}

// Get the logged-in user's email
$loggedInEmail = getEmailIDByTenantID3($conn);

// Close the database connection
$conn->close();
?>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
           <li class="nav-item nav-profile">
                <a href="#" class="nav-link">
                    <div class="nav-profile-text d-flex flex-column">
                        <p class="mb-0 text-black text-small"><?php echo $loggedInEmail; ?></p>
                        <span class="text-secondary text-very-small">Tenant</span>
                    </div>
                    <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                </a>
            </li>
              <li class="nav-item">
                  <a class="nav-link" href="../dashboard/dashboard.php">
                      <span class="menu-title">Dashboard</span>
                      <i class="mdi mdi-home menu-icon"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="../profile/profile.php">
                      <span class="menu-title">Profile</span>
                      <i class="mdi mdi-account-multiple menu-icon"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="../payment/payment.php">
                      <span class="menu-title">Payment</span>
                      <i class="mdi mdi-wallet menu-icon"></i>
                  </a>
              </li>        
              <li class="nav-item">
                  <a class="nav-link" href="../account_sett/account_sett.php">
                      <span class="menu-title">Account Settings</span>
                      <i class="mdi mdi-settings menu-icon"></i>
                  </a>
              </li>
          </ul>
        </nav>