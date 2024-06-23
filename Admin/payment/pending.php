
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
                    <div class="page-header"></div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <div class="col-lg-12 mb-4">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <h1 class=" mb-3 mb-md-0">Pending payments<ul class="list-arrow">
                                                    <li>After completing/rejecting one payment please wait for the page to fully reload</li>
                                                </ul>
                                            </h1>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Month Paid</th>
                                                    <th>Email</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Reference code</th>
                                                    <th>Proof of Payment</th>
                                                    <th class="reason-column">Reason</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                // Database connection details
                                                include '../../includes/config/dbconn.php';

                                                include '../../includes/function/paymentUpdate2.php';
                                                function displayPendingPayments($servername, $username, $password, $dbname) {
                                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                                
                                                    // Check the connection
                                                    if ($conn->connect_error) {
                                                        die("Connection failed: " . $conn->connect_error);
                                                    }
                                                
                                                    // Use prepared statement to fetch only pending payments
                                                    $stmt = $conn->prepare("SELECT * FROM payment WHERE Status = 'Pending' ORDER BY PaymentID DESC");
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                
                                                    // Check the query result
                                                    if (!$result) {
                                                        die("Query failed: " . $conn->error);
                                                    }
                                                
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo '<tr>';
                                                            echo '<td>' . $row['Date'] . '</td>';
                                                            echo '<td>' . $row['Month'] . '</td>';
                                                            echo '<td>' . $row['EmailID'] . '</td>';
                                                            echo '<td>' . $row['Amount'] . '</td>';
                                                            echo '<td>' . $row['PaymentMethod'] . '</td>';
                                                            echo '<td>' . $row['reference'] . '</td>';
                                                            echo '<td>';
                                                            if (!empty($row['ProofOfPayment'])) {
                                                                echo '<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#view-proof-of-payment" data-image="' . $row['ProofOfPayment'] . '">View</button>';
                                                            }
                                                            echo '</td>';
                                                            echo '<td>';
                                                
                                                            // Add a dropdown for rejection reason
                                                            if ($row['Status'] === 'Rejected') {
                                                                echo '<select class="form-control reason-dropdown" data-id="' . $row['PaymentID'] . '" disabled>';
                                                                echo '<option value="' . $row['Reason'] . '" selected>' . $row['Reason'] . '</option>';
                                                                echo '</select>';
                                                            } else {
                                                                echo '<div class="reason-column" style="display: none;">'; // Hide the reason column by default
                                                                echo '<select class="form-control reason-dropdown" data-id="' . $row['PaymentID'] . '">';
                                                                echo '<option value="" selected>Select Reason</option>';
                                                                echo '<option value="Incorrect Information">Incorrect Information</option>';
                                                                echo '<option value="Invalid Photo">Invalid Photo</option>';
                                                                echo '<option value="Insufficient Funds">Insufficient Funds</option>';
                                                                echo '<option value="Incomplete Documentation">Incomplete Documentation</option>';
                                                                echo '<option value="Duplicate Payment">Duplicate Payment</option>';
                                                                echo '<option value="Payment Discrepancy">Payment Discrepancy</option>';
                                                                echo '<option value="Fraudulent Activity">Fraudulent Activity</option>';
                                                              
                                                                echo '</select>';
                                                                echo '</div>';
                                                            }
                                                
                                                            echo '</td>';
                                                            echo '<td>';
                                                
                                                            // Only show the dropdown if the status is not 'Completed'
                                                            if ($row['Status'] !== 'Completed') {
                                                                echo '<select class="form-control status-dropdown" data-id="' . $row['PaymentID'] . '">';
                                                                echo '<option value="Pending" selected>Pending</option>';
                                                                echo '<option value="Completed">Completed</option>';
                                                                echo '<option value="Rejected">Rejected</option>';
                                                                echo '</select>';
                                                            } else {
                                                                // If status is 'Completed', display a div for clarity
                                                                echo '<div class="btn btn-success"><span>Completed</span></div>';
                                                            }
                                                
                                                            echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="10">No pending transactions found</td></tr>';
                                                    }
                                                
                                                    // Close the connection
                                                    $stmt->close();
                                                    $conn->close();
                                                }

                                                // Call the function to process payments
                                                displayPendingPayments($servername, $username, $password, $dbname);
                                                    // Function to update paymentschedule table status
                                                   // Function to update paymentschedule table status
                                                   function updatePaymentScheduleStatus($servername, $username, $password, $dbname) {
                                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                                
                                                    // Check the connection
                                                    if ($conn->connect_error) {
                                                        die("Connection failed: " . $conn->connect_error);
                                                    }
                                                
                                                    // Update paymentschedule table status for records with completed payments
                                                    $updateSql = "UPDATE paymentschedule ps
                                                        SET ps.Status = 'Completed'
                                                        WHERE ps.Status = 'Pending'
                                                        AND EXISTS (
                                                            SELECT 1 FROM payment p
                                                            WHERE p.EmailID = ps.EmailID
                                                            AND p.Month = ps.MonthYear
                                                            AND p.Status = 'Completed'
                                                        )";
                                                
                                                    // Execute the update query
                                                    if ($conn->query($updateSql) === TRUE) {
                                                        // Check if the total amount in paymentschedule matches the total of completed amounts paid
                                                        $checkTotalAmountSql = "SELECT SUM(Amount) AS TotalAmount FROM paymentschedule WHERE Status = 'Completed'";
                                                        $result = $conn->query($checkTotalAmountSql);
                                                
                                                        if ($result && $result->num_rows > 0) {
                                                            $row = $result->fetch_assoc();
                                                            $totalAmountCompleted = $row['TotalAmount'];
                                                
                                                            $checkTotalPaidSql = "SELECT SUM(Amount) AS TotalPaid FROM payment WHERE Status = 'Completed'";
                                                            $resultTotalPaid = $conn->query($checkTotalPaidSql);
                                                
                                                            if ($resultTotalPaid && $resultTotalPaid->num_rows > 0) {
                                                                $rowTotalPaid = $resultTotalPaid->fetch_assoc();
                                                                $totalAmountPaid = $rowTotalPaid['TotalPaid'];
                                                
                                                                if ($totalAmountCompleted === $totalAmountPaid) {
                                                                    // If total amount matches, update all paymentschedule records to 'Completed'
                                                                    $updateAllSql = "UPDATE paymentschedule SET Status = 'Completed' WHERE Status = 'Pending'";
                                                                    if ($conn->query($updateAllSql) === TRUE) {
                                                                        echo "All paymentschedule records updated to 'Completed'";
                                                                    } else {
                                                                        echo "Error updating paymentschedule records: " . $conn->error;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        echo "Error updating records: " . $conn->error;
                                                    }
                                                
                                                    // Close the connection
                                                    $conn->close();
                                                }

                                                // Call the function to update paymentschedule table status
                                                updatePaymentScheduleStatus($servername, $username, $password, $dbname);
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="view-proof-of-payment" tabindex="-1" role="dialog" aria-labelledby="view-proof-of-payment-label" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="view-proof-of-payment-label">Proof of Payment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img id="proof-of-payment-img" class="d-block mx-auto img-fluid custom-img" src="path/to/your/image.jpg" alt="Proof of Payment">
                            </div>
                        </div>
                    </div>
                </div>
                <?php include '../footer.php'; ?>
            </div>
        </div>
    </div>
    <?php include '../scripts.php'; ?>
    <!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#view-proof-of-payment').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var imageBase64 = button.data('image');
            var modal = $(this);
            modal.find('#proof-of-payment-img').attr('src', 'data:image/jpeg;base64,' + imageBase64);
        });

        $('.status-dropdown').change(function () {
            var paymentId = $(this).data('id');
            var newStatus = $(this).val();
            var reasonDropdown = $(this).closest('tr').find('.reason-dropdown'); // Reason dropdown
            var reasonColumn = $(this).closest('tr').find('.reason-column'); // Reason column

            // Show/hide reason column based on the selected status
            if (newStatus === 'Rejected') {
                reasonColumn.show();
                // Prompt user to select reason using SweetAlert
                Swal.fire({
                    title: 'Select Reason',
                    input: 'select',
                    inputOptions: {
                        'Incorrect Information': 'Incorrect Information',
                        'Invalid Photo': 'Invalid Photo',
                        'Insufficient Funds': 'Insufficient Funds',
                        'Incomplete Documentation': 'Incomplete Documentation',
                        'Duplicate Payment': 'Duplicate Payment',
                        'Payment Discrepancy': 'Payment Discrepancy',
                        'Fraudulent Activity': 'Fraudulent Activity',
                        
                    },
                    inputPlaceholder: 'Select a reason',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        return new Promise((resolve) => {
                            if (value !== '') {
                                resolve();
                            } else {
                                resolve('You need to select a reason');
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update status and reason after user selects a reason
                        updateStatusAndReason(paymentId, newStatus, result.value);
                    }
                });
            } else if (newStatus === 'Completed') {
                // Show SweetAlert for completing the payment
                Swal.fire({
                    title: 'Confirm Completion',
                    text: 'Are you sure you want to mark this payment as completed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, mark as completed'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update status without reason for completed payments
                        updateStatusAndReason(paymentId, newStatus, '');
                    } else {
                        // Reset the dropdown value if the user cancels
                        $(this).val('');
                    }
                });
            } else {
                reasonColumn.hide();
                // Update status without reason for other statuses
                updateStatusAndReason(paymentId, newStatus, '');
            }
        });

        function updateStatusAndReason(paymentId, newStatus, reason) {
            // Send an AJAX request to update the status and reason
            $.ajax({
                url: '../../includes/function/update_status.php',
                method: 'POST',
                data: { payment_id: paymentId, new_status: newStatus, Reason: reason }, // Correctly specify the field name
                success: function (response) {
                    // Handle the response if needed
                    console.log(response);

                    // Reload the page upon successful status update
                    location.reload();
                },
                error: function (xhr, status, error) {
                    // Handle errors if any
                    console.error(xhr.responseText);
                }
            });
        }

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
