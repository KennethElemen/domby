<?php
include '../includes/config/dbconn.php';

// Create a database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

$aboutResult = mysqli_query($dbConnection, "SELECT  DormName, LandlordName, FacebookName, Location, ContactNumber, FacebookLink FROM about");
$aboutInfo = mysqli_fetch_assoc($aboutResult);

// Use this information when generating the contract content
$dormname = $aboutInfo['DormName'];
$contactnumber = $aboutInfo['ContactNumber'];
$fblink = $aboutInfo['FacebookLink'];
$landlordName = $aboutInfo['LandlordName'];
$fbname = $aboutInfo['FacebookName'];
$location = $aboutInfo['Location'];

// Extract src attribute from the iframe tag
preg_match('/src="([^"]+)"/', $location, $matches);
$iframeSrc = isset($matches[1]) ? $matches[1] : '';

// Store the iframe src in the database or use it as needed

$dbConnection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>About Us</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/About.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="../assets/images/favicon.png" />
    <style>
        /* Add your existing custom styles here */
        .map_inner {
            margin-top: 50px;
        }

        .map_bind {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .info_single i {
            font-size: 2em; /* Adjust the size as needed */
            margin-right: 10px; /* Optional: Add some space between the icon and the text */
        }

        .info_single .info_text {
            font-size: .8em; /* Adjust the size as needed */
            width: 50%; /* Adjust the width as needed */
            display: inline-block; /* Ensures the text doesn't take the full width */
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
    </style>
</head>
<body style="background-color:#c4b0d8 ">
    <script src="//code.tidio.co/vr072utkoakvng9rlwemwhd5vxqxzcx5.js" async></script>
 <!-- Navigation Bar -->
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
    
    <div class="container-fluid page-body-wrapper full-page-wrapper ">
        <div class="content-wrapper d-flex align-items-center justify-content-center auth " style="background-color:#c4b0d8 ">

    <div class="container" >
        <div class="row" >
            <div class="col-md-10 offset-md-1">
                <div class="contact_inner" style="background-color:#f2edf3">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="contact_form_inner">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12 offset-md-1">
                                            <div class="map_inner">
                                                <h4 class="mt-4">Find Us on Google Map</h4>
                                                <div class="map_bind" style="width: 80%; height: 450px; overflow: hidden;">
                                                    <iframe src="<?php echo htmlspecialchars($iframeSrc, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="100%" style="border: 0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="right_conatct_social_icon d-flex align-items-end">
                                <div class="contact_info_sec">
                                    <h4>Contact Info</h4>
                                    <div class="d-flex info_single align-items-center">
                                        <i class="mdi mdi-map-marker-radius menu-icon"></i>
                                        <span class="info_text"><?php echo htmlspecialchars($dormname, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="d-flex info_single align-items-center">
                                        <i class="mdi mdi-account-circle menu-icon"></i>
                                        <span class="info_text"><?php echo htmlspecialchars($landlordName, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="d-flex info_single align-items-center">
                                        <i class="mdi mdi-cellphone-iphone menu-icon"></i>
                                        <span class="info_text"><?php echo htmlspecialchars($contactnumber, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="d-flex info_single align-items-center">
                                        <i class="mdi mdi-facebook-box menu-icon"></i>
                                        <a href="<?php echo htmlspecialchars($fblink, ENT_QUOTES, 'UTF-8'); ?>">
                                            <span class="info_text"><?php echo htmlspecialchars($fbname, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
    <footer style="background-color:#c4b0d8; margin-bottom: 20px;">
            <div class="d-flex justify-content-center align-items-center">
                <span class="text-center" style="color: white;">© 2024 DORMBELL ALL RIGHTS RESERVE</span>
            </div>
        </footer>
     
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
