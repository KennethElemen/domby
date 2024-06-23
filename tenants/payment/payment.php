<?php
include '../../includes/config/dbconn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's email from the session
function getEmailIDByTenantID($dbConnection)
{
    // Initialize session if not started
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Start the session
    }

    // Check if TenantID is set in the session
    if (isset($_SESSION['TenantID'])) {
        $tenantID = $_SESSION['TenantID'];

        // Use prepared statement to prevent SQL injection
        $stmt = $dbConnection->prepare("SELECT EmailID FROM tenants WHERE TenantID = ?");
        $stmt->bind_param("s", $tenantID);
        $stmt->execute();

        // Get the result
        $emailIDResult = $stmt->get_result()->fetch_assoc();

        // Close the statement
        $stmt->close();

        // Check if a result is found
        if ($emailIDResult) {
            // Return the EmailID if needed in the calling code
            return $emailIDResult['EmailID'];
        }
    }

    // If TenantID is not set in the session or no result is found, return null
    return null;
}

// Get the logged-in user's email
$loggedInEmail = getEmailIDByTenantID($conn);

// Check if email is available before querying the database
if ($loggedInEmail) {
    // Retrieve rate, total amount, balance, and type_of_stay from the tenant profile
    $profileSql = "SELECT rate, totalamount, balance, type_of_stay FROM tenantprofile WHERE email = ?";
    $profileStmt = $conn->prepare($profileSql);
    $profileStmt->bind_param("s", $loggedInEmail);
    $profileStmt->execute();
    $profileResult = $profileStmt->get_result();

    // Fetch the profile data
    if ($profileResult->num_rows > 0) {
        $profileRow = $profileResult->fetch_assoc();
        $rate = $profileRow['rate'];
        $totalAmount = $profileRow['totalamount'];
        $balance = $profileRow['balance'];
        $typeOfStay = $profileRow['type_of_stay']; // Added this line to fetch type_of_stay
    } else {
        // Set default values if no profile data found
        $rate = 0;
        $totalAmount = 0;
        $balance = 0;
        $typeOfStay = ''; // Default value for type_of_stay
    }

    // Close the profile statement
    $profileStmt->close();
}


?>
<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<head>
  <style>
.status-pending {
  
    color: orange; 
}

.status-completed {
    color: green; 
}

.status-rejected {
    color: red; 
}
</style>
</head>
<!DOCTYPE html>
<html lang="en">





<body>
    <div class="container-scroller">
        <?php include '../topbar.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                    <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" onclick="openFullPaymentPage()">
    <i class="mdi mdi-plus"></i>Full Payment
</button>
<?php
// Check if the tenant is long-term to show the Monthly Payment button
if ($typeOfStay == 'Long-term') {
    echo '<button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" onclick="openPaymentPage()">';
    echo '<i class="mdi mdi-plus"></i>Monthly Payment';
    echo '</button>';
}
?>
                    </div>
                   <div class="row">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body shadow-lg">
                                <h3 class="card mb-4">Account Summary</h3>
                                <div class="table-responsive">
                                    <table class='table .table mb-4'> <!-- Added mb-4 for margin bottom -->
                                        <thead class="text-center">
                                            <tr>
                                                <th><strong>Rate</strong></th>
                                                <th><strong>Total Bills</strong></th>
                                                <th><strong>Balance</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <tr>
                                                <td><?php echo $rate; ?></td>
                                                <td><?php echo $totalAmount; ?></td>
                                                <td><?php echo ($balance >= 0) ? $balance : 0; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                 
                                    <h3 class=" mt-4 mb-4">Transaction Details</h3>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Month Paid</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Proof of Payment</th>
                                                    <th>Reason</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                    <tbody>
                                         <?php
                                           
                                            include '../../includes/config/dbconn.php';

                                            $conn = new mysqli($servername, $username, $password, $dbname);

                                            // Check the connection
                                            if ($conn->connect_error) {
                                                die("Connection failed: " . $conn->connect_error);
                                            }

                                            // Get the logged-in user's email from the session
                                            function getEmailIDTenantID($dbConnection) {
                                                // Initialize session if not started
                                                if (session_status() == PHP_SESSION_NONE) {
                                                    session_start(); // Start the session
                                                }

                                                // Check if TenantID is set in the session
                                                if (isset($_SESSION['TenantID'])) {
                                                    $tenantID = $_SESSION['TenantID'];

                                                    // Use prepared statement to prevent SQL injection
                                                    $stmt = $dbConnection->prepare("SELECT EmailID FROM tenants WHERE TenantID = ?");
                                                    $stmt->bind_param("s", $tenantID);
                                                    $stmt->execute();

                                                    // Get the result
                                                    $emailIDResult = $stmt->get_result()->fetch_assoc();

                                                    // Close the statement
                                                    $stmt->close();

                                                    // Check if a result is found
                                                    if ($emailIDResult) {
                                                        // Return the EmailID if needed in the calling code
                                                        return $emailIDResult['EmailID'];
                                                    }
                                                }

                                                // If TenantID is not set in the session or no result is found, return null
                                                return null;
                                            }

                                            // Get the logged-in user's email
                                            $loggedInEmail = getEmailIDByTenantID($conn);

                                            // Check if email is available before querying the database
                                            if ($loggedInEmail) {
                                                $sql = "SELECT * FROM payment WHERE EmailID = ?";
                                                $stmt = $conn->prepare($sql);

                                                // Bind the email parameter
                                                $stmt->bind_param("s", $loggedInEmail);

                                                $stmt->execute();

                                                $result = $stmt->get_result();

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        // Set default status to 'pending' if the 'Status' field is empty
                                                        $status = empty($row['Status']) ? 'Pending' : $row['Status'];
                                                
                                                        echo '<tr>';
                                                        echo '<td>' . $row['Date'] . '</td>';
                                                        echo '<td>' . $row['Month'] . '</td>';
                                                        echo '<td>' . $row['Amount'] . '</td>';
                                                        echo '<td>' . $row['PaymentMethod'] . '</td>';
                                                        echo '<td>';
                                                        if (!empty($row['ProofOfPayment'])) {
                                                            echo '<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#view-proof-of-payment" data-image="' . $row['ProofOfPayment'] . '">View</button>';
                                                        }
                                                        echo '</td>';
                                                         echo '<td>' . $row['Reason'] . '</td>';
                                                        echo '<td class="status-' . strtolower($status) . '">' . $status . '</td>';
                                                        echo '</tr>';
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="9">No transactions found</td></tr>';
                                                }
                                                

                                                $stmt->close();
                                            }

                                            $conn->close();
                                            ?>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- View proof of payment modal -->
                <div class="modal fade" id="view-proof-of-payment" tabindex="-1" role="dialog"aria-labelledby="view-proof-of-payment-label" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="view-proof-of-payment-label">Proof of Payment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img id="proof-of-payment-img" class="d-block mx-auto img-fluid custom-img"
                                    alt="Proof of Payment" style="width: 50%; height: 50%">
                            </div>
                        </div>
                    </div>
                </div>
                 <?php include '../modals.php'; ?>
                <?php include '../footer.php'; ?>
            </div>
        </div>
    </div>
    <?php include '../scripts.php'; ?>
    <script>
        $(document).ready(function () {
            $('#view-proof-of-payment').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var imageBase64 = button.data('image');
                var modal = $(this);
                modal.find('#proof-of-payment-img').attr('src', 'data:image/jpeg;base64,' + imageBase64);
            });
        });
    </script>
</body>

</html>