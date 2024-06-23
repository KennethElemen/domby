<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

function sanitizeFormInput($formData) {
    foreach ($formData as $key => $value) {
        // Check if the value is an array (e.g., checkboxes)
        if (is_array($value)) {
            $formData[$key] = sanitizeFormInput($value); // Recursively sanitize array values
        } else {
            // Sanitize individual form field value
            $formData[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
    }
    return $formData;
}

// Sanitize form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are present and not empty
    $requiredFields = ['Name', 'email', 'contact_number', 'date_range', 'duration', 'type_of_stay', 'room_number'];
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        // Handle missing fields, for example, redirect back to the form with an error message
        $errorMessage = "The following fields are required: " . implode(', ', $missingFields);
        // Redirect back to the form with an error message
        header("Location: ../errorpage/unsuccessful.php?error=" . urlencode($errorMessage));
        exit();
    }

    // Proceed with form processing if all required fields are present

    // Sanitize form data
    $_POST = sanitizeFormInput($_POST);
    // Now you can safely use $_POST array for further processing
}

function isDateInPast($date) {
    $today = date("Y-m-d");
    return (strtotime($date) < strtotime($today));
}

// Function to sanitize and validate dates
function sanitizeAndValidateDates($conn, $check_in_date, $check_out_date) {
    // Check if the dates are in valid format (YYYY-MM-DD)
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $check_in_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $check_out_date)) {
        throw new Exception("Invalid date format");
    }

    // Check if the dates are in the past
    if (isDateInPast($check_in_date) || isDateInPast($check_out_date)) {
        throw new Exception("Check-in or check-out date cannot be in the past");
    }

    // Sanitize the dates to prevent XSS
    $check_in_date = htmlspecialchars($check_in_date, ENT_QUOTES, 'UTF-8');
    $check_out_date = htmlspecialchars($check_out_date, ENT_QUOTES, 'UTF-8');

    return array($check_in_date, $check_out_date);
}

