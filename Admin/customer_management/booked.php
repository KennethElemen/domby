<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../head.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Additional meta tags, stylesheets, and scripts can be added here -->
    <style>
        .completed {
            color: green;
        }
        .not-yet {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <?php include '../topbar.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                    </div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h1 class="">List of all tenants
                                        <ul class="list-arrow">
                                            <li>If a tenant is accepted but hasn't paid the downpayment or set up their profile, you can remove them from this list.</li>
                                        </ul>
                                    </h1>
                                    <div class="table-responsive">
                                        <?php
                                        include '../../includes/config/dbconn.php';
                                        $dbConnection = new mysqli($servername, $username, $password, $dbname);
                                        if ($dbConnection->connect_error) {
                                            die("Connection failed: " . $dbConnection->connect_error);
                                        }
                                        $query = "SELECT r.*, p.PaymentType 
                                                FROM reservations r 
                                                LEFT JOIN payment p ON r.email = p.emailID AND p.PaymentType = 'Down Payment' 
                                                WHERE r.status = 'Accepted'";
                                        $result = $dbConnection->query($query);
                                        if ($result) {
                                            ?>
                                            <table class="table table-striped" id="example1">
                                                <thead>
                                                    <tr>
                                                        <th>Room No.</th>
                                                        <th>Full Name</th>
                                                        <th>Email</th>
                                                        <th>Contact No.</th>
                                                        <th>Check-in Date</th>
                                                        <th>Check-out Date</th>
                                                        <th>Downpayment</th>
                                                        <th>Profiling Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                 <?php
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $row['room_number'] . "</td>";
                                                        echo "<td>" . $row['Name'] . "</td>";
                                                        echo "<td>" . $row['email'] . "</td>";
                                                        echo "<td>" . $row['contact_number'] . "</td>";
                                                        echo "<td>" . $row['check_in_date'] . "</td>";
                                                        echo "<td>" . $row['check_out_date'] . "</td>";
                                                    
                                                        // Check if profiling completed
                                                        $email = $row['email'];
                                                        $checkProfileQuery = "SELECT * FROM tenantprofile WHERE email = '$email'";
                                                        $profileResult = $dbConnection->query($checkProfileQuery);
                                                        $profileCompleted = ($profileResult && $profileResult->num_rows > 0);
                                                    
                                                        // Check if down payment completed
                                                        $paymentCompleted = ($row['PaymentType'] ? true : false);
                                                    
                                                        echo "<td class='" . ($paymentCompleted ? "completed" : "not-yet") . "'>" . ($paymentCompleted ? "Completed" : "Not Yet") . "</td>";
                                                        echo "<td class='" . ($profileCompleted ? "completed" : "not-yet") . "'>" . ($profileCompleted ? "Completed" : "Not Yet") . "</td>";
                                                    
                                                        echo "<td>";
                                                        if (!$paymentCompleted || !$profileCompleted) {
                                                            // Display remove button if payment or profile is not completed
                                                            echo '<button type="button" class="btn btn-danger btn-sm mr-2" onclick="removeReservation(' . $row['ReservationID'] . ')">Remove</button>';
                                                        }
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                    $dbConnection->close();
                                                    ?>

                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../scripts.php'; ?>

    <script>
        function removeReservation(ReservationID) {
            swal({
                title: 'Are you sure?',
                text: 'You are about to remove the reservation.',
                icon: 'warning',
                buttons: ["Cancel", "Remove"],
                dangerMode: true,
            }).then(function (willRemove) {
                if (willRemove) {
                    $.ajax({
                        type: 'POST',
                        url: window.location.href,
                        data: {
                            removeReservation: true,
                            ReservationID: ReservationID
                        },
                        success: function (response) {
                            location.reload();
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                }
            });
        }
    </script>
<?php
function sendEmail($userEmail) {
    global $dbConnection;
    // If email has not been sent today, proceed with sending the email
    $subject = 'Notice: Your Reservation is Being Cancelled';

    // Construct email message
    $message = <<<HTML
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="Cancellation Notice">
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
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Cancellation Notice</h1>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        Hello, $userEmail
                                    </p>
                                    <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                        We regret to inform you that your reservation is being cancelled due to failure to pay the downpayment within the given time frame.
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

    $headers = 'From: DormBell' . "\r\n" .
               'X-Mailer: PHP/' . phpversion() . "\r\n" .
               'MIME-Version: 1.0' . "\r\n" .
               'Content-Type: text/html; charset=utf-8';

    // Send email
    if (mail($userEmail, $subject, $message, $headers)) {
        return true; // Email sent successfully
    } else {
        return false; // Failed to send email
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['removeReservation'])) {
    $ReservationID = $_POST['ReservationID'];
    $dbConnection = new mysqli($servername, $username, $password, $dbname);
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }
    // Fetch the email of the tenant before deletion
    $getEmailSql = "SELECT email FROM reservations WHERE ReservationID = $ReservationID";
    $emailResult = $dbConnection->query($getEmailSql);
    if ($emailResult->num_rows > 0) {
        // Fetch the email address of the tenant
        $row = $emailResult->fetch_assoc();
        $tenantEmail = $row["email"];

        // Deleting reservation and related tenant records
        $deleteReservationSql = "DELETE reservations, tenants
                                  FROM reservations 
                                  LEFT JOIN tenants ON reservations.email = tenants.EmailID
                                  WHERE reservations.ReservationID = $ReservationID";
        if ($dbConnection->query($deleteReservationSql) === TRUE) {
            // Send email to the tenant
            sendEmail($tenantEmail);
            if (sendEmail($tenantEmail)) {
                echo "<script>alert('Reservation and related records removed successfully. Email sent to $tenantEmail.');</script>";
            } else {
                echo "<script>alert('Reservation and related records removed successfully, but failed to send email to $tenantEmail.');</script>";
            }
        } else {
            echo "Error removing reservation and related records: " . $dbConnection->error;
        }
    } else {
        echo "Error: Tenant email not found.";
    }
    $dbConnection->close();
}
?>



</body>
</html>
