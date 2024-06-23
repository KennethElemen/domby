<?php
session_start(); // Start the session

// Check if email is submitted
if (isset($_POST['email'])) {
    // Get the submitted email
    $email = $_POST['email'];
    
    include '../includes/config/dbconn.php';

    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Prepare and execute SQL query
    $stmt = $dbConnection->prepare("SELECT status FROM reservations WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($reservationStatus);
    $stmt->fetch();

    // Check if the reservation status is set
    if ($stmt->num_rows > 0) {
        if ($reservationStatus === 'Accepted') {
            $_SESSION['submitted_email'] = $email; // Store the email in the session
header('Location: reservation-payment.php?email=' . urlencode($email));
exit();
        } else {
            echo "<script>alert('Reservation is Not Yet Accepted');</script>";
        }
    } else {
        echo "<script>alert('Email is Not Valid');</script>";
    }

    // Close statement and connection
    $stmt->close();
    $dbConnection->close();
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Email Validation</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="../assets/images/favicon.png"/>
</head>
<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="col-lg-5 col-md-8 col-sm-10 mx-auto text-left">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Email Validation</h1>
                        <span>Please type the email address you used to reserve the room.<br>We will validate your email before proceeding to the payment form. </span>
                        <hr>
                        <!-- Include necessary HTML and CSS -->
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="email" class="form-control" name="email" id="Email" placeholder="email" required>
                            </div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Search</button>
                            <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<script>
    document.getElementById('cancelButton').addEventListener('click', function () {
        window.location.href = '../index';
    });
</script>
<script src="../assets/vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="../assets/js/off-canvas.js"></script>
<script src="../assets/js/hoverable-collapse.js"></script>
<script src="../assets/js/misc.js"></script>
<!-- endinject -->
</body>
</html>