// Function to validate room number
function validateRoomNumber($conn, $roomNumber) {
    $stmt = $conn->prepare("SELECT * FROM room_management WHERE room_number = ?");
    $stmt->bind_param("i", $roomNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Form data retrieval
        $check_in_date = $_POST["check_in_date"];
        $check_out_date = $_POST["check_out_date"];
        $full_name = $_POST["Name"];
        $email = $_POST["email"];
        $contact_number = $_POST["contact_number"];
        $type_of_stay = $_POST["type_of_stay"];
        $room_number = $_POST["room_number"];
        $duration = $_POST["duration"];
        $current_date = $_POST["date"];

        // Database connection
        include '../includes/config/dbconn.php';
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Start a transaction
        $conn->begin_transaction();

        // Sanitize and validate dates
        list($check_in_date, $check_out_date) = sanitizeAndValidateDates($conn, $check_in_date, $check_out_date);

        // Check if room number exists
        if (!validateRoomNumber($conn, $room_number)) {
            throw new Exception("Invalid room number.");
        }

        // Fetch the maximum occupancy limit for the room
        $occupancyLimitSql = "SELECT max_occupants FROM room_management WHERE room_number = ?";
        $stmtOccupancyLimit = $conn->prepare($occupancyLimitSql);
        $stmtOccupancyLimit->bind_param("i", $room_number);
        $stmtOccupancyLimit->execute();
        $stmtOccupancyLimit->store_result();
        
        if ($stmtOccupancyLimit->num_rows == 0) {
            echo "Room number not found.";
            throw new Exception("Room number not found.");
        }
        
        $stmtOccupancyLimit->bind_result($maxOccupancy);
        $stmtOccupancyLimit->fetch();
        $stmtOccupancyLimit->close();
        
       
        
        // Fetch the count of accepted reservations for the current room
        $reservationSql = "SELECT COUNT(*) as current_occupants FROM reservations WHERE room_number = ? AND status = 'accepted'";
        $stmtReservation = $conn->prepare($reservationSql);
        $stmtReservation->bind_param("i", $room_number);
        $stmtReservation->execute();
        $stmtReservation->store_result();
        
        if ($stmtReservation->num_rows > 0) {
            $stmtReservation->bind_result($currentOccupants);
            $stmtReservation->fetch();
        }
        
        $stmtReservation->close();
        
      
       
        // Check if current occupants exceed the maximum occupancy limit
        if ($currentOccupants >= $maxOccupancy) {
                 header("Location: ../errorpage/unsuccessful.php");
            throw new Exception("Maximum occupancy limit for the room has been reached.");
            
        }

        // Check if the email already exists in the reservations table
        $emailCheckQuery = "SELECT * FROM reservations WHERE email = ?";
        $stmt = $conn->prepare($emailCheckQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email already exists in reservations
            $stmt->close();
            $conn->rollback();
          
            echo "Email already exists in reservations.";
            exit();
        }

        // Insert reservation data
        $sqlInsertReservation = "INSERT INTO reservations (Name, email, contact_number, check_in_date, check_out_date, type_of_stay, duration, room_number,date) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";
        $stmtInsert = $conn->prepare($sqlInsertReservation);
        $stmtInsert->bind_param("sssssssss", $full_name, $email, $contact_number, $check_in_date, $check_out_date, $type_of_stay, $duration, $room_number,$current_date);

        if ($stmtInsert->execute()) {
            // Reservation successful
            $reservation_id = $stmtInsert->insert_id;

            // Include the reservemail.php file


            // Commit the transaction
            $conn->commit();
            include '../includes/function/reservemail.php';
          
            exit();
        } else {
            // Rollback the transaction in case of an error
            $stmtInsert->close();
            $conn->rollback();
            echo "Error inserting reservation: " . $stmtInsert->error;
            // Uncomment the following line for debugging
            header("Location: ../errorpage/unsuccessful.php");
            exit();
        }
    } catch (Exception $e) {
        // Handle exceptions and rollback the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        // Uncomment the following line for debugging
         
        exit();
    } finally {
        // Close the database connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Room Reservation</title>
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
     <link rel="stylesheet" href="../assets/css/style2.css">
     <link rel="shortcut icon" href="../assets/images/favicon.png" />
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>
    <style>
        .form-check {
            margin-left: 30px;
        }
    </style>
</head>
<body>


    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center justify-content-center auth">
           <div style="text-align: center;">
                        <button type="button" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 10px; margin-right: 10px; margin-bottom: 50px; width: 50px; height: 60px; border-radius: 100%; z-index: 9999; display: flex; align-items: center; justify-content: center;" data-toggle="modal" data-target="#Reservation">
                            HELP
                        </button>
                    </div>
            <div class="row flex-grow">
                <div class="col-lg-3 offset-lg-2">
                    <div class="auth-form-light text-left p-4">
                        <h2 class="display-7" style="color: black;">Dorm Reservation</h2>
                       <form class="pt-3" method="post" action="">
                                        <div class="form-group">
                                            <label for="full_name" style="color: black;">Full Name</label>
                                            <input type="text" class="form-control" id="Name" name="Name" placeholder="Enter your full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" style="color: black;">Email address</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"  data-inputmask="'alias': 'email'" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="contact_number" style="color: black;">Contact Number</label>
                                            <input type="text" class="form-control" id="contact_number" name="contact_number" data-inputmask-alias="(+63) 999-9999-999" placeholder="(+63)XXX-XXXX-XXX" required>
                                        </div>
                                         <div class="form-group">
                                            <label for="date_range" style="color: black;">Period of Stay</label>
                                            <input type="text" class="form-control datepicker" id="date_range" name="date_range" placeholder="Select Check in and check out"  required>
                                        </div>
                                        <div class="form-group">
                                            <label for="duration" style="color: black;">Duration</label>
                                            <input type="text" style="background-color: #edebeb;" class="form-control" id="duration" name="duration"  placeholder="Select period of stay"  readonly required>
                                        </div>

                                        <div class="form-group">
                                            <label for="type_of_stay" style="color: black;">Type of Stay</label>
                                             <input type="text" style="background-color: #edebeb;" class="form-control" id="type_of_stay" name="type_of_stay" placeholder="Select period of stay" readonly required>
                                        </div>
                                        <?php
                                        // Retrieve room number from the query parameter
                                        $selected_room_number = isset($_GET['room_number']) ? $_GET['room_number'] : '';
                                        ?>
                                        <div class="form-group">
                                            <label for="room_number" style="color: black;">Room Number</label>
                                            <input type="number" style="background-color: #edebeb;" class="form-control" id="room_number" name="room_number" value="<?php echo htmlspecialchars($selected_room_number); ?>" readonly>
                                        </div>
                                        <div class="form-group form-check">
                                            <!-- Your checkbox -->
                                            <input type="checkbox" class="form-check-input" id="terms_and_agreement" name="terms_and_agreement" required>
                                            <label class="form-check-label" for="terms_and_agreement">I agree to the <a href="#" data-toggle="modal" data-target="#termsAndAgreementModal" class="text-primary text-decoration-none">Terms and Agreement</a></label>
                                        </div>
                                        <input type="hidden" id="date" name="date" value="">
                                        <input type="hidden" id="check_in_date" name="check_in_date" value="">
                                        <input type="hidden" id="check_out_date" name="check_out_date" value="">
                                        <button type="submit" class="btn btn-gradient-primary me-2" onclick="showSuccessToast('Reservation successful!')">Submit</button>
                                        <button type="button" class="btn btn-light" onclick="location.href='../index';">Cancel</button>
                                    </form>
                    </div>
                </div>
                 <div class="col-lg-6 ml-lg-3">
                    <img src="../assets/images/res/Con1.png" class="img-fluid h-100 w-100">
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="termsAndAgreementModal" tabindex="-1" role="dialog" aria-labelledby="termsAndAgreementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsAndAgreementModalLabel">Terms and Conditions - DormBell Dormitory Management System</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>By using the DormBell Dormitory Management System, you agree to comply with the following terms and conditions:</p>
                
                <ol>
                    <li><strong>Account Registration:</strong> Users must provide accurate and complete information during the registration process. Users are responsible for maintaining the confidentiality of their account credentials.</li>
                    
                    <li><strong>Usage Restrictions:</strong> Users shall not engage in any activity that disrupts the normal operation of the DormBell system, including but not limited to hacking, data breaches, or unauthorized access.</li>

                    <li><strong>Payment and Billing:</strong> Users agree to pay any applicable fees for services provided by DormBell. DormBell reserves the right to modify pricing and payment terms with notice to users.</li>

                    <li><strong>Room Reservations:</strong> Users can reserve rooms through the DormBell system based on availability. Room assignments are subject to dormitory policies.</li>

                    <li><strong>Code of Conduct:</strong> Users must adhere to the dormitory's code of conduct, respecting fellow residents and staff. Violation of conduct may result in account suspension or termination.</li>
                    
                    <li><strong>Privacy:</strong> DormBell collects and processes user data in accordance with its privacy policy. Users can review the privacy policy for details on data handling and protection.</li>

                    <li><strong>Termination of Service:</strong> DormBell reserves the right to terminate or suspend user accounts for violation of terms or for any reason deemed necessary to protect the system's integrity.</li>
                </ol>

                <p>These terms and conditions may be updated by DormBell, and users will be notified of any changes. Continued use of the DormBell Dormitory Management System constitutes acceptance of the updated terms.</p>

                <p>For questions or concerns, please contact DormBell Support.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
  <?php include 'modals.php'; ?>
  <!-- Add flatpickr library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
   <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select input fields
       const inputFields = document.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], textarea');
        // Add event listeners to each input field
        inputFields.forEach(function(inputField) {
            inputField.addEventListener('input', function(event) {
                const maxLength = 40; // Maximum allowed characters
                if (event.target.value.length > maxLength) {
                    event.target.value = event.target.value.slice(0, maxLength); // Truncate input if exceeds limit
                }
            });
        });
    });
