<?php 
include '../includes/config/dbconn.php'; 

session_start();

// Define constants for login attempt tracking
define('MAX_LOGIN_ATTEMPTS', 3); // Maximum allowed login attempts
define('LOGIN_ATTEMPT_TIMEOUT', 120); // Timeout in seconds (2 minutes)

function login($email, $password, $conn){
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Hash the provided password
    $hashed_password = hash('sha256', $password);

    // Check the admin credentials
    $adminQuery = "SELECT * FROM admins WHERE email=? AND password=?";
    $adminStmt = mysqli_prepare($conn, $adminQuery);
    mysqli_stmt_bind_param($adminStmt, "ss", $email, $hashed_password);
    mysqli_stmt_execute($adminStmt);
    $adminResult = mysqli_stmt_get_result($adminStmt);

    // Check the tenant credentials
    $tenantQuery = "SELECT * FROM tenants WHERE EmailID=? AND password=?";
    $tenantStmt = mysqli_prepare($conn, $tenantQuery);
    mysqli_stmt_bind_param($tenantStmt, "ss", $email, $hashed_password);
    mysqli_stmt_execute($tenantStmt);
    $tenantResult = mysqli_stmt_get_result($tenantStmt);

    // Handle successful login for admin
    if (mysqli_num_rows($adminResult) > 0) {
        $_SESSION['login_attempts'] = 0; // Reset login attempts on successful login
        unset($_SESSION['last_login_attempt']); // Reset last login attempt time
        $adminData = mysqli_fetch_assoc($adminResult);
        $_SESSION['AdminID'] = $adminData['AdminID'];
        $_SESSION['user_type'] = 'admin';
        return "admin";
    }
    // Handle successful login for tenant
    elseif (mysqli_num_rows($tenantResult) > 0) {
        $tenantData = mysqli_fetch_assoc($tenantResult);
        if ($tenantData['status'] === 'disabled') {
            return "account_disabled";
        }
        $_SESSION['login_attempts'] = 0; // Reset login attempts on successful login
        unset($_SESSION['last_login_attempt']); // Reset last login attempt time
        $_SESSION['TenantID'] = $tenantData['TenantID'];
        $_SESSION['user_type'] = 'tenant';
        return "tenant";
    } else {
        // Increment login attempts
        $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;

        // Set last login attempt time
        $_SESSION['last_login_attempt'] = time();

        // Check if the maximum login attempts have been reached
        if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION['brute_force'] = time(); // Set brute force activation time
            $elapsedTime = time() - $_SESSION['last_login_attempt'];
            $remainingTime = max(0, LOGIN_ATTEMPT_TIMEOUT - $elapsedTime);
            return "brute_force_protected|$remainingTime";
        }

        // Incorrect credentials or other errors
        return "invalid_credentials";
    }
}


