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
$paymentData = [];
$totalAmount = 0;

// Get the current month and year
$currentMonth = date('n');
$currentYear = date('Y');

// Query to retrieve available months and years with payments
$sql = "SELECT DISTINCT YEAR(Date) AS PaymentYear, MONTH(Date) AS PaymentMonth FROM payment WHERE Status = 'Completed'";
$result = $dbConnection->query($sql);

// Initialize arrays to store available months and years
$availableMonths = [];
$availableYears = [];

// Check if any rows were returned
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availableMonths[$row['PaymentMonth']] = date('F', mktime(0, 0, 0, $row['PaymentMonth'], 1));
        $availableYears[$row['PaymentYear']] = $row['PaymentYear'];
    }
}

// Generate a range of months with payments
function generateMonthRange($availableMonths, $availableYears)
{
    $monthRange = [];
    foreach ($availableYears as $year) {
        foreach ($availableMonths as $monthNum => $monthName) {
            $monthRange[] = [
                'month' => $monthNum,
                'year' => $year,
                'label' => "$monthName $year"
            ];
        }
    }
    return $monthRange;
}

// Get the month range with payments
$monthRange = generateMonthRange($availableMonths, $availableYears);

// Check if selected months are submitted
if (isset($_GET['selectedMonthsYears'])) {
    $selectedMonthsYears = $_GET['selectedMonthsYears'];
    $selectedMonthsYears = explode(',', $selectedMonthsYears); // Split the string into an array

    $conditions = [];
    foreach ($selectedMonthsYears as $selectedMonthYear) {
        $selectedMonthYearParts = explode('-', $selectedMonthYear);
        $selectedMonth = $selectedMonthYearParts[0];
        $selectedYear = $selectedMonthYearParts[1];
        $conditions[] = "(MONTH(Date) = $selectedMonth AND YEAR(Date) = $selectedYear)";
    }

    // Modified SQL query to fetch distinct room numbers for each date
    $sql = "SELECT GROUP_CONCAT(DISTINCT RoomNumber ORDER BY RoomNumber ASC) AS RoomNumbers, Amount, DATE_FORMAT(Date, '%M %e, %Y') AS FormattedDate
        FROM payment
        WHERE Status = 'Completed' AND (" . implode(' OR ', $conditions) . ")
        GROUP BY Date";

    $result = $dbConnection->query($sql);

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $paymentData[] = $row;
            $totalAmount += $row['Amount'];
        }
    }
} else {
    // If no month is selected, fetch records for the current month and year
    $sql = "SELECT GROUP_CONCAT(DISTINCT RoomNumber ORDER BY RoomNumber ASC) AS RoomNumbers, Amount, DATE_FORMAT(Date, '%M %e, %Y') AS FormattedDate
        FROM payment
        WHERE Status = 'Completed' AND MONTH(Date) = $currentMonth AND YEAR(Date) = $currentYear
        GROUP BY Date";

    $result = $dbConnection->query($sql);

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $paymentData[] = $row;
            $totalAmount += $row['Amount'];
        }
    }
}



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
// Close the database connection
$dbConnection->close();
?>


<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<head>
    <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.js"></script>
   

    
<style>
/* Custom styling for checkboxes */
.month-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
}

.form-check {
    display: flex;
    align-items: center;
}

.form-check-input {
    margin-right: 5px;
}

