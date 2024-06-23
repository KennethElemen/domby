<?php
include '../../includes/config/dbconn.php';

// Create connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Fetch room number, name, check-in, and check-out dates from the database
$sql = "SELECT room_number, name, check_in_date, check_out_date FROM tenantprofile";
$result = $dbConnection->query($sql);

// Prepare events array for Evo Calendar
$evoCalendarEvents = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate the duration in days between check-in and check-out
        $duration = date_diff(new DateTime($row['check_in_date']), new DateTime($row['check_out_date']))->days;

        // Generate events for each day within the duration
        for ($i = 0; $i <= $duration; $i++) {
            $currentDate = date("M d, Y", strtotime($row['check_in_date'] . " +$i days"));

            $evoCalendarEvents[] = [
                'id' => 'event_' . $row['room_number'] . '_' . $i,
                'name' => 'Room Number: ' . $row['room_number'],
                'description' => 'Tenant Name: ' . $row['name'],
                'date' => $currentDate,
                'type' => 'event',
            ];
        }
    }
}


// Close the database connection
$dbConnection->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Add Evo Calendar stylesheets -->
    <link rel="stylesheet" href="evo-calendar.min.css">
    <link rel="stylesheet" href="evo-calendar.css">
    
    <!-- Add FullCalendar library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css" />
   
</head>
<body>

    <div class="container-scroller">
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
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="mb-12">Event Calendar</h1>
                                    <ul class="list-arrow">
                                              <li>All the red dot show that there is a tenant in that day</li>
                                            </ul>
                                    <div class="hero">
                                        <div id="calendar" class="calendar"></div>
                                    </div>
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

            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->

    <!-- Include necessary scripts -->
    <?php include '../scripts.php'; ?>

   
</body>

</html>
