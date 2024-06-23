<?php
include '../../includes/config/dbconn.php';
include '../../includes/function/sanitize.php';

function getEmailIDAndTenantID($dbConnection) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['TenantID'])) {
        $tenantID = $_SESSION['TenantID'];

        $stmt = $dbConnection->prepare("SELECT TenantID, EmailID FROM tenants WHERE TenantID = ?");
        $stmt->bind_param("s", $tenantID);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            return $result;
        }
    }

    return null;
}

// Assuming $conn is your database connection
$tenantData = getEmailIDAndTenantID($conn);

if ($tenantData) {
    $loggedInEmail = $tenantData['EmailID'];
    $loggedInTenantID = $tenantData['TenantID'];

    // Fetch data from the tenantprofile table based on a tenant ID
    $sqlProfile = "SELECT * FROM tenantprofile WHERE TenantID = ?";
    $stmtProfile = $conn->prepare($sqlProfile);
    $stmtProfile->bind_param("s", $loggedInTenantID);
    $stmtProfile->execute();
    $resultProfile = $stmtProfile->get_result();

    if ($resultProfile->num_rows > 0) {
        // Data found in the tenantprofile table
        $rowProfile = $resultProfile->fetch_assoc();

        // Assign fetched data to session variables
        $_SESSION['Name'] = $rowProfile['Name'];
        $_SESSION['type_of_stay'] = $rowProfile['type_of_stay'];
        $_SESSION['room_number'] = $rowProfile['room_number'];
        $_SESSION['check_in_date'] = $rowProfile['check_in_date'];
        $_SESSION['check_out_date'] = $rowProfile['check_out_date'];
        $_SESSION['Gender'] = $rowProfile['Gender'];
        $_SESSION['Age'] = $rowProfile['Age'];
        $_SESSION['email'] = $rowProfile['email'];
        $_SESSION['Address'] = $rowProfile['Address'];
        $_SESSION['ContactNumber'] = $rowProfile['ContactNumber'];
        $_SESSION['GuardianName'] = $rowProfile['GuardianName'];
        $_SESSION['EmergencyNumber'] = $rowProfile['EmergencyNumber'];
        $_SESSION['totalamount'] = $rowProfile['totalamount'];


        // Assign payment rate based on type_of_stay from tenantprofile
        if ($_SESSION['type_of_stay'] == 'Long-term') {
            // Fetch rate_per_month from the room_management table
            $roomNumber = $rowProfile['room_number'];
            $stmtRoom = $conn->prepare("SELECT rate_per_month FROM room_management WHERE room_number = ?");
            $stmtRoom->bind_param("s", $roomNumber);
            $stmtRoom->execute();
            $resultRoom = $stmtRoom->get_result();

            if ($resultRoom->num_rows > 0) {
                $roomData = $resultRoom->fetch_assoc();
                // Assign rate_per_month to the variable
                $rate = $roomData['rate_per_month'];
            }
            $stmtRoom->close();
        } elseif ($_SESSION['type_of_stay'] == 'Transient') {
            // Fetch rate_per_night from the room_management table
            $roomNumber = $rowProfile['room_number'];
            $stmtRoom = $conn->prepare("SELECT rate_per_night FROM room_management WHERE room_number = ?");
            $stmtRoom->bind_param("s", $roomNumber);
            $stmtRoom->execute();
            $resultRoom = $stmtRoom->get_result();

            if ($resultRoom->num_rows > 0) {
                $roomData = $resultRoom->fetch_assoc();
                // Assign rate_per_night to the variable
                $rate = $roomData['rate_per_night'];
            }
            $stmtRoom->close();
        }

        // Echo the rate from room_management
    } else {
        // No data found in tenantprofile, fetch data from reservations table
        $stmtReservations = $conn->prepare("SELECT Name, Email, type_of_stay, check_in_date, check_out_date, room_number FROM reservations WHERE Email = ?");
        $stmtReservations->bind_param("s", $loggedInEmail);
        $stmtReservations->execute();
        $resultReservations = $stmtReservations->get_result();

        if ($resultReservations->num_rows > 0) {
            // Data found in reservations table
            $rowReservation = $resultReservations->fetch_assoc();

            // Assign fetched data to session variables
            $_SESSION['Name'] = $rowReservation['Name'];
            $_SESSION['type_of_stay'] = $rowReservation['type_of_stay'];
            $_SESSION['room_number'] = $rowReservation['room_number'];
            $_SESSION['check_in_date'] = $rowReservation['check_in_date'];
            $_SESSION['check_out_date'] = $rowReservation['check_out_date'];
            $_SESSION['email'] = $rowReservation['Email'];

            // Optionally, you may handle other fields from the reservations table
        } else {
            // Handle case where no data found in both tenantprofile and reservations table
            echo "No data found for the logged-in user.";
        }
        $stmtReservations->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Form is submitted, process the form data and insert or update into tenantprofile table
    $name = $_POST['Name'];
    $type_of_stay = $_POST['type_of_stay'];
    $room_number = $_POST['roomNumber'];
    $check_in_date = $_POST['checkInDate'];
    $check_out_date = $_POST['checkOutDate'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $address = $_POST['Address'];
    $contactNumber = $_POST['ContactNumber'];
    $guardianName = $_POST['GuardianName'];
    $emergencyNumber = $_POST['EmergencyNumber'];
    $rate = $_POST['rate']; // Add this line to retrieve the rate value from the form
   
    // Calculate daystay based on check_in_date and check_out_date
    $checkIn = new DateTime($check_in_date);
    $checkOut = new DateTime($check_out_date);
    $daystay = $checkOut->diff($checkIn)->days;

    // Check if a profile already exists for the logged-in tenant
    $profileExists = $conn->query("SELECT ProfileID FROM tenantprofile WHERE TenantID = '$loggedInTenantID'")->fetch_assoc();

    if ($profileExists) {
        // Update the existing profile
        $stmtUpdate = $conn->prepare("UPDATE tenantprofile SET type_of_stay=?, Gender=?, age=?, email=?, Address=?, ContactNumber=?, GuardianName=?, EmergencyNumber=?, rate=?, daystay=? WHERE TenantID=?");
        $stmtUpdate->bind_param("sssssssssss", $type_of_stay, $gender, $age, $email, $address, $contactNumber, $guardianName, $emergencyNumber, $rate, $daystay, $loggedInTenantID);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Calculate total amount and update it in the tenantprofile table
        $totalAmount = 0;
        if ($_SESSION['type_of_stay'] == 'Transient') {
            // For transient, total amount is rate * daystay
            $totalAmount = $rate * $daystay;
        } elseif ($_SESSION['type_of_stay'] == 'Long-term') {
            // For long-term, calculate total months and extra days
            $totalMonths = floor($daystay / 30); // Total Months
            $extraDays = $daystay % 30; // Extra Days
            $dailyrate = $rate / 30;
            if ($extraDays != 0) {
                $totalAmount = ($rate * $totalMonths) + ($dailyrate * $extraDays);
            } else {
                $totalAmount = $rate * $totalMonths;
            }
        }
 
        // Collect all completed payments made by the tenant
        $stmtCollectPayments = $conn->prepare("SELECT SUM(Amount) AS totalPaidAmount FROM payment WHERE EmailID = ? AND Status = 'Completed'");
        if ($stmtCollectPayments === false) {
            // Handle prepare error here
            die('Error preparing statement: ' . $conn->error);
        }

        $stmtCollectPayments->bind_param("s", $email);
        $stmtCollectPayments->execute();
        $resultCollectPayments = $stmtCollectPayments->get_result();

        if ($resultCollectPayments === false) {
            // Handle execute or get_result error here
            die('Error executing statement or getting result: ' . $stmtCollectPayments->error);
        }

        if ($resultCollectPayments->num_rows > 0) {
            $rowCollectPayments = $resultCollectPayments->fetch_assoc();
            $totalPaidAmount = $rowCollectPayments['totalPaidAmount']; // Total amount paid by the tenant for completed payments

            // Calculate the balance
            $balance = $totalAmount - $totalPaidAmount;

            // Update the total amount and balance in the tenantprofile table
            $stmtUpdateTotalAmount = $conn->prepare("UPDATE tenantprofile SET totalamount = ?, balance = ? WHERE TenantID = ?");
            $stmtUpdateTotalAmount->bind_param("dds", $totalAmount, $balance, $loggedInTenantID); // Assuming balance is a decimal type
            $stmtUpdateTotalAmount->execute();
            $stmtUpdateTotalAmount->close();

            // Optionally, you can redirect the user to another page after successful submission
            header("Location: ../dashboard/dashboard.php");
            exit();
        } else {
            echo "Error fetching payment information.";
        }
    } else {
        // Insert a new profile
        $stmtInsert = $conn->prepare("INSERT INTO tenantprofile (TenantID, Name, type_of_stay, room_number, check_in_date, check_out_date, Gender, age, email, Address, ContactNumber, GuardianName, EmergencyNumber, rate, daystay, totalamount, balance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Calculate total amount based on type_of_stay and rate
        $totalAmount = 0;
        if ($_SESSION['type_of_stay'] == 'Transient') {
            // For transient, total amount is rate * daystay
            $totalAmount = $rate * $daystay;
        } elseif ($_SESSION['type_of_stay'] == 'Long-term') {
            // For long-term, calculate total months and extra days
            $totalMonths = floor($daystay / 30); // Total Months
            $extraDays = $daystay % 30; // Extra Days
            $dailyrate = $rate / 30;
            if ($extraDays != 0) {
                $totalAmount = ($rate * $totalMonths) + ($dailyrate * $extraDays);
            } else {
                $totalAmount = $rate * $totalMonths;
            }
        }

        // Collect all completed payments made by the tenant
        $stmtCollectPayments = $conn->prepare("SELECT SUM(Amount) AS totalPaidAmount FROM payment WHERE EmailID = ? AND Status = 'Completed'");
        if ($stmtCollectPayments === false) {
            // Handle prepare error here
            die('Error preparing statement: ' . $conn->error);
        }

        $stmtCollectPayments->bind_param("s", $email);
        $stmtCollectPayments->execute();
        $resultCollectPayments = $stmtCollectPayments->get_result();

        if ($resultCollectPayments === false) {
            // Handle execute or get_result error here
            die('Error executing statement or getting result: ' . $stmtCollectPayments->error);
        }

        if ($resultCollectPayments->num_rows > 0) {
            $rowCollectPayments = $resultCollectPayments->fetch_assoc();
            $totalPaidAmount = $rowCollectPayments['totalPaidAmount']; // Total amount paid by the tenant for completed payments

            // Calculate the balance
            $balance = $totalAmount - $totalPaidAmount;

            // Update the total amount and balance in the tenantprofile table
            $stmtUpdateTotalAmount = $conn->prepare("UPDATE tenantprofile SET totalamount = ?, balance = ? WHERE TenantID = ?");
            $stmtUpdateTotalAmount->bind_param("dds", $totalAmount, $balance, $loggedInTenantID); // Assuming balance is a decimal type
            $stmtUpdateTotalAmount->execute();
            $stmtUpdateTotalAmount->close();
        }

        // Bind parameters
        $stmtInsert->bind_param("ssssssssssssssddd", $loggedInTenantID, $name, $type_of_stay, $room_number, $check_in_date, $check_out_date, $gender, $age, $email, $address, $contactNumber, $guardianName, $emergencyNumber, $rate, $daystay, $totalAmount, $balance);

        // Execute the statement
        $stmtInsert->execute();

        // Close the statement
        $stmtInsert->close();

        // Optionally, you can redirect the user to another page after successful submission
        header("Location: ../dashboard/dashboard.php");
        exit();
    }
}



// Check if any reservations are found
if ($loggedInEmail) {
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE Email = ?");
    $stmt->bind_param("s", $loggedInEmail);
    $stmt->execute();

    // Get the result
    $reservations = array();
    $result = $stmt->get_result();

    // Loop through each reservation and store the data in an array
        while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Check if any reservations are found
    if (!empty($reservations)) {
        // Display the first reservation data in the form
        $firstReservation = $reservations[0];

        // Define variable for the rate
        $rate = null;

        // Assign payment rate based on type_of_stay from tenantprofile
        if ($firstReservation['type_of_stay'] == 'Long-term') {
            // Fetch rate_per_month from the room_management table
            $roomNumber = $firstReservation['room_number'];
            $stmtRoom = $conn->prepare("SELECT rate_per_month FROM room_management WHERE room_number = ?");
            $stmtRoom->bind_param("s", $roomNumber);
            $stmtRoom->execute();
            $resultRoom = $stmtRoom->get_result();

            if ($resultRoom->num_rows > 0) {
                $roomData = $resultRoom->fetch_assoc();
                // Assign rate_per_month to the variable
                $rate = $roomData['rate_per_month'];
            }
            $stmtRoom->close();
        } elseif ($firstReservation['type_of_stay'] == 'Transient') {
            // Fetch rate_per_night from the room_management table
            $roomNumber = $firstReservation['room_number'];
            $stmtRoom = $conn->prepare("SELECT rate_per_night FROM room_management WHERE room_number = ?");
            $stmtRoom->bind_param("s", $roomNumber);
            $stmtRoom->execute();
            $resultRoom = $stmtRoom->get_result();

            if ($resultRoom->num_rows > 0) {
                $roomData = $resultRoom->fetch_assoc();
                // Assign rate_per_night to the variable
                $rate = $roomData['rate_per_night'];
            }
        }

        // Populate the form fields with the first reservation data
        $_SESSION['Name'] = $firstReservation['Name'];
        $_SESSION['type_of_stay'] = $firstReservation['type_of_stay'];
        $_SESSION['room_number'] = $firstReservation['room_number'];
        $_SESSION['check_in_date'] = $firstReservation['check_in_date'];
        $_SESSION['check_out_date'] = $firstReservation['check_out_date'];
        $_SESSION['contact_number'] = $firstReservation['contact_number'];
        $_SESSION['email'] = $firstReservation['email'];

        // Optionally, perform other actions or display the form using this reservation data
    } else {
        // Handle case where no reservations are found for the given email
        echo "No reservations found for the logged-in user's email.";
    }
} else {
    // Handle case where no email is found for the logged-in user
    echo "No email found for the logged-in user.";
}

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
                        <div class="page-header">
                            <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" data-toggle="modal" data-target="#edit-user">
                        <i class="mdi mdi-plus"></i>UPDATE PROFILE
                    </button>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h3 class="card">Profile</h3>
                                     <form class="form-sample" method="post" action="">
                                        <p class="card-description">Personal info</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="fullName">Name</label>
                                                    <input type="text" class="form-control" id="Name" name="Name" placeholder="Full Name" value="<?php echo isset($_SESSION['Name']) ? $_SESSION['Name'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tenantType">Tenant Type</label>
                                                    <input type="text" class="form-control" id="type_of_stay" name="type_of_stay" value="<?php echo isset($_SESSION['type_of_stay']) ? $_SESSION['type_of_stay'] : ''; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="roomNumber">Room No.</label>
                                                    <input type="text" class="form-control" id="room_number" name="roomNumber" placeholder="Room Type" value="<?php echo isset($_SESSION['room_number']) ? $_SESSION['room_number'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="checkInDate" style="color: black;">Check-in Date</label>
                                                    <input type="date" class="form-control" id="check_in_date" name="checkInDate" value="<?php echo isset($_SESSION['check_in_date']) ? $_SESSION['check_in_date'] : ''; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="checkOutDate" style="color: black;">Check-out Date</label>
                                                    <input type="date" class="form-control" id="check_out_date" name="checkOutDate" value="<?php echo isset($_SESSION['check_out_date']) ? $_SESSION['check_out_date'] : ''; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gender">Gender</label>
                                                    <input type="text" class="form-control" id="Gender" name="Gender" placeholder="Gender" value="<?php echo isset($_SESSION['Gender']) ? $_SESSION['Gender'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="age">Age</label>
                                                    <input type="text" class="form-control" id="Age" name="age" placeholder="Age" value="<?php echo isset($_SESSION['Age']) ? $_SESSION['Age'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="text" class="form-control" id="email" name="email" placeholder="name@gmail.com" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <input type="text" class="form-control" id="Address" name="address" placeholder="Address" value="<?php echo isset($_SESSION['Address']) ? $_SESSION['Address'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="contactNumber">Contact Number</label>
                                                    <input type="text" class="form-control" id="ContactNumber" name="contactNumber" placeholder="Contact Number" value="<?php echo isset($_SESSION['contact_number']) ? $_SESSION['contact_number'] : ''; ?>" readonly />
                                                </div>
                                            </div>
                    

                                        </div>
                                        <h3 class="card">Emergency Contact</h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="emergencyContactName">Name</label>
                                                    <input type="text" class="form-control" id="GuardianName" name="GuardianName" placeholder="Full Name" value="<?php echo isset($_SESSION['GuardianName']) ? $_SESSION['GuardianName'] : ''; ?>"readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="emergencyNumber">Emergency Number</label>
                                                    <input type="text" class="form-control" id="EmergencyNumber" name="emergencyNumber" placeholder="Emergency Number" value="<?php echo isset($_SESSION['EmergencyNumber']) ? $_SESSION['EmergencyNumber'] : ''; ?>"readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="card">Payment Details</h3>
                                        <ul class="list-arrow">
                                            <li>Your Monthly Bills</li>
                                        </ul>
                                        </h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                <label for="Payment" style="color: black;">Rate Per Stay</label>
                                                <input type="number" class="form-control" id="rate" name="rate" value="<?php echo isset($rate) ? $rate : ''; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                <label for="Payment" style="color: black;">Total Amount</label>
                                               <!-- Add this code inside the relevant input field in your HTML form -->
                                               <input type="number" class="form-control" id="totalamount" name="totalamount" value="<?php echo isset($_SESSION['totalamount']) ? $_SESSION['totalamount'] : ''; ?>" readonly>

                                                </div>
                                            </div>
                                        </div>
                                        
                                    </form>
                                 <?php
include '../../includes/config/dbconn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmtFetchTotalAmount = $conn->prepare("SELECT totalamount FROM tenantprofile WHERE email = ?");
if (!$stmtFetchTotalAmount) {
    echo("Error in preparing statement: " . $conn->error);
}

$stmtFetchTotalAmount->bind_param("s", $_SESSION['email']);
$resultFetchTotalAmount = $stmtFetchTotalAmount->execute();
if (!$resultFetchTotalAmount) {
    echo("Error in executing statement: " . $stmtFetchTotalAmount->error);
}

$totalAmountResult = $stmtFetchTotalAmount->get_result();
if ($totalAmountResult && $totalAmountResult->num_rows > 0) {
    $totalAmountRow = $totalAmountResult->fetch_assoc();
    $totalExpectedAmount = $totalAmountRow['totalamount']; // Assign the actual total expected amount value
    $stmtFetchTotalAmount->close();
} else {
    echo("");
}

if (isset($rate) && $_SESSION['type_of_stay'] == 'Long-term') {
    $check_in_date = $_SESSION['check_in_date'];
    $check_out_date = $_SESSION['check_out_date'];
    $checkIn = new DateTime($check_in_date);
    $checkOut = new DateTime($check_out_date);
    $totalDays = $checkOut->diff($checkIn)->days;
    $totalMonths = floor($totalDays / 30); // Total months without considering extra days
    $extraDays = $totalDays % 30; // Extra days
    $amountPerMonth = $rate; // Amount per month is the same as the rate

    // Get the day of the check-in date
    $checkInDay = date('j', strtotime($check_in_date));

    // Calculate the payment schedule and populate the paymentSchedule array
    $paymentSchedule = [];
    $currentMonth = date('n', strtotime($check_in_date)); // Get the starting month
    for ($i = 0; $i < $totalMonths; $i++) {
        $monthYear = date('F Y', strtotime("+$i month", strtotime($check_in_date)));
        $dueDay = min($checkInDay, cal_days_in_month(CAL_GREGORIAN, $currentMonth + $i, date('Y', strtotime($check_in_date)))); // Calculate the due day

        // Calculate the due date by advancing one month from the check-in date
        $dueDate = date('Y-m-d', strtotime("+$i month +1 month", strtotime($check_in_date))); // Set the due date to the next month

        $paymentSchedule[$monthYear] = [
            'amount' => $amountPerMonth,
            'due_date' => $dueDate
        ];
    }

    // Include any extra days in the last month's payment
    $lastMonthYear = date('F Y', strtotime("+$totalMonths month", strtotime($check_in_date)));
    $adjustedAmount = 0;
    if ($extraDays > 0) {
        // Calculate the prorated amount for the extra days based on the rate
        $adjustedAmount = ($rate / 30) * $extraDays;
    }

    // Check if $lastMonth key exists in $paymentSchedule before adding to it
    if (array_key_exists($lastMonthYear, $paymentSchedule)) {
        $paymentSchedule[$lastMonthYear]['amount'] += round($adjustedAmount, 2); // Adjusted for extra days and rounded to 2 decimal places

        // Set the due date of the last month as the check-out date
        $paymentSchedule[$lastMonthYear]['due_date'] = $check_out_date;
    } else {
        $paymentSchedule[$lastMonthYear] = [
            'amount' => round($adjustedAmount, 2),
            'due_date' => $check_out_date // Set the due date to the check-out date for the last month
        ];
    }

    // Fetch existing payment records for the user
    $stmtFetchPayments = $conn->prepare("SELECT MonthYear FROM paymentschedule WHERE EmailID = ?");
    $stmtFetchPayments->bind_param("s", $_SESSION['email']);
    $stmtFetchPayments->execute();
    $existingPayments = $stmtFetchPayments->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtFetchPayments->close();

    // Delete outdated payment records not covered by the updated duration
    $validMonths = array_keys($paymentSchedule);
    $outdatedMonths = array_diff(array_column($existingPayments, 'MonthYear'), $validMonths);
    if (!empty($outdatedMonths)) {
        $placeholders = implode(',', array_fill(0, count($outdatedMonths), '?'));
        $stmtDeleteOutdated = $conn->prepare("DELETE FROM paymentschedule WHERE EmailID = ? AND MonthYear IN ($placeholders)");
        $stmtDeleteOutdated->bind_param("s" . str_repeat("s", count($outdatedMonths)), ...array_merge([$_SESSION['email']], $outdatedMonths));
        $stmtDeleteOutdated->execute();
        $stmtDeleteOutdated->close();
    }

 // Update or insert into paymentschedule table and display payment schedule in an HTML table
echo "<table class='table table-bordered'>
<thead>
    <tr>
        <th>Month and Year</th>
        <th>Amount per Month</th>
        <th>Due Date</th>
        <th>Status</th> <!-- New column for status -->
    </tr>
</thead>
<tbody>";
foreach ($paymentSchedule as $monthYear => $payment) {
    $month = date('F', strtotime($monthYear));
    $year = date('Y', strtotime($monthYear));
    $amount = $payment['amount'];
   $dueDate = date('Y-m-d', strtotime($payment['due_date']));


    // Fetch status for the current month and year
    $stmtFetchStatus = $conn->prepare("SELECT Status FROM paymentschedule WHERE EmailID = ? AND MonthYear = ?");
    $stmtFetchStatus->bind_param("ss", $_SESSION['email'], $monthYear);
    $stmtFetchStatus->execute();
    $statusResult = $stmtFetchStatus->get_result();
    $statusRow = $statusResult->fetch_assoc();
    $status = $statusRow['Status'];
    $stmtFetchStatus->close();

    // Check if the amount is greater than zero before displaying the row
    if ($amount > 0) {
     echo "<tr><td>$month $year</td><td>Php $amount</td><td>" . date('F j, Y', strtotime($dueDate)) . "</td><td>$status</td></tr>";


        // Update or insert into paymentschedule table for the current month and year
        $stmtCheckExisting = $conn->prepare("SELECT * FROM paymentschedule WHERE EmailID = ? AND MonthYear = ?");
        $stmtCheckExisting->bind_param("ss", $_SESSION['email'], $monthYear);
        $stmtCheckExisting->execute();
        $result = $stmtCheckExisting->get_result();
        if ($result->num_rows > 0) {
            // Update existing record
            $stmtUpdatePayment = $conn->prepare("UPDATE paymentschedule SET Amount = ?, rent_due_date = ?, Status = ? WHERE EmailID = ? AND MonthYear = ?");
            $stmtUpdatePayment->bind_param("dssss", $amount, $dueDate, $status, $_SESSION['email'], $monthYear);
            $stmtUpdatePayment->execute();
            $stmtUpdatePayment->close();
        } else {
            // Insert new record
            $stmtInsertPayment = $conn->prepare("INSERT INTO paymentschedule (EmailID, MonthYear, Amount, rent_due_date, Status) VALUES (?, ?, ?, ?, ?)");
            $stmtInsertPayment->bind_param("ssdss", $_SESSION['email'], $monthYear, $amount, $dueDate, $status);
            $stmtInsertPayment->execute();
            $stmtInsertPayment->close();
        }
        $stmtCheckExisting->close();
    }
}
echo "</tbody></table>";

    // Update status to "Pending" where status is "Not Completed" or is NULL
    $stmtUpdateStatus = $conn->prepare("UPDATE paymentschedule SET Status = 'Pending' WHERE (Status = 'Not Completed' OR Status IS NULL) AND EmailID = ?");
    $stmtUpdateStatus->bind_param("s", $_SESSION['email']);
    $stmtUpdateStatus->execute();
    $stmtUpdateStatus->close();

    // Compare total amount in paymentschedule with completed amount in payment table
    $stmtCheckTotalAmount = $conn->prepare("SELECT SUM(Amount) AS TotalAmount FROM paymentschedule WHERE EmailID = ?");
    $stmtCheckTotalAmount->bind_param("s", $_SESSION['email']);
    $stmtCheckTotalAmount->execute();
    $resultTotalAmount = $stmtCheckTotalAmount->get_result();
    $totalAmountRow = $resultTotalAmount->fetch_assoc();
    $totalAmountSchedule = $totalAmountRow['TotalAmount']; // Total amount in paymentschedule
    $stmtCheckTotalAmount->close();

    // Calculate total completed amount paid by the tenant from the payment table
    $stmtTotalCompleted = $conn->prepare("SELECT SUM(Amount) AS TotalCompleted FROM payment WHERE EmailID = ? AND Status = 'Completed'");
    $stmtTotalCompleted->bind_param("s", $_SESSION['email']);
    $stmtTotalCompleted->execute();
    $resultTotalCompleted = $stmtTotalCompleted->get_result();
    $totalCompletedRow = $resultTotalCompleted->fetch_assoc();
    $totalCompletedAmount = $totalCompletedRow['TotalCompleted']; // Total completed amount paid by the tenant
    $stmtTotalCompleted->close();

    // Compare total completed amount with total amount in paymentschedule and update if matched
    if ($totalCompletedAmount == $totalAmountSchedule) {
        // Update all payment schedule entries for this tenant to "Completed"
        $stmtUpdateAllCompleted = $conn->prepare("UPDATE paymentschedule SET Status = 'Completed' WHERE EmailID = ?");
        $stmtUpdateAllCompleted->bind_param("s", $_SESSION['email']);
        $stmtUpdateAllCompleted->execute();
        $stmtUpdateAllCompleted->close();
    }

    // Compare records in paymentschedule and payment tables, and update Status if matched and payment is Completed
    $stmtCompareAndUpdate = $conn->prepare("UPDATE paymentschedule ps
                                            JOIN payment p ON ps.EmailID = p.EmailID AND ps.MonthYear = p.Month AND ps.Amount = p.Amount
                                            SET ps.Status = 'Completed'
                                            WHERE ps.EmailID = ? AND p.Status = 'Completed'");
    $stmtCompareAndUpdate->bind_param("s", $_SESSION['email']);
    $stmtCompareAndUpdate->execute();
    $stmtCompareAndUpdate->close();

    // Display the months covered from January to December
    $startDate = new DateTime($check_in_date);
    $endDate = new DateTime($check_out_date);
    $months = [];
    while ($startDate <= $endDate) {
        $monthYear = $startDate->format('F Y');
        $months[] = $monthYear;
        $startDate->modify('+1 month');
    }
}
?>
                                </div>
                            </div>
                            </div>
                              <?php include '../modals.php'; ?>
                        </div>
                        <?php include '../footer.php'; ?>
                        </div>
                    </div>
        <?php include '../scripts.php'; ?>
         <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Select input fields
       const inputFields = document.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], textarea');
        // Add event listeners to each input field
        inputFields.forEach(function(inputField) {
            inputField.addEventListener('input', function(event) {
                const maxLength = 40; // Maximum allowed characters
                if (event.target.value.length > maxLength) {
                    event.target.value = event.target.value.slice(0, maxLength); // Truncate input if exceeds limit
                }
            });
        });
    });
    </script>
    </body>
</html>