/* Styling for the checkbox label */
.form-check-label {
    cursor: pointer;
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
                                    <h4 class="card-title mb-4">Income Report</h4>

                                    <div class="mb-4">
                                        <label for="dormName" class="font-weight-bold">DormBell</label>
                                    </div>

                                    <div class="mb-4">
                                        <span class="text"><?php echo date('F j, Y'); ?></span>
                                    </div>   
                                    
                                   <div class="mb-4 col-auto text-left">
                                            <label>Select Month</label>
                                            <ul id="monthCheckboxes" class="list-arrow m-12">
                                                <?php
                                                foreach ($monthRange as $range) {
                                                    $checked = ""; // Default unchecked state
                                                    // Check if the month is within the current range selection
                                                    if (isset($_GET['selectedMonthsYears'])) {
                                                        $selectedMonthsYears = explode(',', $_GET['selectedMonthsYears']);
                                                        foreach ($selectedMonthsYears as $selectedMonthYear) {
                                                            if ($selectedMonthYear === "{$range['month']}-{$range['year']}") {
                                                                $checked = "checked"; // Set as checked if it's within the range
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    echo "<li>";
                                                    echo "<input class='form-check-input' type='checkbox' id='month{$range['month']}Year{$range['year']}' value='{$range['month']}-{$range['year']}' $checked>";
                                                    echo "<label class='form-check-label' for='month{$range['month']}Year{$range['year']}'>{$range['label']}</label>";
                                                    echo "</li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <div class="mb-4 col-auto text-right">
                                        <button type="button" class="btn btn-primary btn-icon-text" onclick="printPdf()"> Print <i class="mdi mdi-printer btn-icon-append"></i></button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="incomeTable">
                                            <thead>
                                                <tr>
                                                    <th class="font-weight-bold">Date</th>
                                                    <th class="font-weight-bold">Room Number</th>
                                                    <th class="font-weight-bold">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="paymentData">
                                                <?php
                                                foreach ($paymentData as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row["FormattedDate"] . "</td>"; // Display formatted date
                                                    echo "<td>" . $row["RoomNumbers"] . "</td>"; // Display RoomNumber
                                                    echo "<td>" . $row["Amount"] . "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                                 <tr><td colspan="3"><div id="totalAmount" class="mt-3 font-weight-bold display-5 text-center">Monthly Income: PHP <?php echo number_format($totalAmount, 2); ?></div></td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                   
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
        
       // JavaScript for checkbox selection behavior
        document.querySelectorAll('#monthCheckboxes input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCheckboxes = Array.from(document.querySelectorAll('#monthCheckboxes input[type="checkbox"]:checked'));
                if (checkedCheckboxes.length === 0) {
                    window.location.href = 'monthly.php'; // Redirect if no checkboxes are selected
                } else {
                    const selectedMonthsYears = checkedCheckboxes.map(checkbox => checkbox.value).join(',');
                    window.location.href = `monthly.php?selectedMonthsYears=${selectedMonthsYears}`;
                }
            });
        });


        function printPdf() {
    const element = document.getElementById('incomeTable');
    var currentDate = new Date().toLocaleString();

    // Create a new div to hold the content
    const contentToPrint = document.createElement('div');
    contentToPrint.innerHTML += '<img src="../../assets/images/DormBell.png" style="width: 100px; position: absolute; top: 10px; left: 10px;">'; // Add your logo path here
    contentToPrint.innerHTML += '<h4 style="text-align:center; margin-bottom: 10px;">Dorm Bell Income Report</h4>';
    contentToPrint.innerHTML += '<p style="text-align:center; margin-bottom: 5px;">Generated on: ' + currentDate + '</p>';
    contentToPrint.innerHTML += '<p style="text-align:center; margin-bottom: 20px;">Generated By: <?php echo $adminEmail; ?></p>';

    // Extract the table content as a string
    const tableContent = element.outerHTML;

    // Customize the style of the entire table, including header and body
    const styledTableContent = tableContent.replace('<table', '<table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd; text-align:center; background-color:#f2f2f2;"');
    const styledTableCells = styledTableContent.replace(/<td/g, '<td style="border: 1px solid #ddd; padding: 8px; text-align:center; background-color:#ffffff;"');

    // Add the styled table to the content
    contentToPrint.innerHTML += styledTableCells;


    // Create a new HTML document for the PDF using html2pdf
    const pdfConfig = {
        margin: 10,
        filename: 'DormBell_Monthly_Report_' + currentDate + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(pdfConfig).from(contentToPrint).save();
}
    </script>
</body>
</html>
