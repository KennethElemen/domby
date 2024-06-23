<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

function checkSession($conn, $allowedUserTypes = [], $requireOTP = false) {
    if (!isset($_SESSION['user_type'])) {
        // Redirect to index page if no valid user is logged in
        header("Location: ../../errorpage/unsuccessful.php");
        exit();
    }

    $user_type = $_SESSION['user_type'];

    if (!in_array($user_type, $allowedUserTypes)) {
        // Redirect to index page if the user type is not allowed for this file
        header("Location: ../../errorpage/unsuccessful.php");
        exit();
    }

    // Check for OTP-related session variables if required
    if ($requireOTP) {
        if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email'])) {
            // Redirect to index page if OTP-related session variables are missing
          header("Location: ../../errorpage/unsuccessful.php");
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
   header("Location: ../../errorpage/unsuccessful.php");
    exit();
    


// Use the checkSession function to validate the session for admins or tenants
checkSession($conn, ['admin', 'tenant'], true);



}
?>



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
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- End layout styles -->
   <link rel="shortcut icon" href="../assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="col-lg-5 col-md-8 col-sm-10 mx-auto text-left">
                <div class="card">
                    <div class="card-body">
                        <h2 class="display-7" style="color: black;">Reset Password<ul class="list-arrow">
                                              <li>Your about to change the password for  <strong><?php echo $_SESSION['submitted_email'] ?? ''; ?>.</strong></li>
                                            </ul>
                    </h2>
                     <form method="post" action="../includes/function/update_password.php">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="input-group"> 
                                    <input type="password" class="form-control" name="password" id="password" placeholder="New password" required oninput="validatePasswordMatch()">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                            <i class="mdi mdi-eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Conpassword">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirm_password" id="Conpassword" placeholder="Confirm password" required oninput="validatePasswordMatch()">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword1()">
                                            <i class="mdi mdi-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                 <small id="passwordHelpBlock" class="form-text text-danger"></small>
                                       <small id="passwordMatchMessage" class="form-text text-danger"></small> <!-- Error message placeholder -->
                            </div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <button type="button" class="btn btn-light">Cancel</button>
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
    function togglePassword() {
        var passwordInput = document.getElementById('password');
        passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
    }
    function togglePassword1() {
        var passwordInput = document.getElementById('Conpassword');
        passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
    }
</script>
<script>
   
      document.getElementById("password").addEventListener("input", function() {
        var password = this.value;
        var passwordHelpBlock = document.getElementById("passwordHelpBlock");
        var hasUppercase = /[A-Z]/.test(password);
        var hasLowercase = /[a-z]/.test(password);
        var hasNumber = /\d/.test(password);
        var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        if (password.length < 8) {
            passwordHelpBlock.textContent = "Password must be at least 8 characters long.";
            passwordHelpBlock.classList.add("text-danger"); // Using Bootstrap's text-danger class for red color
        } else if (!hasUppercase) {
            passwordHelpBlock.textContent = "Password must contain at least one uppercase letter.";
            passwordHelpBlock.classList.add("text-danger"); // Using Bootstrap's text-danger class for red color
        } else if (!hasLowercase) {
            passwordHelpBlock.textContent = "Password must contain at least one lowercase letter.";
            passwordHelpBlock.classList.add("text-danger"); // Using Bootstrap's text-danger class for red color
        } else if (!hasNumber) {
            passwordHelpBlock.textContent = "Password must contain at least one number.";
            passwordHelpBlock.classList.add("text-danger"); // Using Bootstrap's text-danger class for red color
        } else if (!hasSpecialChar) {
            passwordHelpBlock.textContent = "Password must contain at least one special character.";
            passwordHelpBlock.classList.add("text-danger"); // Using Bootstrap's text-danger class for red color
        } else {
            passwordHelpBlock.textContent = "";
            passwordHelpBlock.classList.remove("text-danger"); // Remove text-danger class if all criteria met
        }
    });
     function validatePasswordMatch() {
        var newPassword = document.getElementById("password").value;
        var confirmPassword = document.getElementById("Conpassword").value;
        var passwordMatchMessage = document.getElementById("passwordMatchMessage");

        if (newPassword !== confirmPassword) {
            passwordMatchMessage.textContent = "New password and confirm password do not match";
            passwordMatchMessage.classList.add("text-danger"); // Using Bootstrap's text-danger class for red color
        } else {
            passwordMatchMessage.textContent = "";
            passwordMatchMessage.classList.remove("text-danger"); // Remove text-danger class if passwords match
        }
    }
</script>
    <!-- container-scroller -->
    <!-- plugins:js -->
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