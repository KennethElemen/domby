


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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start the session

include '../includes/config/dbconn.php';
$conn = new mysqli($servername, $username, $password, $dbname);

function getAdminEmail1($conn) {
    // Query to fetch the email of the first admin (assuming there's only one admin)
    $sql = "SELECT Email FROM admins LIMIT 1";

    // Execute the query
    $result = $conn->query($sql);

    // Check if a result is found
    if ($result && $result->num_rows > 0) {
        // Fetch the email and return
        $row = $result->fetch_assoc();
        return $row['Email'];
    }

    // If no result is found, return null
    return null;
}

// Get the logged-in admin's email
$loggedInEmail = getAdminEmail1($conn);




function sendNotifAdmin($loggedInEmail, $email, $duration, $type_of_stay, $check_in_date, $check_out_date) {
    // Your email content and sending logic here
    $to = $loggedInEmail;
    $subject = 'Reservation Rescheduled';
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="Contract Details">
            <style type="text/css">
                a:hover {
                    text-decoration: underline !important;
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f2f3f8; font-family: Arial, sans-serif;">
            <table cellspacing="0" align="center" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="font-family: Arial, sans-serif;">
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 670px; margin: 0 auto; background-color: #ffffff; border-radius: 3px; box-shadow: 0 6px 18px 0 rgba(0,0,0,.06);">
                            <tr>
                                <td style="height: 40px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 0 35px;">
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Reschedule</h1>
                                    <hr>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        $email just updated their schedule of stay
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Duration: $duration<br>
                                        Type of Stay: $type_of_stay<br>
                                        Check-in Date: $check_in_date<br>
                                        Check-out Date: $check_out_date<br>
                                        <br>
                                        <br>
                                        Thank you.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: center;'>
                                    <a href='https://dormbell.online/' style='background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;'>Login to Dormbell</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
HTML;

    $headers = 'From: BellDorm' . "\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        'MIME-Version: 1.0' . "\r\n" .
        'Content-Type: text/html; charset=utf-8';

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        // Email sent successfully
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent',
                    text: 'Email sent successfully.'
                });
             </script>";
    } else {
        // Failed to send email
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Send Email',
                    text: 'Failed to send email. Please try again later.'
                });
             </script>";
    }
}

