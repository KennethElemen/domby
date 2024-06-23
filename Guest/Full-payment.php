<?php
include '../includes/config/dbconn.php';
include '../includes/function/sanitize.php';

function signOut() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the landing page (index.php)
    header("Location: ../../index.php");
    exit();
}

// Check if the 'Signout' parameter is set in the URL
if (isset($_GET['Signout'])) {
    // Call the signOut function to perform the signout process
    signOut();
}

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
        $rowProfile = $resultProfile->fetch_assoc();

        // Assign fetched data to session variables
        $_SESSION['Name'] = $rowProfile['Name'];
        $_SESSION['room_number'] = $rowProfile['room_number'];
        $_SESSION['email'] = $rowProfile['email'];
        $_SESSION['balance'] = $rowProfile['balance'];
        // You can add more session variables here based on the columns in tenantprofile table
    }

    $stmtProfile->close();
} else {
    echo "No tenant data found for the logged-in user.";
     header("Location: ../errorpage/error-403.php");
    
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Full Payment</title>
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/css/style.css">
     <link rel="stylesheet" href="../assets/css/style1.css">
     <link rel="stylesheet" href="../assets/css/dropify.min.css">
      <link rel="shortcut icon" href="../assets/images/favicon.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    
</head>
<body>
<div class="container-scroller">
    <div class="row">
        <div class="col-12 text-right mb-3">
                <div style="position: fixed; bottom: 20px; right: 50px; margin-right: 10px; margin-bottom: 10px; width: 56px; height: 56px; z-index: 9999;">
                    <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-lg rounded-circle m-3" data-toggle="modal" data-target="#Payment">
                        HELP
                    </button>
                </div>
           
                            <?php
                            include '../includes/function/paymentUpdate.php';

                            require '../includes/config/dbconn.php';
                            $conn = new mysqli($servername, $username, $password, $dbname);

                            // Check the connection
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // Fetch room numbers from the room_management table
                            $room_numbers_query = "SELECT room_number FROM room_management";
                            $result = $conn->query($room_numbers_query);

                            // Check if the query was successful
                            if ($result) {
                                // Fetch room numbers into an array
                                $room_numbers = [];
                                while ($row = $result->fetch_assoc()) {
                                    $room_numbers[] = $row['room_number'];
                                }
                            } else {
                                // Handle the case where the query fails
                                echo "Error fetching room numbers: " . $conn->error;
                            }

                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $roomNumber = isset($_POST['room_number']) ? $_POST['room_number'] : '';
                                $fullName = isset($_POST['full_name']) ? $_POST['full_name'] : '';
                                $email = isset($_POST['email']) ? $_POST['email'] : '';
                                $balance = isset($_POST['balance']) ? $_POST['balance'] : '';
                                $reference = isset($_POST['reference']) ? $_POST['reference'] : '';
                                $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
                                $paymentType = isset($_POST['payment_type']) ? $_POST['payment_type'] : '';
                                $paymentMonth = isset($_POST['payment_Month']) ? $_POST['payment_Month'] : '';

                                // Validate and sanitize your input here

                                $targetDir = "../Admin/uploads/";
                                $targetFile = $targetDir . basename($_FILES['img']['name']);
                                $uploadOk = 1;
                                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                                // Check if the file already exists
                                if (file_exists($targetFile)) {
                                   header("Location: ../errorpage/paymentunsuccessful.php");
                                    $uploadOk = 0;
                                }

                                // Check file size
                                if ($_FILES['img']['size'] > 500000000) {
                                   header("Location: ../errorpage/paymentunsuccessful.php");
                                    $uploadOk = 0;
                                }

                                // Allow only certain file formats
                                if ($imageFileType !== 'jpg' && $imageFileType !== 'png' && $imageFileType !== 'jpeg') {
                                    header("Location: ../errorpage/paymentunsuccessful.php");
                                    $uploadOk = 0;
                                }

                                // Check if $uploadOk is set to 0 by an error
                                if ($uploadOk === 0) {
                                      header("Location: ../errorpage/paymentunsuccessful.php");
                                } else {
                                    // If everything is ok, try to upload file
                                    if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                                        echo "The file " . htmlspecialchars(basename($_FILES['img']['name'])) . " has been uploaded.";
                                        
                                         header("Location: ../errorpage/error-404.php");
                                        $imageData = base64_encode(file_get_contents($targetFile));

                                        $status = isset($_POST['status']) ? $_POST['status'] : 'Pending';

                                        $sql = "INSERT INTO payment (RoomNumber, Name, EmailID, Amount,reference, PaymentMethod, PaymentType, ProofOfPayment, Status, Month)
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)";

                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("ssssssssss", $roomNumber, $fullName, $email, $balance, $reference ,$paymentMethod, $paymentType, $imageData, $status , $paymentMonth);

                                        if ($stmt->execute()) {
                                            // Call the function to send pending payment email
                                            sendPendingPaymentEmail($email, $stmt->insert_id); // Assuming PaymentID is auto-incremented

                                            header("Location: ../errorpage/paymentSuccess.php");
                                        } else {
                                             header("Location: ../errorpage/paymentunsuccessful.php");
                                            echo "Error: " . $stmt->error;
                                        }

                                        $stmt->close();
                                        $conn->close();
                                    } else {
                                        echo '<div class="alert alert-danger" role="alert">';
                                        echo "Sorry, there was an error uploading your file.";
                                        echo '</div>';
                                    }
                                }
                            }
                            ?>
                 
                            
            <!-- Section 1: Login Form -->
            <div class="content-wrapper d-flex align-items-center justify-content-center auth">
                <div class="row flex-grow">
                    
                    <!-- Center the image on the left with right margin -->
                    <div  class="col-lg-6 text-center mr-lg-4">
                    <div class="text-center mt-3">
                        <h1 class="display-8">Full Payment</h1>
                        <br>
    <button type="button" class="btn btn-info" style="width:200px;" onclick="showQRCode('gcash')">GCash</button>
    <button type="button" class="btn btn-success" style="width:200px;" onclick="showQRCode('paymaya')">PayMaya</button>
