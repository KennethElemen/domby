<?php

// includes/config/db.php
date_default_timezone_set('Asia/Manila');

include '../../includes/config/dbconn.php';

// Create connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] == 'print') {
    // Get income details per room including date on a yearly basis
    $reportPeriod = 'yearly'; // Only yearly is supported in this version
    $roomIncomeData = getRoomIncomeData($dbConnection, $reportPeriod);

    // Handle PDF generation and printing
    if ($_GET['output'] == 'pdf') {
        generatePDF($roomIncomeData, $reportPeriod);
    } elseif ($_GET['output'] == 'excel') {
        generateExcel($roomIncomeData, $reportPeriod);
    }

    exit;
}

// Function to get income details per room including date
function getRoomIncomeData($conn, $reportPeriod) {
    $paymentTableName = 'payment';

    // Query to get room numbers, months, and total amounts for each room
    $sql = "SELECT DATE_FORMAT(Date, '%M %Y') AS MonthYear, GROUP_CONCAT(DISTINCT RoomNumber) AS RoomNumbers, SUM(Amount) AS RoomTotal
            FROM $paymentTableName
            WHERE Status = 'Completed'
            GROUP BY MonthYear
            ORDER BY STR_TO_DATE(CONCAT('01 ', MonthYear), '%d %M %Y') ASC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $roomIncomeData = array();

        while ($row = $result->fetch_assoc()) {
            $roomNumbers = isset($row['RoomNumbers']) ? $row['RoomNumbers'] : '';
            $monthYear = isset($row['MonthYear']) ? $row['MonthYear'] : '';
            $roomTotal = $row['RoomTotal'];

            $roomIncomeData[] = array(
                'roomNumbers' => $roomNumbers,
                'date' => $monthYear,
                'roomTotal' => $roomTotal
            );
        }

        return $roomIncomeData;
    } else {
        return array();
    }
}

function getAvailableYears($conn) {
    $paymentTableName = 'payment';

    // Query to get distinct years with payments
    $sql = "SELECT DISTINCT YEAR(Date) AS PaymentYear FROM $paymentTableName WHERE Status = 'Completed' ORDER BY PaymentYear DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $availableYears = array();
        while ($row = $result->fetch_assoc()) {
            $availableYears[] = $row['PaymentYear'];
        }
        return $availableYears;
    } else {
        return array();
    }
}

// Get available years with payments
$availableYears = getAvailableYears($dbConnection);
// Function to generate PDF content
function generatePDF($roomIncomeData, $reportPeriod) {
    $pdfData = [
        'totalAmount' => array_sum(array_column($roomIncomeData, 'roomTotal')),
        'roomIncomeData' => $roomIncomeData,
    ];

    header('Content-Type: application/json');
    echo json_encode($pdfData);
    exit;
}
// Get admin email
$adminEmail = getAdminEmail($dbConnection);

// Get admin email function
function getAdminEmail($conn) {
    $adminsTableName = 'admins';

    $sql = "SELECT Email FROM $adminsTableName LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Email'];
    } else {
        return null; // Handle the case where no admin email is found
    }
}
// Function to generate Excel content
function generateExcel($roomIncomeData, $reportPeriod) {
    // Create a string with the Excel content
    $excelContent = '';

    // Excel header
    $excelContent .= "DormBell Income Report\n";
    $excelContent .= "Generated on: " . date('F j, Y') . "\n\n";

    // Add header row
    $excelContent .= "Month&Year\tRoom Number\tAmount per Room\n";

    // Add data rows
    foreach ($roomIncomeData as $roomData) {
        $excelContent .= "{$roomData['date']}\t{$roomData['roomNumbers']}\t{$roomData['roomTotal']}\n";
    }

    // Set the content type header to Excel
    header("Content-type: application/vnd.ms-excel");

    // Set the file name
    header("Content-Disposition: attachment; filename=income_report.xls");

    // Output the Excel content
    echo $excelContent;
}


$reportPeriod = 'yearly'; // Only yearly is supported in this version
$roomIncomeData = getRoomIncomeData($dbConnection, $reportPeriod);

