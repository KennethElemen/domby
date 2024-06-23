<?php
include '../../includes/config/dbconn.php';
include '../../includes/function/dashboardFunction.php';

$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Get tenant counts by status
$tenantCounts = getTenantCountByStatus($servername, $username, $password, $dbname);
$totalRegularTenants = $tenantCounts['Long-term'];
$totalTransientTenants = $tenantCounts['Transient'];

// Get the room count
$totalRooms = getRoomCount($servername, $username, $password, $dbname);

// Get the room type count
$totalRoomTypes = getRoomTypeCount($servername, $username, $password, $dbname);

// Get the tenant count
$totalAcceptedReservations =  getAcceptedReservationCount($servername, $username, $password, $dbname);

// Get the count of pending payments
$totalPendingPayments = getPendingPaymentsCount($servername, $username, $password, $dbname);

// Get the count of bookings without status
$totalUnconfirmedBookings = getUnconfirmedBookingsCount($servername, $username, $password, $dbname);
$monthNames = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

// Get the number of inquiries by month
$inquiriesByMonth = getInquiriesByMonth($servername, $username, $password, $dbname);

$totalOccupants = getTotalOccupantsCount($servername, $username, $password, $dbname);
$currentDate = date('l, F j, Y');

// Fill in missing months with zero inquiries and align data with correct month order
$inquiriesData = array_map(function($month) use ($inquiriesByMonth) {
    return isset($inquiriesByMonth[$month]) ? $inquiriesByMonth[$month] : 0;
}, $monthNames);

// Data for the bar graph
$inquiriesData = [
    'labels' => $monthNames,
    'datasets' => [
        [
            'label' => 'Number of Inquiries',
            'data' => array_values($inquiriesData), // Align data with correct month order
            'backgroundColor' => 'rgba(167, 66, 214, 0.5)',
            'borderColor' => 'rgba(167, 66, 214, 1)',
            'borderWidth' => 1
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<head>
    <style>
        .custom-icon-lg {
        font-size: 100px; /* Adjust the size as needed */
}

    </style>
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
            <div class="main-panel ">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h1 class="mb-12"> Dashboard </h1>
                    </div>
                    
                    
                    <div class="row">
                        <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">
                                <div class="card-body text-purple">
                                    <h2 class="mb-12">Number of Room</h2>
                                    <h3 class="card-text"><i class="mdi mdi-view-carousel icon-lg" style="color: #a742d6;"></i><?php echo $totalRooms; ?> </h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">
                                <div class="card-body text-purple">
                                    <h2 class="mb-12">Number of Tenants</h2>
                                    <h3 class="card-text"><i class="mdi mdi-account icon-lg" style="color: #a742d6;"></i> <?php echo $totalAcceptedReservations ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">    
                               <div class="card bg-purple ">
                                <div class="card-body">
                                    <h2 class="mb-12">Tenants Type</h2>
                                    
                                     <h5 class="card-text"> <span style="color: #a742d6;">Long-Term: </span> <span class="page-title"><?php echo $totalRegularTenants; ?></span></h3>
                                    <h5 class="card-text"> <span style="color: #a742d6;">Transient: </span> <span class="page-title"><?php echo $totalTransientTenants; ?></span></h3>
                                </div>
                            </div>  
                            </div>
                        </div>

                        
                       <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">
                                <div class="card-body text-purple">
                                    <h3 class="mb-12">Pending Payments</h3>
                                    <ul class="list-arrow">
                                        <li>as of <?php echo $currentDate; ?></li>
                                    </ul>
                                    <h2 class="card-text"><i class="mdi mdi-ticket-confirmation custom-icon-lg" style="color: #a742d6;"></i> <?php echo $totalPendingPayments; ?></h2>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">
                                <div class="card-body text-purple">
                                    <h3 class="mb-12">Inquiries</h3>
                                    <ul class="list-arrow">
                                      <li>As of <?php echo date('F j, Y'); ?></li>
                                    </ul>
                                    <h2 class="card-text"><i class="mdi mdi-calendar-remove custom-icon-lg" style="color: #a742d6;"></i> <?php echo $totalUnconfirmedBookings; ?></h2>
                                </div>
                            </div>
                        </div>


                        
                         <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">
                                <div class="card-body">
                                    <h3 class="mb-12">Dorm Occupancy</h3>
                                    <ul class="list-arrow">
                                        <li>This graph shows the capacity of the dormitory over time.</li>
                                    </ul>
                                    <canvas id="roomCapacityChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-9 grid-margin stretch-card">
                            <div class="card shadow p-3 mb-5 bg-white rounded border-0">
                                <div class="card-body">
                                    <h2 class="mb-12">Number of Inquiries by Month</h2>
                                    <ul class="list-arrow">
                                       <li>This chart highlights which month has the highest number of reservations.</li>
                                    </ul>
                                    <canvas id="inquiriesChart"></canvas>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:../../partials/_footer.html -->
                <?php include '../footer.php'; ?>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php include '../scripts.php'; ?>
  
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

 <script>
        // Data for the bar graph
        var inquiriesData = <?php echo json_encode($inquiriesData); ?>;

        // Bar graph configuration
        var inquiriesConfig = {
            type: 'bar',
            data: inquiriesData,
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        };

        // Get the canvas element
        var inquiriesCanvas = document.getElementById('inquiriesChart').getContext('2d');

        // Create the bar graph
        var inquiriesChart = new Chart(inquiriesCanvas, inquiriesConfig);
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

<!-- Script for Donut Graph -->
<script>
    <?php
    // Call the function to get total occupants count
    $occupantsData = getTotalOccupantsCount($servername, $username, $password, $dbname);
    
    // Extract relevant data
    $totalActualOccupants = $occupantsData['total_actual_occupants'];
    $totalMaxOccupants = $occupantsData['total_max_occupants'];
    ?>

    // Data for the donut graph
    var roomCapacityData = {
        labels: ['Occupied', 'Available'],
        datasets: [{
            data: [<?php echo $totalActualOccupants; ?>, <?php echo ($totalMaxOccupants - $totalActualOccupants); ?>],
            backgroundColor: ['#cf4dfa ', '#04c299 '], // Adjust colors as needed
            hoverBackgroundColor: ['#cf4dfa ', '#04c299 ']
        }]
    };

    // Donut graph configuration
    var roomCapacityConfig = {
        type: 'doughnut',
        data: roomCapacityData,
        options: {
            responsive: true,
            legend: {
                position: 'bottom',
            },
            title: {
                display: false,
                text: 'Room Capacity'
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };

    // Get the canvas element
    var roomCapacityCanvas = document.getElementById('roomCapacityChart').getContext('2d');

    // Create the donut graph
    var roomCapacityChart = new Chart(roomCapacityCanvas, roomCapacityConfig);
</script>

</body>

</html>
