<?php
include '../../includes/config/dbconn.php';

// Create a new database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Get the logged-in user's email and name from the session
function getUserInfoByTenantID($dbConnection) {
    // Initialize session if not started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if TenantID is set in the session
    if (isset($_SESSION['TenantID'])) {
        $tenantID = $_SESSION['TenantID'];

        // Use prepared statement to prevent SQL injection
        $stmt = $dbConnection->prepare("SELECT EmailID, Name FROM tenants WHERE TenantID = ?");
        $stmt->bind_param("s", $tenantID);
        $stmt->execute();

        // Get the result
        $userInfo = $stmt->get_result()->fetch_assoc();

        // Close the statement
        $stmt->close();

        // Check if a result is found
        if ($userInfo) {
            // Return the user info
            return $userInfo;
        }
    }

    // If TenantID is not set in the session or no result is found, return null
    return null;
}

// Get the logged-in user's email and name
$userInfo = getUserInfoByTenantID($dbConnection);

// Extract user email and name
$userEmail = $userInfo['EmailID'] ?? null;
$userName = $userInfo['Name'] ?? null;

// Initialize $ratePerNight variable
$rate = "Set up your profile first";


// Fetch bills from the payment schedule table using email
if ($userEmail) {
    // Check the type_of_stay and balance
    $queryStayTypeAndBalance = "SELECT type_of_stay, balance, check_out_date FROM tenantprofile WHERE Email = ?";
    $stmtStayTypeAndBalance = $dbConnection->prepare($queryStayTypeAndBalance);

    if ($stmtStayTypeAndBalance) {
        $stmtStayTypeAndBalance->bind_param("s", $userEmail);
        $stmtStayTypeAndBalance->execute();
        $resultStayTypeAndBalance = $stmtStayTypeAndBalance->get_result();

        if ($resultStayTypeAndBalance && $resultStayTypeAndBalance->num_rows > 0) {
            $rowStayTypeAndBalance = $resultStayTypeAndBalance->fetch_assoc();
            $typeOfStay = $rowStayTypeAndBalance['type_of_stay'];
            $balance = $rowStayTypeAndBalance['balance'];

            // Based on type_of_stay, decide whether to fetch bills or display balance
            if ($typeOfStay === "Long-term") {
                $query = "SELECT Amount, MonthYear FROM paymentschedule WHERE EmailID = ? ORDER BY ABS(DATEDIFF(MonthYear, CURDATE())) LIMIT 1";
                $stmtBills = $dbConnection->prepare($query);

                if ($stmtBills) {
                    $stmtBills->bind_param("s", $userEmail);
                    $stmtBills->execute();
                    $resultBills = $stmtBills->get_result();

                    // Check if a result is found
                    if ($resultBills && $resultBills->num_rows > 0) {
                        $rowBill = $resultBills->fetch_assoc();
                        $amount = $rowBill['Amount'];
                        $monthYear = $rowBill['MonthYear'];
                        // Process bills for long-term stay
                        
                        // Echo the monthyear if the type of stay is long-term
                       // echo "MonthYear: $monthYear";
                    } else {
                        echo "No bills found.";
                    }

                    // Close the statement
                    $stmtBills->close();
                }
            } elseif ($typeOfStay === "Transient") {
                // If type_of_stay is transient, echo check_out_date
                //echo "Check out date: " . $rowStayTypeAndBalance['check_out_date'];
            } else {
                //echo "Invalid type of stay.";
            }
        } else {
            //echo "User not found.";
        }

        // Close the statement
        $stmtStayTypeAndBalance->close();
    }
}




// Fetch the check_in_date and check_out_date from the tenantprofile table
$queryDates = "SELECT check_in_date, check_out_date FROM tenantprofile WHERE email = ?";
$stmtDates = $dbConnection->prepare($queryDates);
if ($stmtDates) {
    $stmtDates->bind_param("s", $userEmail);
    $stmtDates->execute();
    $resultDates = $stmtDates->get_result();

    // Check if a result is found
    if ($resultDates && $resultDates->num_rows > 0) {
        $rowDates = $resultDates->fetch_assoc();

        // Convert check-in and check-out dates to DateTime objects
        $checkInDate = new DateTime($rowDates['check_in_date']);
        $checkOutDate = new DateTime($rowDates['check_out_date']);

        // Calculate remaining days
        $currentDate = new DateTime();
        $remainingDays = $currentDate->diff($checkOutDate)->days;

        // If it's past the check-out date, make remaining days negative
        if ($currentDate > $checkOutDate) {
            $remainingDays *= -1;
        }

        // Format remaining time for display
        $remainingTime = '';

        if ($remainingDays >= 365) {
            $years = floor($remainingDays / 365);
            $remainingDays %= 365;
            $remainingTime .= "$years year" . ($years > 1 ? 's' : '') . ' ';
        }

        if ($remainingDays >= 30) {
            $months = floor($remainingDays / 30);
            $remainingDays %= 30;
            $remainingTime .= "$months month" . ($months > 1 ? 's' : '') . ' ';
        }

        if ($remainingDays >= 7) {
            $weeks = floor($remainingDays / 7);
            $remainingDays %= 7;
            $remainingTime .= "$weeks week" . ($weeks > 1 ? 's' : '') . ' ';
        }

        if ($remainingDays > 0) {
            $remainingTime .= "$remainingDays day" . ($remainingDays > 1 ? 's' : '');
        }
         if ($currentDate->format('Y-m-d') == $checkOutDate->format('Y-m-d')) {
            $remainingDaysText = "<span style='color:red;'>TODAY</span>";
             $iconClass = "mdi mdi-calendar-clock text-danger icon-lg mr-3";
        } else {
            // Output the remaining time
            $remainingDaysText = $remainingTime;

            // Apply color based on remaining days
            if ($remainingDays < 6) {
                // Warning color
                $remainingDaysText = "<span style='color:orange;'>$remainingDaysText</span>";
                 $iconClass = "mdi mdi-calendar-clock text-warning icon-lg mr-3";
            } elseif ($remainingDays == 0) {
                // Red color for overdue
                $remainingDaysText = "<span style='color:red;'>$remainingDaysText</span>";
                 $iconClass = "mdi mdi-calendar-clock text-danger icon-lg mr-3";
            } elseif ($remainingDays >= 7) {
                // Warning color
                $remainingDaysText = "<span style='color:success;'>$remainingDaysText</span>";
                 $iconClass = "mdi mdi-calendar-clock text-success icon-lg mr-3";
            
            } else {
                // Success color
                $remainingDaysText = "<span style='color:success;'>$remainingDaysText</span>";
                 $iconClass = "mdi mdi-calendar-clock text-success icon-lg mr-3";
            }
        }


    } else {
        // Handle case when no result is found
        $remainingDaysText = "Set up your profile";
    }

    // Close the statement
    $stmtDates->close();
}


