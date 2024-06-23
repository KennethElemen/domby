

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password</title>
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

// Function to generate OTP
function generateOTP() {
    // Generate a random 6-digit OTP
    $otp = rand(100000, 999999);
    return $otp;
}

// Function to send OTP to email and store it in session
function sendOTP($email) {
    include '../includes/config/mailer.php'; // Include mailer configuration
    $otp = generateOTP(); // Generate OTP

    // Send OTP to email using PHPMailer
    $subject = 'Your OTP for Reset Password';
    $message = '
    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
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
                                            <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px; font-family:  sans-serif;">Reset Password</h1>
                                            <p style="font-size: 15px; color: #455056; margin: 8px 0 0; line-height: 24px;">
                                                this OTP is valid for 2 minutes</p>
                                           
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
    
    // Send email using PHPMailer
    if (sendEmail($email, $subject, $message)) {
        // Store OTP and its expiration time in session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + (2 * 60); // OTP valid for 2 minutes (2 * 60 seconds)
        return true; // Email sent successfully
    } else {
        return false; // Failed to send email
    }
}

// Function to sanitize input
function sanitizeInput($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Check if email is submitted
if (isset($_POST['email'])) {
    // Get the submitted email and sanitize it
    include '../includes/config/dbconn.php'; // Include database connection
    $email = sanitizeInput($conn, $_POST['email']);
    $emailID = $email; // Assign $email to $emailID initially

    // Prepare and execute SQL query for admin
    $stmt_admin = mysqli_prepare($conn, "SELECT AdminID FROM admins WHERE Email = ?");
    mysqli_stmt_bind_param($stmt_admin, "s", $email);
    mysqli_stmt_execute($stmt_admin);
    $result_admin = mysqli_stmt_get_result($stmt_admin);

    // Prepare and execute SQL query for tenant
    $stmt_tenant = mysqli_prepare($conn, "SELECT TenantID FROM tenants WHERE EmailID = ?");
    mysqli_stmt_bind_param($stmt_tenant, "s", $email);
    mysqli_stmt_execute($stmt_tenant);
    $result_tenant = mysqli_stmt_get_result($stmt_tenant);

    // Check if the reservation status is set
    if ($result_admin->num_rows > 0) {
        // If email belongs to admin
        $_SESSION['submitted_email'] = $email;
    } elseif ($result_tenant->num_rows > 0) {
        // If email belongs to tenant
        $_SESSION['submitted_email'] = $emailID;
    } else {
        // If email not found in either table
      echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Reservation not found',
                      timer: 3000, // 3 seconds
                    showConfirmButton: true,
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.href = 'rpemail.php';
                    }
                });
            </script>";
        exit();
    }

    // Send OTP
    if (sendOTP($_SESSION['submitted_email'])) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'OTP Sent',
                    text: 'An OTP has been sent to your email. Please verify.',
                    timer: 3000, // 3 seconds
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.href = 'otp.php';
                    }
                });
            </script>";
        exit();
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to send OTP. Please try again later.',
                    showConfirmButton: true,
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
                       <h1 class="Mb-12">Reset Password<ul class="list-arrow">
                                              <li>Type the email address of your account.</li>
                                            
                                            </ul></h1>
                        <hr>
                      <!-- Include necessary HTML and CSS -->
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" class="form-control" name="email" id="Email" placeholder="email" required>
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