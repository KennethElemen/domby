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

// Initialize variables
$reportPeriod = isset($_GET['reportPeriod']) ? $_GET['reportPeriod'] : 'yearly';
$roomIncomeData = array(); // Initialize as an empty array

if (isset($_GET['action']) && $_GET['action'] == 'print') {
    // Get income details per room including date based on the selected report period
    $reportPeriod = isset($_GET['reportPeriod']) ? $_GET['reportPeriod'] : 'monthly';
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

    $sql = "";
    switch ($reportPeriod) {
        case 'monthly':
            $sql = "SELECT RoomNumber, DATE_FORMAT(Date, '%M %Y') AS MonthYear, SUM(Amount) AS RoomTotal
                FROM $paymentTableName
                WHERE Status = 'Completed'
                GROUP BY RoomNumber, MonthYear
                ORDER BY RoomNumber, MonthYear";
            break;
        case 'yearly':
            $sql = "SELECT YEAR(Date) AS Year, RoomNumber, SUM(Amount) AS TotalByYear
                FROM $paymentTableName
                WHERE Status = 'Completed'
                GROUP BY Year, RoomNumber
                ORDER BY Year, RoomNumber";
            break;
        default:
            // Handle other report periods if needed
            break;
    }

    $result = $conn->query($sql);

    $roomIncomeData = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($reportPeriod === 'monthly') {
                $roomIncomeData[] = array(
                    'roomNumber' => $row['RoomNumber'],
                    'date' => isset($row['MonthYear']) ? $row['MonthYear'] : '',
                    'roomTotal' => $row['RoomTotal']
                );
            } elseif ($reportPeriod === 'yearly') {
                // For yearly report, store the total directly without grouping by room
                $roomIncomeData[] = array(
                    'year' => $row['Year'],
                    'roomNumber' => $row['RoomNumber'],
                    'totalByYear' => $row['TotalByYear']
                );
            }
        }
    }
    return $roomIncomeData;
}

// Function to generate PDF content
function generatePDF($roomIncomeData, $reportPeriod) {
    if (!empty($roomIncomeData)) {
        // Your PDF generation code here
    } else {
        // Handle case where no data is available
        echo "No data available.";
        exit;
    }
}

// Function to generate Excel content
function generateExcel($roomIncomeData, $reportPeriod) {
    if (!empty($roomIncomeData)) {
        // Your Excel generation code here
    } else {
        // Handle case where no data is available
        echo "No data available.";
        exit;
    }
}

// Get room income data
$roomIncomeData = getRoomIncomeData($dbConnection, $reportPeriod);

