<?php
// Include database connection
include '../../includes/config/dbconn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_id'])) {
    $paymentId = $_POST['payment_id'];

    // Fetch payment details from the database based on PaymentID
    $stmt = $conn->prepare("SELECT * FROM payment WHERE PaymentID = ?");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $amount = $row['Amount'];
        $email = $row['EmailID'];

        // Fetch tenant details based on email from tenantprofile table
        $tenantStmt = $conn->prepare("SELECT * FROM tenantprofile WHERE email = ?");
        $tenantStmt->bind_param("s", $email);
        $tenantStmt->execute();
        $tenantResult = $tenantStmt->get_result();

        if ($tenantResult->num_rows > 0) {
            $tenantRow = $tenantResult->fetch_assoc();
            $tenantId = $tenantRow['TenantID'];
            $currentBalance = $tenantRow['balance'];

            // Deduct the payment amount from the balance
            $newBalance = $currentBalance - $amount;

                // Update the balance in the tenantprofile table
            $updateStmt = $conn->prepare("UPDATE tenantprofile SET balance = ? WHERE TenantID = ?");
            $updateStmt->bind_param("di", $newBalance, $tenantId);
            $updateStmt->execute();

            if ($updateStmt->affected_rows > 0) {
                echo "Balance updated successfully!";
            } else {
                echo "Failed to update balance.";
            }
        } else {
            echo "Tenant not found or balance not available.";
        }
    } else {
        echo "Payment details not found.";
    }

    // Close statements and database connection
    $stmt->close();
    $tenantStmt->close();
    $updateStmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