</div><br>
                    <div id="gcashQRCode">
                    <?php
                        // Database connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check the connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch QR code path and payment number from the about table
                        $aboutResult = mysqli_query($conn, "SELECT QRCode, paymentNumber FROM about");
                        $aboutInfo = mysqli_fetch_assoc($aboutResult);

                        // Use this information when generating the contract content
                        $qrCodePath = $aboutInfo['QRCode'];
                        $paymentNumber = $aboutInfo['paymentNumber'];

                        $conn->close();
                        
                        // Check if QRCodePath is empty
                        if (empty($qrCodePath)) {
                            echo '<div class="alert alert-danger" role="alert">';
                            echo "QR Code unavalable.";
                            echo '</div>';
                        } else {
                            echo '<div>';
                            echo '<img src="../Admin/uploads/Screenshot 2024-05-06 205910.png" class="img-fluid" alt="QR Code" style="width: 500px; height: 500px; box-shadow: 0 0 3px #2555f5">';
                            echo '</div>';
}
                    ?>
                   <div class="text-center mt-5" style="font-size: 20px; margin-bottom:20px; ">
                     <p><strong>Friendly Reminder:</strong> Please ensure to pay the exact amount of your <br>down payment, as any excess amount unfortunately cannot be refunded. Thank you!</p>
                <?php
                        // Assuming $qrCodePath contains the path to the QR code image
                        echo "<strong>Gcash Number:</strong><br> $paymentNumber";
                    ?>
                   
                    </div>
                    
                    </div>
                    <div id="paymayaQRCode">
                    
                    <?php
                        // Database connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check the connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch QR code path and payment number from the about table
                        $aboutResult = mysqli_query($conn, "SELECT QRCode2, paymentNumber2 FROM about");
                        $aboutInfo = mysqli_fetch_assoc($aboutResult);

                        // Use this information when generating the contract content
                        $qrCodePath2 = $aboutInfo['QRCode2'];
                        $paymentNumber2 = $aboutInfo['paymentNumber2'];

                        $conn->close();
                        
                        // Check if QRCodePath is empty
                        if (empty($qrCodePath2)) {
                            echo '<div class="alert alert-danger" role="alert">';
                            echo "QR Code unavalable.";
                            echo '</div>';
                        } else {
                            echo '<div>';
                            echo '<img src="../Admin/uploads/' . $qrCodePath2 . '" class="img-fluid" alt="QR Code" style="width: 500px; height: 500px; box-shadow: 0 0 3px #039127">';
                            echo '</div>';
}
                    ?>
                    <div class="text-center mt-5" style="font-size: 20px; margin-bottom:20px; ">
                     <p><strong>Friendly Reminder:</strong> Please ensure to pay the exact amount of your <br>down payment, as any excess amount unfortunately cannot be refunded. Thank you!</p>
                    <?php
                            // Assuming $qrCodePath contains the path to the QR code image
                            echo "<strong>PayMaya Number:</strong><br> $paymentNumber2";
                        ?>
                    </div>
                    </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="auth-form-light text-left p-4">
                                    

                                <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="roomNumber">Room No.</label>
                                    <input type="text" style="background-color: #edebeb;" class="form-control" id="room_number" name="room_number" placeholder="Room Type" value="<?php echo isset($_SESSION['room_number']) ? $_SESSION['room_number'] : ''; ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label for="fullName">Name</label>
                                    <input type="text" style="background-color: #edebeb;" class="form-control" id="Name" name="full_name" placeholder="Full Name" value="<?php echo isset($_SESSION['Name']) ? $_SESSION['Name'] : ''; ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" style="background-color: #edebeb;" class="form-control" id="email" name="email" placeholder="name@gmail.com" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" readonly />
                                </div>
                                 <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Payment Method</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="payment_method" required>
                                            <option>Cash</option>
                                            <option>GCash</option>
                                            <option>PayMaya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Payment Type</label>
                                    <div class="col-sm-12">
                                        <input type="text" style="background-color: #edebeb;" class="form-control" name="payment_type" value="Full Payment" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
    <label for="exampleInputName1">Amount</label>
    <input type="number" style="background-color: #edebeb;" class="form-control" id="balance" placeholder="" name="balance" value="<?php echo isset($_SESSION['balance']) ? $_SESSION['balance'] : ''; ?>" readonly required />
    <?php
    // Check if $_SESSION['balance'] is empty or not set
    if (empty($_SESSION['balance'])) {
        echo "<p>Your bills are all settled. Thank You!</p>";
    }
    ?>