// Calculate total payments by year and total rooms by year
$totalPaymentsByYear = array(); // Initialize an array to store total payments by year
$totalRoomsByYear = array(); // Initialize an array to store total rooms by year
foreach ($roomIncomeData as $income) {
    $year = $income['year'];
    $roomNumber = $income['roomNumber'];
    $total = $income['totalByYear'];
    // If the year already exists in the array, add the total to the existing value
    if (isset($totalPaymentsByYear[$year])) {
        $totalPaymentsByYear[$year] += $total;
    } else {
        $totalPaymentsByYear[$year] = $total;
    }
    // If the year already exists in the array, add the room number to the existing value
    if (!isset($totalRoomsByYear[$year][$roomNumber])) {
        $totalRoomsByYear[$year][$roomNumber] = 1;
    } else {
        $totalRoomsByYear[$year][$roomNumber]++;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<head>
    <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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
                                    <h4 class="card-title mb-4">Income Report</h4>

                                    <div class="mb-4">
                                        <label for="dormName" class="font-weight-bold">DormBell</label>
                                    </div>

                                    <div class="mb-4">
                                        <span class="text"><?php echo date('F j, Y'); ?></span>
                                    </div>

                                    <div class="mb-4 col-auto text-left">
                                        <label for="reportPeriod" class="font-weight-bold">Report Period:</label>
                                        <div class="col-auto">
                                            <select class="form-control form-control-sm w-auto" id="reportPeriod" onchange="updateTableFormat()">
                                                <option value="yearly" <?php echo ($reportPeriod == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                   

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="incomeTable">
                                            <thead>
                                                <tr>
                                                    <?php if ($reportPeriod == 'yearly') : ?>
                                                        <th id="dateHeader">Year</th>
                                                    <?php endif; ?>
                                                    <th>Room Number</th>
                                                     <th>Total Rooms</th>
                                                    <th>Total Payments</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($totalRoomsByYear as $year => $rooms) : ?>
                                                    <tr>
                                                        <td><?php echo $year; ?></td>
                                                        <td><?php echo implode(', ', array_keys($rooms)); ?></td>
                                                         <td><?php echo array_sum($rooms); ?></td>
                                                        <td>PHP <?php echo $totalPaymentsByYear[$year]; ?></td>
                                                       
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <p class="mt-3 font-weight-bold text-right">Total Amount: PHP <?php echo array_sum(array_column($roomIncomeData, 'totalByYear')); ?></p>
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
        function redirectToYear() {
            // Replace 'path-to-year' with the actual path you want to redirect to
            window.location.href = 'preview_income_report.php';
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
            var reportPeriod = document.getElementById('reportPeriod').value;
            var dateHeader = document.getElementById('dateHeader');
            var dateColumn = document.getElementById('dateColumn');

            if (reportPeriod === 'monthly') {
                dateHeader.innerText = 'Month';
                dateColumn.innerText = 'Month';
            } else if (reportPeriod === 'yearly') {
                dateHeader.innerText = 'Year';
                dateColumn.innerText = 'Year';
            }

            // Fetch and update table data based on the selected report period
            fetch('preview_income_report.php?action=print&reportPeriod=' + reportPeriod + '&output=pdf')
                .then(response => response.json())
                .then(data => {
                    var roomIncomeData = data.roomIncomeData;

                    // Update table data
                    var tableBody = document.getElementById('incomeTable').getElementsByTagName('tbody')[0];
                    tableBody.innerHTML = ''; // Clear existing table data

                    roomIncomeData.forEach(roomData => {
                        var row = tableBody.insertRow();
                        var cellDate = row.insertCell(0);
                        var cellRoomNumber = row.insertCell(1);
                        var cellRoomTotal = row.insertCell(2);

                        // Display the month or year based on the report period
                        if (reportPeriod === 'monthly') {
                            cellDate.innerText = roomData.date;
                        } else if (reportPeriod === 'yearly') {
                            cellDate.innerText = roomData.year; // Assuming the server sends only the year
                        }

                        cellRoomNumber.innerText = roomData.roomNumber;
                        cellRoomTotal.innerText = roomData.roomTotal;
                    });
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        function generateFile(outputType) {
            // Define the action and output type
            var action = 'preview_income_report.php?action=print&reportPeriod=' + document.getElementById('reportPeriod').value + '&output=' + outputType;

            // Redirect to the action URL
            window.location.href = action;
        }

       function printPdf() {
    var dormName = document.querySelector('.font-weight-bold').innerText.trim();
    var currentDate = new Date().toLocaleString();
    var reportPeriod = document.getElementById('reportPeriod').value;

    // Fetch the total amount and room income data
    fetch('preview_income_report.php?action=print&reportPeriod=' + reportPeriod + '&output=pdf')
        .then(response => response.json())
        .then(data => {
            var totalAmount = data.totalAmount;
            var roomIncomeData = data.roomIncomeData;

            var contentToPrint = document.createElement('div');
            // Add dorm name, date, and report period to the content with center alignment
            contentToPrint.innerHTML += '<h4 style="text-align:center;">Dorm Bell Income Report</h4>';
            contentToPrint.innerHTML += '<p style="text-align:center;">Generated on: ' + currentDate + '</p>';
            contentToPrint.innerHTML += '<p style="text-align:center;">Report Period: ' + reportPeriod + '</p>';

            // Add the table to the content with improved styling
            var tableHtml = '<table style="width:100%; border-collapse: collapse; margin-top:10px;">';

            // Add the relevant headers based on the report period
            tableHtml += '<thead style="background-color:#f2f2f2;"><tr>';
            if (reportPeriod === 'monthly') {
                tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Month</th>';
            } else if (reportPeriod === 'yearly') {
                tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Year</th>';
            }
            tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Room Number</th>';
            tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align:center;">Amount per Room</th>';
            tableHtml += '</tr></thead><tbody>';

            // Add data rows
            roomIncomeData.forEach(roomData => {
                tableHtml += '<tr style="background-color:' + (roomData.roomNumber % 2 === 0 ? '#f9f9f9' : '#ffffff') + ';">';

                // Add the relevant date information based on the report period
                if (reportPeriod === 'monthly') {
                    tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.date + '</td>';
                } else if (reportPeriod === 'yearly') {
                    tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.year + '</td>';
                }

                tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.roomNumber + '</td>';
                tableHtml += '<td style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + roomData.roomTotal + '</td>';
                tableHtml += '</tr>';
            });

            // Add the total amount row to the table
            tableHtml += '<tr>';
            tableHtml += '<td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align:center;"></td>'; // Empty cells for date or year
            tableHtml += '<td class="font-weight-bold" style="border: 1px solid #ddd; padding: 8px; text-align:center;">Total Amount</td>';
            tableHtml += '<td class="font-weight-bold" style="border: 1px solid #ddd; padding: 8px; text-align:center;">' + totalAmount + '</td>';
            tableHtml += '</tr>';

            tableHtml += '</tbody></table>';
            contentToPrint.innerHTML += tableHtml;

            // Create a new window for printing
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Dorm Bell Income Report</title></head><body>');
            printWindow.document.write(contentToPrint.innerHTML);
            printWindow.document.write('</body></html>');

            // Print the document
            printWindow.document.close(); // Necessary for IE >= 10
            printWindow.focus(); // Necessary for IE >= 10
            printWindow.print();
            printWindow.close();
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}


    </script>

</body>

</html>
