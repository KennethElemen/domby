<?php
include_once '../../includes/config/dbconn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include_once '../../includes/function/check_session.php';

checkSession($conn, ['tenant']);


?>




<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>

  <body>
    <div class="container-scroller">
        <?php 
        // Function to reset password for tenant accounts
function resetPassword($currentPassword, $newPassword, $confirmPassword, $conn) {
    // Start or resume the session
    session_start();

    // Get the tenant ID from the session
    $tenantID = $_SESSION['TenantID'];

    // Check if any field is empty
    if (empty($tenantID) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        return "All fields are required";
    }

    // Retrieve the user data from the database
    $query = "SELECT * FROM tenants WHERE TenantID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $tenantID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists
    if (!$user) {
        return "User not found";
    }

    // Hash the current and new passwords using SHA-256
    $currentPasswordHash = hash('sha256', $currentPassword);
    $newPasswordHash = hash('sha256', $newPassword);

    // Compare the current password hash with the stored password hash
    if ($currentPasswordHash !== $user['Password']) {
        return "Incorrect current password";
    }

    // Check if the new password matches the confirm password
    if ($newPasswordHash !== hash('sha256', $confirmPassword)) {
        return "New password and confirm password do not match";
    }

    // Update the password in the database with the hashed new password
    $updateQuery = "UPDATE tenants SET Password = ? WHERE TenantID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ss', $newPasswordHash, $tenantID);
    $updateStmt->execute();

    // Destroy the session
    session_destroy();

    return "Password updated successfully";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $currentPassword = $_POST['Password'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Call the resetPassword function
    $result = resetPassword($currentPassword, $newPassword, $confirmPassword, $conn);

    // Check the result and display Swal notification accordingly
    if ($result === "Password updated successfully") {
        // Success: Display Swal notification
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Password updated successfully!',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '../../index.php';
                });
              </script>";
    } else {
        // Error: Display Swal notification
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '$result',
                    showConfirmButton: false,
                    timer: 3000
                });
              </script>";
    }
}
        ?>
      <!-- partial:../../partials/_navbar.html -->
      <?php include '../topbar.php'; ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
        <?php include '../sidebar.php'; ?>
        <!-- partial -->
          <div class="main-panel">
            <div class="alert-container"></div>

          <div class="content-wrapper">
          <div class="row flex-grow-1">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body shadow-lg">
                  <h1 class="mb-12">RESET PASSWORD</h1>
                  <hr>
                   <form class="forms-sample" method="post" action="">
                  <div class="form-group">
                          <label for="exampleInputCurrentPassword">Current Password</label>
                          <input type="password" class="form-control" name="Password" id="Password" placeholder="Current Password" required>
                          
                          
                      </div>
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
                            <button type="submit" class="btn btn-gradient-primary">Submit</button>
                        </form>
                </div>
              </div>
            </div>
          </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:../../partials/_footer.html -->
            <?php include '../footer.php'; ?>
        <?php include '../modals.php'; ?>   
        </div>
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