function sendReservationEmail($email, $duration, $type_of_stay, $check_in_date, $check_out_date) {
    // Your email content and sending logic here
    $to = $email;
    $subject = 'Reservation Rescheduled';
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="Contract Details">
            <style type="text/css">
                a:hover {
                    text-decoration: underline !important;
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f2f3f8; font-family: Arial, sans-serif;">
            <table cellspacing="0" align="center" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="font-family: Arial, sans-serif;">
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 670px; margin: 0 auto; background-color: #ffffff; border-radius: 3px; box-shadow: 0 6px 18px 0 rgba(0,0,0,.06);">
                            <tr>
                                <td style="height: 40px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 0 35px;">
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Reschedule</h1>
                                    <hr>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Hello, $email
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Your reservation has been successfully rescheduled
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Duration: $duration<br>
                                        Type of Stay: $type_of_stay<br>
                                        Check-in Date: $check_in_date<br>
                                        Check-out Date: $check_out_date<br>
                                        <br>
                                        <br>
                                        Thank you.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 40px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
HTML;

    $headers = 'From: BellDorm' . "\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        'MIME-Version: 1.0' . "\r\n" .
        'Content-Type: text/html; charset=utf-8';

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        // Email sent successfully
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent',
                    text: 'Email sent successfully.'
                });
             </script>";
    } else {
        // Failed to send email
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Send Email',
                    text: 'Failed to send email. Please try again later.'
                });
             </script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the session
    $email = $_SESSION['submitted_email'] ?? '';

    // Log the email for debugging
    error_log('Submitted email: ' . $email);

    // Check if the email is set in the session
    if (empty($email)) {
        // Redirect with an error message if email is empty
        error_log('Email is empty in session');
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Email Empty',
                    text: 'Email is empty in session'
                });
             </script>"; // Add debugging statement
        exit();
    }

    // Retrieve all form data
    $duration = $_POST["duration"];
    $type_of_stay = $_POST["type_of_stay"];
    $check_in_date = $_POST["check_in_date"];
    $check_out_date = $_POST["check_out_date"];

    // Check if any of the form fields are empty
    if (empty($duration) || empty($type_of_stay) || empty($check_in_date) || empty($check_out_date)) {
        // Log the error for debugging
        error_log('One or more form fields are empty.');
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Fields',
                    text: 'One or more form fields are empty.'
                });
             </script>"; // Add debugging statement
        exit();
    }

    if ($conn->connect_error) {
        // Log the connection error for debugging
        error_log('Connection failed: ' . $conn->connect_error);
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Connection failed. Please try again later.'
                });
             </script>"; // Add debugging statement
        die("Connection failed: " . $conn->connect_error);
    }

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Update reservation with the new data
        $updateReservationQuery = "UPDATE reservations SET 
                                    duration = ?, 
                                    type_of_stay = ?, 
                                    check_in_date = ?, 
                                    check_out_date = ? 
                                    WHERE email = ?";
        $stmt = $conn->prepare($updateReservationQuery);
        $stmt->bind_param("sssss", $duration, $type_of_stay, $check_in_date, $check_out_date, $email);
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            // Commit the transaction
            $conn->commit();

            // Include the file for sending email if it exists
            $reservemailPath = '../includes/function/reservemail.php';
            if (file_exists($reservemailPath)) {
                include $reservemailPath;
            } else {
                // Handle error: File not found
                die("<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'File Not Found',
                            text: 'reservemail.php file not found.'
                        });
                     </script>");
            }

            // Check if the function exists
            if (!function_exists('sendReservationEmail')) {
                // Handle error: Function not defined
                die("<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Function Not Defined',
                            text: 'sendReservationEmail function not defined.'
                        });
                     </script>");
            }
          
            // Now call the function to send the email
            sendReservationEmail($email, $duration, $type_of_stay, $check_in_date, $check_out_date);

            // Send notification email to admin
            sendNotifAdmin($loggedInEmail, $email, $duration, $type_of_stay, $check_in_date, $check_out_date);
            
            // Redirect with success message
            echo "<script>
                     Swal.fire({
                        icon: 'success',
                        title: 'Update Successful',
                        text: 'Reservation updated successfully.',
                        timer: 3000, // Time in milliseconds (3 seconds in this example)
                    }).then(() => {
                          window.location.href = '../index.php';
                    });
                </script>";
            exit();
        } else {
            // Rollback the transaction if no rows were affected
            $conn->rollback();
            // Log the update failure for debugging
            error_log('Update failed. No rows were affected.');
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'Update failed. No rows were affected.'
                    });
                 </script>"; // Add debugging statement
          
            exit();
        }
    } catch (Exception $e) {
        // Handle exceptions and rollback the transaction
        $conn->rollback();
        // Log the exception for debugging
        error_log('Error: ' . $e->getMessage());
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error Occurred',
                    text: 'Error: " . $e->getMessage() . "'
                });
             </script>"; // Output the error message for debugging
        exit();
    } finally {
        $conn->close();
    }
}
?>

    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center justify-content-center auth">
          
                <div class="col-lg-3 offset-lg-2">
                    <div class="auth-form-light text-left p-4">
                        <h2 class="display-7" style="color: black;">Dorm Reservation<ul class="list-arrow">
                                              <li>Your about to reschedule the reservation for  <strong><?php echo $_SESSION['submitted_email'] ?? ''; ?>.</strong></li>
                                            </ul>
                    </h2>
                       <form class="pt-3" method="post" action="">
                                        <div class="form-group">
                                            <label for="date_range" style="color: black;">Period of Stay</label>
                                            <input type="text" class="form-control datepicker" id="date_range" name="date_range" placeholder="Check in and checkout" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="duration" style="color: black;">Duration</label>
                                            <input type="text" style="background-color: #edebeb;" class="form-control" id="duration" name="duration" placeholder="Fill out Preriod of Stay" readonly required>
                                        </div>

                                        <div class="form-group">
                                            <label for="type_of_stay" style="color: black;">Type of Stay</label>
                                             <input type="text" style="background-color: #edebeb;" class="form-control" id="type_of_stay" name="type_of_stay" placeholder="Fill out Preriod of Stay"  readonly required>
                                        </div>
                                     
                                        <input type="hidden" id="date" name="date" value="">
                                        <input type="hidden" id="check_in_date" name="check_in_date" value="">
                                        <input type="hidden" id="check_out_date" name="check_out_date" value="">
                                        <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                        <button type="button" class="btn btn-light" onclick="location.href='../index.php';">Cancel</button>
                                    </form>
                    </div>
                </div>
                 <div class="col-lg-6 ml-lg-3">
                    <img src="../assets/images/res/Con1.png" class="img-fluid h-100 w-100">
                </div>
            </div>
        </div>
   

  <!-- Add flatpickr library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
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