// Close the database connection
$dbConnection->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<head>
    <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        #selectYear {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            width: 200px; /* Adjust width as needed */
        }

        #selectYear option {
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            background-color: #f0f0f0;
            cursor: pointer;
        }

        #selectYear option:hover {
            background-color: #e0e0e0;
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
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title mb-4">Yearly Income Report</h4>

                                    <div class="mb-4">
                                        <label for="dormName" class="font-weight-bold">DormBell</label>
                                    </div>

                                    <div class="mb-4">
                                        <span class="text"><?php echo date('F j, Y'); ?></span>
                                    </div>

                                    <div class="mb-4 col-auto text-left" style="display: none;">
                                        <div class="col-auto">
                                            <select class="form-control form-control-sm w-auto" id="reportPeriod" onchange="updateTableFormat()">
                                                <option value="yearly" <?php echo ($reportPeriod == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4 col-auto text-left">
                                    <label for="selectYear">Select Year:</label>
                                    <select class="form-control form-control-sm w-auto" id="availableyear">
                                    <option value="" disabled>Select Year</option>
                                    <?php
                                    $currentYear = date('Y');
                                    foreach ($availableYears as $year) :
                                        $selected = ($year == $currentYear) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $year; ?>" <?php echo $selected; ?>><?php echo $year; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                    </div>
                                    <div class="mb-4 col-auto text-right">
                                       
                                        <button type="button" class="btn btn-primary btn-icon-text" onclick="printPdf()"> Print <i class="mdi mdi-printer btn-icon-append"></i></button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="incomeTable">
                                            <thead>
                                                <tr>
                                                    <th id="dateHeader" class=" font-weight-bold">Month and Year</th>
                                                    <th class=" font-weight-bold" >Room Number</th>
                                                    <th class=" font-weight-bold" >Amount Per Month</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($roomIncomeData as $roomData) : ?>
                                                    <tr>
                                                        <td id="dateColumn"><?php echo $roomData['date']; ?></td>
                                                        <td><?php echo $roomData['roomNumbers']; ?></td>
                                                        <td><?php echo $roomData['roomTotal']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-5 font-weight-bold display-5">Yearly Income: PHP <?php echo number_format(array_sum(array_column($roomIncomeData, 'roomTotal')), 2); ?></div>

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

    <script>

function redirectToMonthlyReport() {
    window.location.href = 'monthly.php'; 
}

      document.addEventListener('DOMContentLoaded', function () {
    // Initial setup
    updateTableFormat();

    // Add event listener for changes in the report period dropdown
    document.getElementById('reportPeriod').addEventListener('change', function () {
        updateTableFormat();
    });
// Add event listener for Ctrl+P
document.addEventListener('keydown', function (event) {
            if (event.ctrlKey && event.key === 'p') {
                printPdf();
                event.preventDefault(); // Prevent the default Ctrl+P behavior (print dialog)
            }
        });
    });

function updateTableFormat() {
    // No need to update table format since it's fixed to yearly
}

function generateFile(outputType) {
    // Define the action and output type
    var action = 'preview_income_report.php?action=print&reportPeriod=yearly&output=' + outputType;

    // Redirect to the action URL
    window.location.href = action;
}

function printPdf() {
    var dormName = document.querySelector('.font-weight-bold').innerText.trim();
    var currentDate = new Date().toLocaleString();
    var reportPeriod = 'yearly'; // Only yearly is supported in this version

    // Fetch the total amount and room income data
    fetch('preview_income_report.php?action=print&reportPeriod=yearly&output=pdf')
        .then(response => response.json())
        .then(data => {
            var totalAmount = data.totalAmount;
            var roomIncomeData = data.roomIncomeData;

            var contentToPrint = document.createElement('div');

            // Add dorm name, date, and report period to the content with center alignment
            contentToPrint.innerHTML += '<img src="../../assets/images/DormBell.png" style="width: 100px; position: absolute; top: 10px; left: 10px;">'; // Add your logo path here
                    // Add dorm name, date, and report period to the content with center alignment
                    contentToPrint.innerHTML += '<h4 style="text-align:center;">Dorm Bell Income Report</h4>';
                    contentToPrint.innerHTML += '<p style="text-align:center;">Generated on: ' + currentDate + '</p>';
                    // Inside the printPdf() function
                    contentToPrint.innerHTML += '<p style="text-align:center;">Generated By: ' + '<?php echo $adminEmail; ?>' + '</p>';
                    // Add the table to the content with improved styling
                    var tableHtml = '<table style="width:100%; border-collapse: collapse; margin-top:10px;">';

                    // Add the relevant headers based on the report period
                    tableHtml += '<thead style="background-color:#f2f2f2;"><tr>';
                    tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Month and Year</th>';
                    tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Room Number</th>';
                    tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Amount per Month</th>';
                    tableHtml += '</tr></thead><tbody>';

                    // Add data rows
                    roomIncomeData.forEach(roomData => {
                        tableHtml += '<tr style="background-color:' + (roomData.roomNumbers % 2 === 0 ? '#f9f9f9' : '#ffffff') + ';">';
                        tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.date + '</td>';
                        tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.roomNumbers + '</td>';
                        tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.roomTotal + '</td>';
                        tableHtml += '</tr>';
            });

            // Add the total amount row to the table
            tableHtml += '<tr>';
                    tableHtml += '<td colspan="2" class="font-weight-bold" style="border: 1px solid #ddd; padding: 8px; text-align:center;"></td>';
                    tableHtml += '<td class="font-weight-bold" style="border: 1px solid #ddd; padding: 8px; text-align:center;" id="totalRoomAmount">Total Room Amount: PHP ' + totalAmount.toFixed(2) + '</td>';
                    tableHtml += '</tr>';

            tableHtml += '</tbody></table>';
            contentToPrint.innerHTML += tableHtml;

      

            var pdfConfig = {
                margin: 10,
                filename: 'DormBell_income_report_DATE' + currentDate + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf(contentToPrint, pdfConfig);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}
    </script>
</body>
</html>
