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

function sendNotifAdmin($loggedInEmail,$email) {
    
    // Your email content and sending logic here
    $to = $loggedInEmail;
    $subject = 'Reservation Canceled';
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
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Canceled Reservation</h1>
                                    <hr>
                                   
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                     $email, just canceled his/her  reservation
                                    </p>
                                      
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
               return true;
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


function sendCancellationConfirmationEmail($email) {
    // Your email content and sending logic here
    $to = $email;
    $subject = 'Reservation Cancellation Confirmation';
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="Reservation Cancellation Confirmation">
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
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Reservation Cancellation Confirmation</h1>
                                    <hr>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Dear guest,
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Your reservation has been successfully cancelled.
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        We hope to see you again soon.
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
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
               return true;
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

// Check if the POST request is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the session
    $email = $_SESSION['submitted_email'] ?? '';

    // Check if the email is set in the session
    if (empty($email)) {
        // Redirect with an error message if email is empty
        error_log('Email is empty in session');
        echo "Email is empty in session"; // Add debugging statement
        exit();
    }

    // Include database connection
    include '../includes/config/dbconn.php';
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check database connection
    if ($conn->connect_error) {
        // Log the connection error for debugging
        error_log('Connection failed: ' . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Send cancellation email
        if (sendCancellationConfirmationEmail($email) && sendNotifAdmin($loggedInEmail,$email)) {
            // Delete data associated with the email
            $deleteDataQuery = "DELETE FROM reservations WHERE email = ?";
            $stmt = $conn->prepare($deleteDataQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                // Commit the transaction
                $conn->commit();
                echo "Reservation cancelled successfully. Cancellation email sent.";

                // Redirect with success message
                header('Location: ../errorpage/cancelationSuccess.php');
                exit();
            } else {
                // Rollback the transaction if no rows were affected
                $conn->rollback();
                // Log the delete failure for debugging
                error_log('Delete failed. No rows were affected.');
                echo "Delete failed. No rows were affected."; // Add debugging statement
                  header('Location: ../errorpage/cancelationUnSuccess.php');
                exit();
            }
        } else {
            echo "Failed to send cancellation email.";
              header('Location: ../errorpage/cancelationUnSuccess.php');
            exit();
        }
    } catch (Exception $e) {
        // Handle exceptions and rollback the transaction
        $conn->rollback();
        // Log the exception for debugging
        error_log('Error: ' . $e->getMessage());
        echo "Error: " . $e->getMessage(); // Output the error message for debugging
        exit();
    } finally {
        $conn->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cancellation</title>
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
        <div class="row flex-grow">
            <div class="col-lg-3 offset-lg-2 d-flex align-items-center">
                <div class="auth-form-light text-left p-4">
                    <h2 class="display-7" style="color: black;">Cancellation Confirmation</h2>
                    <div class="mt-3">
                        <p class="mb-1">Dear Traveler,</p>
                        <p>You are about to cancel your reservation for <strong><?php echo $_SESSION['submitted_email'] ?? ''; ?>.</strong></p>
                    </div>
                    <hr>
                    <form class="pt-3" method="post" action="">
                        <div class="form-group">
                            <p class="mb-3">Are you sure you want to proceed?</p>
                            <p class="text-muted">Please note that canceling your reservation may result in the loss of any reserved accommodations or associated benefits.</p>
                            <button type="submit" class="btn btn-danger">Yes, Cancel Reservation</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex justify-content-center">
                    <img src="../assets/images/res/Con1.png" class="img-fluid">
                </div>
            </div>
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

                // Calculate the number of days, weeks, months, and years
                const startDate = new Date(selectedDates[0]);
                const endDate = new Date(selectedDates[1]);
                const timeDiff = Math.abs(endDate.getTime() - startDate.getTime());

                // Calculate days
                const days = Math.ceil(timeDiff / (1000 * 3600 * 24));

                // Calculate weeks
                const weeks = Math.ceil(days / 7);

                // Calculate months
                const months = Math.ceil(days / 30); // Assuming a month is 30 days

                // Calculate years
                const years = Math.ceil(days / 365); // Assuming a year is 365 days

                // Update the number_of_days field
                const durationText = getDurationText(days, weeks, months, years);
                document.getElementById('duration').value = durationText;

                // Determine the type_of_stay based on the number of days
                const typeOfStay = (days <= 30) ? "Transient" : ((days > 31) ? "Long-term" : "Custom Type");
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

     // Function to handle success message display
    function showSuccessToast(message) {
        swal({
            title: 'Success!',
            text: message || 'Operation completed successfully.',
            icon: 'success',
            timer: 10000, // Display the message for 5 seconds
        });
    }
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
