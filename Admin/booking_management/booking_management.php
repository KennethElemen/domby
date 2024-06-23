<?php

// Include necessary files
include '../../includes/function/reservation_status.php';
include '../../includes/config/dbconn.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ReservationID']) && isset($_POST['status'])) {
    $ReservationID = $_POST['ReservationID'];
    $newStatus = $_POST['status'];

    // Update reservation status
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    $updateSql = "UPDATE reservations SET status = '$newStatus' WHERE ReservationID = $ReservationID";

    if ($dbConnection->query($updateSql) === TRUE) {
        // Check if status is 'Rejected'
        if ($newStatus === 'Rejected') {
            // Get reservation details
            $reservationDetails = getReservationDetails($ReservationID);

            // Send email to the guest
            sendRejectedEmail($reservationDetails['email']);

        }
    }
    $dbConnection->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../head.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Additional meta tags, stylesheets, and scripts can be added here -->
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
                                    <h1 class="mb-12">Booking Management<ul class="list-arrow">
                                              <li>Show the list of booking inquiries</li>
                                            </ul></h1>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Room No.</th>
                                                    <th>Email</th>
                                                    <th>Contact No.</th>
                                                    <th>Check-in Date</th>
                                                    <th>Check-out Date</th>
                                                    <th>Type of Stay</th>
                                                    <th>Duration</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               <?php
                                                $dbConnection = new mysqli($servername, $username, $password, $dbname);

                                                if ($dbConnection->connect_error) {
                                                    die("Connection failed: " . $dbConnection->connect_error);
                                                }

                                                $result = $dbConnection->query("SELECT * FROM reservations");

                                                while ($row = $result->fetch_assoc()) {
                                                    if ($row['status']) {
                                                        continue; // Skip displaying rows with status
                                                    }

                                                    echo "<tr>";
                                                    echo "<td>" . date('F j, Y', strtotime($row['date'])) . "</td>";
                                                    echo "<td>" . $row['room_number'] . "</td>";
                                                    echo "<td>" . $row['email'] . "</td>";
                                                    echo "<td>" . $row['contact_number'] . "</td>";
                                                    echo "<td>" . date('F j, Y', strtotime($row['check_in_date'])) . "</td>";
                                                    echo "<td>" . date('F j, Y', strtotime($row['check_out_date'])) . "</td>";
                                                    echo "<td>" . $row['type_of_stay'] . "</td>";
                                                    echo "<td>" . $row['duration'] . "</td>";
                                                   // Replace this line
                                                    echo "<td>" . $row['status'] ;

                                                    
                                                    echo '<button type="button" class="btn btn-success btn-sm mr-2" onclick="updateStatus(' . $row['ReservationID'] . ', \'Accepted\')">Accept</button>';
                                                    echo '<span class="mr-2"></span>';
                                                    echo '<button type="button" class="btn btn-danger btn-sm" onclick="updateStatus(' . $row['ReservationID'] . ', \'Rejected\')">Reject</button>';
                                                    echo '</td>';

                                                    echo "</tr>";
                                                }

                                                $dbConnection->close();
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
            </div>
             </div>
        </div>
       
        <?php include '../footer.php'; ?>
    </div>
    <?php include '../scripts.php'; ?>

   <script>
    function updateStatus(ReservationID, newStatus) {
        if (newStatus === 'Rejected') {
            swal({
                title: 'Are you sure?',
                text: 'You are about to reject the reservation.',
                icon: 'warning',
                buttons: ["Cancel", "Reject"],
                dangerMode: true,
            }).then(function (willReject) {
                if (willReject) {
                    // If the user clicked "Reject," update the status and remove the reservation
                    performUpdateAndRemove(ReservationID, newStatus);
                }
            });
        } else {
            // For other statuses (e.g., 'Accepted'), directly update the status
            performUpdateAndRemove(ReservationID, newStatus);
        }
    }

    function performUpdateAndRemove(ReservationID, newStatus) {
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: {
                ReservationID: ReservationID,
                status: newStatus
            },
            success: function (response) {
                swal({
                    title: (newStatus === 'Accepted') ? 'Accepted!' : 'Rejected!',
                    text: (newStatus === 'Accepted') ? 'Reservation has been accepted.' : 'Reservation has been rejected.',
                    icon: (newStatus === 'Accepted') ? 'success' : 'error',
                }).then(function () {
                    location.reload(); // Reload the page after updating status
                });
            },
            error: function (error) {
                console.error(error);
            }
        });
    }
</script>

</body>
</html>
