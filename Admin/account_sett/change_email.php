<?php
// Include necessary files
include_once '../../includes/config/dbconn.php';
include_once '../../includes/function/check_session.php';
include_once '../../includes/config/mailer.php';

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check session and user role
checkSession($conn, ['admin']);

// Initialize variables
$showOTPDialog = false;
$response = array();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['otp'])) {
    // Retrieve form data
    $newEmail = $_POST['newEmail'];

    // Store submitted email in session
    $_SESSION['newEmail'] = $newEmail;

    // Fetch admin data from the database based on the AdminID
    $query = "SELECT * FROM admins WHERE AdminID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $_SESSION['AdminID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the admin with the provided AdminID exists
    if (!$user) {
        // User not found error notification
        $response['error'] = true;
        $response['message'] = "User not found";
        echo json_encode($response);
        exit();
    }

    // Generate OTP
    $otp = generateOTP();

    // Store OTP in session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_creation_time'] = time(); // Set OTP creation time

    // Send OTP to the current admin's email address
    $otpSent = sendOTP($user['Email'], $otp);

    // Set the flag to show the OTP verification dialog
    $showOTPDialog = true;
}

// Verify OTP and update email if OTP is correct
if (isset($_POST['otp'])) {
    $enteredOTP = $_POST['otp'];
    if ($enteredOTP == $_SESSION['otp']) {
        // OTP is correct, update email
        $newEmail = $_SESSION['newEmail'];

        $updateQuery = "UPDATE admins SET Email = ? WHERE AdminID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param('ss', $newEmail, $_SESSION['AdminID']);
        $updateStmt->execute();

        // Destroy the session
        session_destroy();

        // Set OTP verified flag
        $response['verified'] = true;
        echo json_encode($response);
        exit();
    } else {
        // Incorrect OTP
        $response['error'] = true;
        $response['message'] = "Incorrect OTP";
        echo json_encode($response);
        exit();
    }
}

// Function to generate OTP
function generateOTP() {
    // Generate a random six-digit OTP
    return rand(100000, 999999);
}

// Function to send OTP to email
function sendOTP($email, $otp) {
    $subject = 'Your OTP for Email Change';
    $message = '<html>
    <head>
        <title>OTP for Email Change</title>
    </head>
    <body>
        <p>Your OTP for email change is: <strong>' . $otp . '</strong></p>
    </body>
    </html>';

    // Send email using PHPMailer
    return sendEmail($email, $subject, $message);
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
                <div class="alert-container"></div>
                <div class="content-wrapper">
                    <div class="row flex-grow-1">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h1 class="mb-12">Change Email<ul class="list-arrow"><li>You will receive and OTP to the current email address for confirmation</li></ul></h1>
                                    <hr>
                                    <form class="forms-sample" method="post" action="">
                                        <div class="form-group">
                                            <label for="newEmail">New Email</label>
                                            <input type="email" class="form-control" name="newEmail" id="newEmail" placeholder="New Email" required>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include '../footer.php'; ?>
            </div>
        </div>
    </div>
    <script>
        function cancel() {
            window.location.href = 'account_sett.php';
        }
    </script>
    <?php include '../scripts.php'; ?>

    <?php if ($showOTPDialog): ?>
        <script>
            // Display OTP input dialog
            Swal.fire({
                title: 'Enter OTP',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Verify',
                showLoaderOnConfirm: true,
                preConfirm: (otp) => {
                    // Submit OTP for verification
                    return fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'otp=' + otp, // Only send OTP for verification, no need to send new email
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    });
                }
            }).then((result) => {
                // Handle OTP verification result
                if (result.isConfirmed) {
                    if (result.value.error) {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.value.message
                        });
                    } else if (result.value.verified) {
                        // Show success message and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Email updated successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '../../Guest/login.php';
                        });
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
