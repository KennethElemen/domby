<?php
include 'includes/config/dbconn.php';

// Log received data

$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dorm Bell</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />


    <style>
        /* Add your custom styles here */
        .custom-img {
            padding: 20px;
            height: 300px;
            object-fit: cover;
            width: 300px;
            /* Ensure the width is 100% to maintain responsiveness */
            cursor: pointer; /* Add cursor pointer for better UX */
        }
        /* Add custom style for the content-wrapper and main-container */
         .content-wrapper,
        .main-container {
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

    </style>
    
</head>
<body style="background-color:#c4b0d8;">
    <script src="//code.tidio.co/vr072utkoakvng9rlwemwhd5vxqxzcx5.js" async></script>
    <nav class="navbar navbar-expand-lg navbar-light bg-white default-layout-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/DormBell.png" alt="logo" class="img-fluid" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Guest/login">
                            <p class="mb-0 text-lg custom-nav-link"><strong>Login</strong></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index">
                            <p class="mb-0 text-lg custom-nav-link"><strong>Rooms</strong></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Guest/About">
                            <p class="mb-0 text-lg custom-nav-link"><strong>About us</strong></p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid page-body-wrapper full-page-wrapper ">
        <div class="content-wrapper d-flex align-items-center justify-content-center auth "style="background-color:#c4b0d8" >
            <div class="main-container">
                <div class="container-fluid">
                    <div class="col-20 text-center mb-4">
                    <h1 class="display-1 p-20" style='color:#ffffff; '>AVAILABLE ROOMS</h1>
                     <p class="tagline" style=" font-size: 1.5em; color:#ffffff;">Find the perfect space for your needs</p>
                    </div>
                    <style>
    /* CSS for fade-in animation */
    .fade-in {
        animation: fadeIn 1.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>

<!-- Available Rooms Section -->
<div class="row fade-in">
    <div class="container">
        <div class="row">
           <?php
$dbConnection = new mysqli($servername, $username, $password, $dbname);

if (!$dbConnection) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT rm.*, COUNT(r.room_number) AS occupants, MIN(r.check_out_date) AS earliest_checkout FROM room_management rm LEFT JOIN reservations r ON rm.room_number = r.room_number AND r.status = 'accepted' GROUP BY rm.room_number";
$result = mysqli_query($dbConnection, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Fetch images for the room
        $images = explode(',', $row['images']);

        echo '<div class="col-md-6 stretch-card grid-margin custom-card-width">';
        echo '<div class="card card-img-holder shadow-lg" style="border-radius: 40px; background: #f2edf3; box-shadow: 21px 21px 15px #4e4656,-21px -21px 15px #ffffff;">';
        echo '<div id="carouselExample' . $row['room_number'] . '" class="carousel slide" data-ride="carousel" style="height: 300px; overflow: hidden;">';
        echo '<div class="carousel-inner" style="height: 100%;">';

        foreach ($images as $index => $image) {
            echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '" style="height: 100%;">';
            // Display the image with FancyBox
            echo '<a data-fancybox="gallery' . $row['room_number'] . '" href="Admin/uploads/' . $image . '">';
            echo '<img src="Admin/uploads/' . $image . '" class="d-block mx-auto img-fluid custom-img" alt="Room Image" style="max-height: 100%; width: auto;">';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';

        echo '<a class="carousel-control-prev" href="#carouselExample' . $row['room_number'] . '" role="button" data-slide="prev">';
        echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        echo '<span class="sr-only">Previous</span>';
        echo '</a>';
        echo '<a class="carousel-control-next" href="#carouselExample' . $row['room_number'] . '" role="button" data-slide="next">';
        echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        echo '<span class="sr-only">Next</span>';
        echo '</a>';
        echo '</div>';

        echo '<div class="card-body">';
        echo '<h1 class="card-title p-1 h2"> ROOM No : ' . $row['room_number'] . '</h1>';
        echo '<hr>';
        echo '<div class="d-flex align-items-center ml-3">';
        echo '<h6 class="card-title mb-0 mr-3">
                <i class="mdi mdi-account-multiple"></i>
                OCCUPANTS 
                <span class="text-success">' . $row['occupants'] . '</span>/
                <span class="text-danger">' . $row['max_occupants'] . '</span>
            </h6>';
        echo '<span class="flex-grow-1"></span>'; // Flexible space to push "Down Payment" to the right
        echo '<p class="mb-0"><strong></i> Down Payment:</strong> ₱ ' . $row['down_payment'] . '</p>';
        echo '</div>'; // End d-flex


        echo '<hr>';
        echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#detailsModal' . $row['room_number'] . '"><i class="mdi mdi-information"></i> View Details</button>';

        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Modal for details
        echo '<div class="modal fade" id="detailsModal' . $row['room_number'] . '" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel' . $row['room_number'] . '" aria-hidden="true">';
        echo '<div class="modal-dialog modal-dialog-centered" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="detailsModalLabel' . $row['room_number'] . '"><i class="mdi mdi-information"></i> Room Details</h5>';
        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button>';
        echo '</div>';
        echo '<div class="modal-body">';
        // Place all the detailed information here
        echo '<div id="carouselModalExample' . $row['room_number'] . '" class="carousel slide" data-ride="carousel">'; // Carousel for images in modal
        echo '<div class="carousel-inner">';

        foreach ($images as $index => $image) {
            echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
            // Display the image with FancyBox in modal
            echo '<a data-fancybox="galleryModal' . $row['room_number'] . '" href="Admin/uploads/' . $image . '">';
            echo '<img src="Admin/uploads/' . $image . '" class="d-block mx-auto img-fluid custom-img" alt="Room Image">';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
        echo '<a class="carousel-control-prev" href="#carouselModalExample' . $row['room_number'] . '" role="button" data-slide="prev">';
        echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        echo '<span class="sr-only">Previous</span>';
        echo '</a>';
        echo '<a class="carousel-control-next" href="#carouselModalExample' . $row['room_number'] . '" role="button" data-slide="next">';
        echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        echo '<span class="sr-only">Next</span>';
        echo '</a>';
        echo '</div>';
        echo '<hr>';
        echo '<p><strong><i class="mdi mdi-format-text"></i> Description:</strong> ' . $row['description'] . '</p>';
        echo '<hr>';
        echo '<p><strong><i class="mdi mdi-door"></i> Room Type:</strong> ' . $row['room_type'] . '</p>';
        echo '<p><strong><i class="mdi mdi-home"></i> Room Name:</strong> ' . $row['room_name'] . '</p>';
        echo '<p><strong><i class="mdi mdi-bed-empty"></i> Number of Beds:</strong> ' . $row['num_of_beds'] . '</p>';
        echo '<p><strong>Regular Rate:</strong> ₱ ' . $row['rate_per_night'] . ' /head/Month</p>';
        echo '<p><strong> Transient Rate:</strong> ₱ ' . $row['rate_per_month'] . ' /head/Night</p>';
        echo '<p><strong> Down Payment:</strong> ₱ ' . $row['down_payment'] . '</p>';
        // Add other details as needed
        $reservationCount = $row['occupants'];
        $buttonColor = $reservationCount >= $row['max_occupants'] ? 'btn-secondary' : 'btn-gradient-primary';
        $onClickAction = $reservationCount >= $row['max_occupants'] ? 'return false;' : '';
        if ($row['occupants'] >= $row['max_occupants']) {
            // Add border warning class
            echo '<button type="button" class="btn btn-sm btn-danger btn-rounded text-center p-2 m-2 text-white">';
            echo 'Room will be available on '; // Check if max occupants reached
            echo '<span>' . ($row['earliest_checkout'] ? date("F j, Y", strtotime($row['earliest_checkout'])) : 'No reservations') . '</span>';
            echo '</button>'; // Close the button
        } else {
            echo '<div class="mt-3">';
            echo '<a class="btn ' . $buttonColor . ' btn-lg font-weight-medium auth-form-btn" href="Guest/Reservation.php?room_number=' . $row['room_number'] . '" onclick="' . $onClickAction . '"><i class="mdi mdi-book-open"></i> BOOK</a>';
            echo '</div>';
        }
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-12 text-center">No available rooms found</div>';
}
mysqli_close($dbConnection);
?>

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


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script>
        // Initialize FancyBox
        $('[data-fancybox="images"]').fancybox({
            // Add custom options here if needed
        });
    </script>
</body>
</html>
