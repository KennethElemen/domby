<?php
function displayPaymentData($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to order by 'Date' in descending order
    $stmt = $conn->prepare("SELECT * FROM payment WHERE Status = 'Completed' ORDER BY PaymentID DESC");
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

            // Only show the dropdown if the status is not 'Completed'
            if ($row['Status'] !== 'Completed') {
                echo '<select class="form-control status-dropdown" data-id="' . $row['PaymentID'] . '">';
                
            } else {
                // If status is 'Completed', display a div for clarity
                echo '<div class="btn btn-success "><span>Completed</span></div>';
            }

            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="9">No transactions found</td></tr>';
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>