// Fetch only the latest announcement outside of the POST request
$result = $dbConnection->query("SELECT * FROM announcements ORDER BY announcement_id DESC LIMIT 1");
$announcementsHTML = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcementsHTML .= "<div class='col-lg-8 mb-4'>
        <div class='card shadow-lg'>
            <div class='card-body '>
                <h1 class='card-title mb-4' style='color: #1e1e2d; font-weight: 500; font-size: 32px;'>" . $row["announcement_title"] . "</h1>
                <p class='card-text text-muted mb-3' style='font-size: 15px;'>" . $row["announcer_name"] . "</p>
                <p class='card-text mb-4' style='font-size: 16px; color: #455056;'>" . $row["announcement_content"] . "</p>
            </div>
            <div class='card-footer bg-transparent border-top-0'>
                <small class='text-muted'>" . date('F j, Y', strtotime($row["publish_date"])) . "&nbsp; " . date('h:i A', strtotime($row["publish_time"])) . "</small>
            </div>
        </div>
    </div>";

    }
} else {
    $announcementsHTML = "<p class='no-announcement'>No announcements available.</p>";
}


// Close the database connection
$dbConnection->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>

<head>
    <style>
        /* Add your additional styles here */
        .custom-header {
            /* Your custom styles for the header */
            color: #007bff;
            /* Change the color to your preference */
            font-size: 24px;
            /* Change the font size to your preference */
            text-align: center;
        }

        /* Ensure equal height for card bodies */
        .card-body {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:../../partials/_navbar.html -->
        <?php include '../topbar.php'; ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:../../partials/_sidebar.html -->
            <?php include '../sidebar.php'; ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <?php
                        // Display welcome message with user's name
                        echo "<h1 class='mb-12'>Welcome, $userName!</h1>";
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class=" rounded card h-100 ">
                                <div class="card-body shadow-lg">
                                    <h4 class="card-title mb-3">Bills</h4>
                                    <h3 class="card-text mb-2">
                                        <i class="mdi mdi-calendar-clock icon-lg mr-2"></i>
                                        <span class="text-danger" style="font-weight: bold;">
                                                <?php
                                                    if ($typeOfStay === "long-term") {
                                                        echo "PHP $amount";
                                                    } else {
                                                        echo "PHP $balance";
                                                    }
                                                ?>
                                            </span>
                                    </h3>
                                    <div class="border-top pt-3">
                                       <p class=" mb-1"><strong>Your bill for:</strong>
                                            <?php
                                            if ($typeOfStay === "Long-term") {
                                                echo "$monthYear<br>";
                                            } elseif ($typeOfStay === "Transient") {
                                                echo "" . date('M d, Y', strtotime($rowStayTypeAndBalance['check_out_date']));
                                            } else {
                                                echo "Invalid type of stay.";
                                            }
                                            ?>
                                        </p>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                 <div class="col-lg-4 mb-4">
                            <div class="card border-0 shadow h-100">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">Remaining Stay</h4>
                                    <div class="d-flex align-items-center mb-4">
                                      <?php 
                                      // Output the icon
                                        echo "<i class='$iconClass'></i>";
                                      ?>
                                        <div>
                                            <h3 class="text-success mb-2 font-weight-bold"><?php
                                                if ($remainingDays >= 0) {
                                                echo $remainingDaysText;
                                                } else {
                                                echo "<span style='color:red;'>Overdue -" . abs($remainingDays) . " day(s)</span>";
                                                }
                                            ?></h3>
                                        </div>
                                    </div>
                                    <div class="border-top pt-3">
                                        <p class="text-muted mb-1"><strong>Actual Checkout Date:</strong></p>
                                        <?php if (isset($checkOutDate)): ?>
                                            <p class="mb-0"><?php echo $checkOutDate->format('F j, Y'); ?></p>
                                        <?php else: ?>
                                            <p class="mb-0">Set up your profile</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php echo $announcementsHTML; ?>
                </div>
                </div>
                <!-- partial:../../partials/_footer.html -->
                <?php include '../footer.php'; ?>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php include '../scripts.php'; ?>
</body>

</html>