</div>
                                <div class="form-group">
                                    <label for="exampleInputName1">Reference code</label>
                                    <input type="text" class="form-control" id="reference" placeholder="***************" name="reference"  maxlength="13" required>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">File upload</label>
                                    <div class="col-sm-12">
                                        <div class="custom-file">
                                            <input type="file" class="dropify" id="customFile" name="img" required>
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-gradient-primary me-2" onclick="showSwal('Payment-Success')">Submit</button>
                              <button class="btn btn-light" onclick="window.location.href='../tenants/payment/payment.php'">Cancel</button>
                            </form>
                        </div>
                    </div>
    </div></div></div></div></div>
   

<div class="modal fade" id="Payment" tabindex="-1" role="dialog" aria-labelledby="termsAndAgreementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationTutorialModalLabel">How to Fill Out the Monthly payment form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Welcome! Follow these steps to complete the reservation form:</p>

                <ol>
                    <li><strong>Room Number:</strong> Choose the room number from the list.</li>
                    <li><strong>Full Name:</strong> Enter your complete name in the provided field.</li>
                    <li><strong>Email:</strong> Provide a valid email address for communication.</li>
                    <li><strong>Payment Method:</strong> Select your preferred payment method from the options.</li>
                    <li><strong>Payment Type:</strong> Monthly payment is pre-selected and readonly.</li>
                    <li><strong>Amount:</strong> Enter the amount for your reservation.</li>
                    <li><strong>Reference Code:</strong> Input a reference code for your payment.</li>
                        <div class="image-holder">
                            <img src="../assets/images/howto/reference.webp" class="img-fluid" alt="Reference Code Image">
                        </div>
                    <li><strong>Select Month:</strong> Choose the month for your payment using the date picker.</li>
                    <li><strong>File Upload:</strong> Attach the required file for your reservation.</li>
                </ol>

                <p>After completing the form, click the "Submit" button to send your reservation request. If needed, you can cancel by clicking the "Cancel" button.</p>

                <p>For any assistance, please contact our support team.</p>

                <div class="image-holder mt-3">
                    <img src="../assets/images/howto/Payment1.jpg" class="img-fluid" alt="Concluding Image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <!-- container-scroller -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>     
    <!-- plugins:js -->
    <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/vendor.bundle.base.js"></script>
    <script src="../../assets/js/misc.js"></script>
     <script src="../assets/js/sweetalert.min.js"></script>
     <script src="../assets/js/alerts.js"></script>
    <script src="../assets/js/jquery.inputmask.bundle.js"></script>
    <script src="../assets/js/inputmask.js"></script>
    <script src="../assets/js/dropify.min.js"></script>
    <script src="../assets/js/dropify.js"></script>
    <script>
    function fetchAmount(selectedMonthYear) {
        // AJAX request to fetch amount based on selected month and year
        $.ajax({
            url: 'fetch_amount.php', // Replace 'fetch_amount.php' with your actual PHP script
            type: 'POST',
            data: { selectedMonthYear: selectedMonthYear },
            success: function(response) {
                // Update the input field value with the fetched amount
                $('#rate').val(response.amount); // Assuming response contains 'amount' field
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Handle errors if needed
            }
        });
    }
</script>

     <script>
        $(document).ready(function(){
            $('.datepicker').datepicker({
                format: "mm/yyyy",
                startView: "months",
                minViewMode: "months",
                autoclose: true
            });
        });
    </script>
   
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
// Declare showQRCode function globally
    function showQRCode(paymentMethod) {
    // Hide both QR codes initially
    document.getElementById('gcashQRCode').style.display = 'none';
    document.getElementById('paymayaQRCode').style.display = 'none';

    // Show the selected QR code
        if (paymentMethod === 'gcash') {
            document.getElementById('gcashQRCode').style.display = 'block';
        } else if (paymentMethod === 'paymaya') {
            document.getElementById('paymayaQRCode').style.display = 'block';
        }
    }

document.addEventListener('DOMContentLoaded', function () {
        // Call the function to show the initial QR code (GCash)
        showQRCode('gcash');
    });
</script>
</body>
</html>