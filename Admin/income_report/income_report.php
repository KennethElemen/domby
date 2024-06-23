<?php
// main.php

// includes/config/db.php
include '../../includes/config/dbconn.php';


// Function to create a database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}


function getMonthlyIncomeData($conn) {
    $paymentTableName = 'payment';

    $sequenceSql = "SELECT DISTINCT DATE_FORMAT(Date, '%Y-%m') AS MonthYear
                    FROM $paymentTableName";

    $sql = "SELECT sequence.MonthYear, COALESCE(SUM($paymentTableName.Amount), 0) AS TotalAmount
            FROM ($sequenceSql) AS sequence
            LEFT JOIN $paymentTableName ON sequence.MonthYear = DATE_FORMAT($paymentTableName.Date, '%Y-%m') AND $paymentTableName.Status = 'Completed'
            GROUP BY sequence.MonthYear
            ORDER BY sequence.MonthYear";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $monthlyIncomeData = array();
        while ($row = $result->fetch_assoc()) {
            $monthlyIncomeData[] = array(
                'year' => date('Y', strtotime($row['MonthYear'])), // Include the year in the data
                'date' => date('F Y', strtotime($row['MonthYear'])),
                'amount' => $row['TotalAmount']
            );
        }
        return $monthlyIncomeData;
    } else {
        return array();
    }
}

// Call the function to create a database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Call the function to get monthly income data
$incomeData = getMonthlyIncomeData($conn);

// Close the database connection
$dbConnection->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>

<body>
    <div class="container-scroller">
        <?php include '../topbar.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h1 class="mb-12">Income Report</h1>
                                    <hr>
                                    <canvas id="yearCharts" width="80%" height="25%"></canvas> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include '../footer.php'; ?>
            </div>
        </div>
    </div>

    <?php
    // Include necessary scripts, including Chart.js
    include '../scripts.php';
    ?>

   
</body>

</html>