

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OTP</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="../assets/images/favicon.png" />
</head>
<body>
    <?php
session_start(); // Start the session

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted OTP
    $submitted_otp = $_POST['otp'];

    // Get the OTP stored in the session
    $stored_otp = $_SESSION['otp'] ?? '';

    // Check if OTP matches
    if ($submitted_otp == $stored_otp) {
        // OTP is correct, redirect to cancel.php
        header('Location: resetpassword.php');
        exit();
    } else {
        // OTP is incorrect, display error message using Swal
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect OTP',
                    text: 'Please try again.'
                });
             </script>";
    }
}
?>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="col-lg-5 col-md-8 col-sm-10 mx-auto text-left">
                    <div class="card">
                        <div class="card-body">
                              <h1 class="mb-12">OTP Verification<ul class="list-arrow">
                                              <li>Enter the OTP code from the email we sent to you</li>
                                              <li>This code is valid for <strong id="timer"></strong></li>
                                            </ul></h1>
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="token">Enter OTP</label>
                                    <input type="number" class="form-control" name="otp" id="token" placeholder="OTP" required>
                                </div>
                                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
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
        document.getElementById('cancelButton').addEventListener('click', function() {
            window.location.href = '../index.php';
        });

        // Countdown timer for 2 minutes
        const twoMinutes = 2 * 60;
        let timeLeft = twoMinutes;

        const countdownTimer = setInterval(function() {
            const minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;

            seconds = seconds < 10 ? '0' + seconds : seconds;

            // Display the countdown timer
            document.getElementById('timer').textContent = `${minutes}:${seconds}`;

            // Check if time is up
            if (timeLeft === 0) {
                clearInterval(countdownTimer);
                // Redirect back to cancelation.php using SweetAlert
                Swal.fire({
                    icon: 'info',
                    title: 'Time Expired',
                    text: 'Your OTP session has expired. Redirecting back to cancellation page.',
                    timer: 3000, // 3 seconds
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.href = 'cancelation.php';
                    }
                });
            } else {
                timeLeft--;
            }
        }, 1000);
    </script>
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
