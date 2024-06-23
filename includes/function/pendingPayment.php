<?php
include 'paymentUpdate2.php';

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
                echo '<option value="Other">Other</option>';
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

?>