$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    $loginResult = login($email, $password, $conn);

    if ($loginResult === "account_disabled") {
        $errors['login'] = 'Your account is currently disabled.';
    } elseif ($loginResult === "admin") {
        session_regenerate_id(true);
        header("Location: ../Admin/dashboard/dashboard");

        exit();
    } elseif ($loginResult === "tenant") {
        session_regenerate_id(true);
        header("Location: ../tenants/dashboard/dashboard");
        exit();
    } elseif (strpos($loginResult, "brute_force_protected") !== false) {
        // Extract remaining time from login result
        $parts = explode("|", $loginResult);
        $remainingTime = $parts[1];
        $errors['brute_force'] = $remainingTime; // Set brute force remaining time
    } else {
        // Incorrect credentials or other errors
        $errors['login'] = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="//code.tidio.co/yzizubnbzmlulcoqcaefpa5qdziy2ezs.js" async></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="../assets/images/favicon.png" />
    <style>
        /* Add custom style for the content-wrapper and main-container */
        .content-wrapper, .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

 .navbar {
            padding: 10px 0;
            background-color: #f8f9fa; /* Light gray background */
        }

        .navbar-brand img {
            max-height: 40px; /* Adjust logo height */
        }

        .custom-nav-link {
            font-size: 18px; /* Adjust the font size as needed */
            transition: color 0.3s ease; /* Add a smooth transition effect */
        }

        .custom-nav-link:hover {
            color: purple; /* Change the hover color to purple */
        }

.wave {
  animation-name: wave-animation;  /* Refers to the name of your @keyframes element below */
  animation-duration: 2.5s;        /* Change to speed up or slow down */
  animation-iteration-count: infinite;  /* Never stop waving :) */
  transform-origin: 70% 70%;       /* Pivot around the bottom-left palm */
  display: inline-block;
}

@keyframes wave-animation {
    0% { transform: rotate( 0.0deg) }
   10% { transform: rotate(14.0deg) }  /* The following five values can be played with to make the waving more or less extreme */
   20% { transform: rotate(-8.0deg) }
   30% { transform: rotate(14.0deg) }
   40% { transform: rotate(-4.0deg) }
   50% { transform: rotate(10.0deg) }
   60% { transform: rotate( 0.0deg) }  /* Reset for the last half to pause */
  100% { transform: rotate( 0.0deg) }
}





    
    </style>
</head>
<body  style="background-color:#c4b0d8;">
    <script src="//code.tidio.co/vr072utkoakvng9rlwemwhd5vxqxzcx5.js" async></script>

        <nav class="navbar navbar-expand-lg navbar-light bg-white default-layout-navbar fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">   
                    <img src="../assets/images/DormBell.png" alt="logo" class="img-fluid" />
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="login">
                                <p class="mb-0 text-lg custom-nav-link"><strong>Login</strong></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index">
                                <p class="mb-0 text-lg custom-nav-link"><strong>Rooms</strong></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="About">
                                <p class="mb-0 text-lg custom-nav-link"><strong>About Us</strong></p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


    
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <!-- Section 1: Login Form -->
            <div class="content-wrapper d-flex align-items-center justify-content-center auth" style="background-color:#c4b0d8 ">
                <div class="row flex-grow">
                    <!-- Center the image on the left with right margin -->
                    <div class="col-lg-6 text-center mr-lg-4">
                        <img src="../assets/images/1.webp" class="img-fluid">
                    </div>
                    <!-- Center the form on the right -->
                    <div class="col-lg-3">
                        <div class="auth-form-light text-left p-4 shadow-lg">
                                    <h1 class="display-8"><span id="welcome"></span> <span class="wave">👋</span></h1>
                                     
                                        <hr>
                                   <form class="pt-3" action=""method="post">
                                  <!-- Display error messages below input fields -->
                                    <?php if (!empty($errors['brute_force'])): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <span id="brute-force-timer" data-remaining-time="<?php echo $errors['brute_force']; ?>"></span> <!-- Add remaining time as a data attribute -->
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($errors['login'])): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $errors['login']; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-lg" id="Email" name="Email" placeholder="Email@gmail.com" required>
                                    </div>
                                    <div class="form-group">
                                     
                                        <div class="input-group-prepend">
                                             <input type="password" class="form-control" name="Password" id="Password" placeholder="Password">
                                            <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                                <i class="mdi mdi-eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                       
                                            <button type="submit" class="btn btn-gradient-primary btn-lg font-weight-medium auth-form-btn">Log in</button>
                                       
                                    </div>


                                    <div class="my-2 d-flex justify-content-between align-items-left m-50px">
                                        <a href="rpemail.php" class="text-muted">Forgot password?</a>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               
                </div>
            </div>
            <!-- End Section 3 -->
            
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <footer style="background-color:#c4b0d8; margin-bottom: 20px;">
            <div class="d-flex justify-content-center align-items-center">
                <span class="text-center" style="color: white;">© 2024 DORMBELL ALL RIGHTS RESERVE</span>
            </div>
        </footer>
        
  
<script>
    const welcomeText = "Welcome ";
    const typingElement = document.getElementById('welcome');
    let index = 0;

    function typeWelcome() {
      typingElement.textContent += welcomeText[index];
      index++;
      if (index < welcomeText.length) {
        setTimeout(typeWelcome, 200); // typing speed
      }
    }

    typeWelcome();
  </script>
  
  
      <script>
    function togglePassword() {
        var passwordInput = document.getElementById('Password');
        passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
    }

    // Function to update and display the remaining time for brute force protection
    function updateBruteForceTimer() {
        var timerElement = document.getElementById('brute-force-timer');
        var remainingTime = parseInt(timerElement.dataset.remainingTime);
        if (remainingTime > 0) {
            var lastLoginTime = parseInt(localStorage.getItem('lastLoginTime')) || 0;
            var currentTime = Math.floor(Date.now() / 1000); // Current time in seconds
            var elapsedTime = currentTime - lastLoginTime;
            var actualRemainingTime = remainingTime - elapsedTime; // Calculate actual remaining time
            if (actualRemainingTime > 0) {
                var minutes = Math.floor(actualRemainingTime / 60);
                var seconds = actualRemainingTime % 60;
                timerElement.textContent = 'Brute force protection activated. Please try again after ' + minutes + ' minutes and ' + seconds + ' seconds.';
                // Disable the login button
                document.querySelector('.auth-form-btn').setAttribute('disabled', 'disabled');
            } else {
                timerElement.textContent = ''; // Hide timer when remaining time is 0 or less
                // Reset remaining time and enable the login button
                timerElement.dataset.remainingTime = 0;
                document.querySelector('.auth-form-btn').removeAttribute('disabled');
            }
        }
    }


    // Call the function to update and display the brute force protection timer
    window.onload = function() {
        var remainingTime = <?php echo isset($errors['brute_force']) ? $errors['brute_force'] : 0; ?>;
        var timerElement = document.getElementById('brute-force-timer');
        if (remainingTime > 0) {
            localStorage.setItem('lastLoginTime', Math.floor(Date.now() / 1000)); // Store current time
            timerElement.dataset.remainingTime = remainingTime; // Update remaining time
            updateBruteForceTimer();
            setInterval(updateBruteForceTimer, 1000); // Update timer every second
        }
    };
</script>
        


  
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/login.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/todolist.js"></script>
   
     <script src="assets/js/vendor.bundle.base.js"></script>
    <script src="assets/js/sweetalert.min.js"></script>
     <script src="assets/js/alerts.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- End custom js for this page -->
</body>
</html>