document.addEventListener('DOMContentLoaded', function () {
    // Initialize flatpickr for date range
    const dateRangePicker = flatpickr('#date_range', {
        mode: 'range',
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function (selectedDates, dateStr, instance) {
           // Set check_in_date and check_out_date values based on the selected date range
            if (selectedDates.length === 2) {
                document.getElementById('check_in_date').value = dateStr.split(" to ")[0];
                document.getElementById('check_out_date').value = dateStr.split(" to ")[1];
            
                // Calculate the difference in milliseconds between the two dates
                const startDate = new Date(selectedDates[0]);
                const endDate = new Date(selectedDates[1]);
                const timeDiff = Math.abs(endDate.getTime() - startDate.getTime());
            
                // Calculate days and remaining milliseconds
                const days = Math.floor(timeDiff / (1000 * 3600 * 24));
                const remainingMilliseconds = timeDiff % (1000 * 3600 * 24);
            
                // Calculate months
                const months = Math.floor(days / 30);
                const remainingDays = days % 30;
            
                // Build the duration text
                let durationText = '';
                if (months > 0) {
                    durationText += months + (months === 1 ? ' month' : ' months');
                    if (remainingDays > 0) {
                        durationText += ' and ' + remainingDays + (remainingDays === 1 ? ' day' : ' days');
                    }
                } else {
                    durationText += days + (days === 1 ? ' day' : ' days');
                }
            
                // Update the number_of_days field
                document.getElementById('duration').value = durationText;
            
                // Determine the type_of_stay based on the number of days
                const typeOfStay = (days <= 30) ? "Transient" : "Long-term";
                document.getElementById('type_of_stay').value = typeOfStay;
            }
        }
    });

    // Manually trigger the onChange event to set initial values
    dateRangePicker.config.onChange(dateRangePicker.selectedDates, dateRangePicker.input.value, dateRangePicker);

    // Helper function to get the duration text
    function getDurationText(days, weeks, months, years) {
        if (days < 7) {
            return days + " day(s)";
        } else if (weeks < 4) {
            return weeks + " week(s)";
        } else if (months < 12) {
            return months + " month(s)";
        } else {
            return years + " year(s)";
        }
    }
});
</script>
        
     <script>
        // Function to allow only numeric input for number type fields
        function allowOnlyNumericInput(inputField) {
            inputField.addEventListener('keypress', function(event) {
                const keyCode = event.keyCode;
                if (!(keyCode >= 48 && keyCode <= 57) && // Digits 0-9
                    !(keyCode >= 96 && keyCode <= 105) && // Numeric keypad
                    keyCode !== 8 && // Backspace
                    keyCode !== 9 && // Tab
                    keyCode !== 37 && // Left arrow
                    keyCode !== 39 && // Right arrow
                    keyCode !== 46 // Delete
                ) {
                    event.preventDefault();
                }
            });
        }

        // Call the function for each number type input field
        document.addEventListener('DOMContentLoaded', function () {
            const numberInputs = document.querySelectorAll('input[type="number"]');
            numberInputs.forEach(function(input) {
                allowOnlyNumericInput(input);
            });
        });
    </script>
<script>
    // Get the current date
    var currentDate = new Date();
    
    // Format the current date as 'YYYY-MM-DD'
    var formattedDate = currentDate.toISOString().split('T')[0];
    
    // Set the value of the hidden input field to the formatted current date
    document.getElementById('date').value = formattedDate;
</script>

    <script src="../assets/js/dashboard.js"></script>
    <script src="../assets/js/todolist.js"></script>
    <script src="../assets/js/vendor.bundle.base.js"></script>
    <script src="../assets/js/jquery.inputmask.bundle.js"></script>
    <script src="../assets/js/sweetalert.min.js"></script>
     <script src="../assets/js/alerts.js"></script>
     <script src="../assets/js/inputmask.js"></script>
</body>

</html>


