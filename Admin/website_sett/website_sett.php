<?php
// Include your database connection file
include '../../includes/config/dbconn.php';

// Function to fetch data from the database
function getAboutData($conn) {
    $sql = "SELECT * FROM about";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return false;
    }
}

// Fetch data from the database
$aboutData = getAboutData($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve form data
  $dormName = $_POST['dormName'];
  $landlordName = $_POST['landlordName'];
  $email = $_POST['email'];
  $location = $_POST['location'];
  $contactNumber = $_POST['contactNumber'];
  $facebookLink = $_POST['facebookLink'];
  $facebookName = $_POST['facebookName'];
  $paymentNumber = $_POST['paymentNumber'];
  $paymentNumber2 = $_POST['paymentNumber2'];

 // Check if a file is uploaded
if ($_FILES['QRCode']['error'] === UPLOAD_ERR_OK) {
  $targetDirectory = '../Admin/uploads/';
  $qrCodePath = $targetDirectory . basename($_FILES['QRCode']['name']);

  // Create the target directory if it doesn't exist
  if (!is_dir($targetDirectory)) {
      mkdir($targetDirectory, 0755, true);
  }

  // Move the uploaded file to the desired directory
  if (move_uploaded_file($_FILES['QRCode']['tmp_name'], $qrCodePath)) {
      // File upload successful
  } else {
      echo "Error moving uploaded file.";
      exit();
  }
} else {
  // No new file uploaded, maintain the existing QR code
  $qrCodePath = isset($aboutData['QRCode']) ? $aboutData['QRCode'] : '';
}

if ($_FILES['QRCode2']['error'] === UPLOAD_ERR_OK) {
  $targetDirectory2 = '../Admin/uploads/';
  $qrCodePath2 = $targetDirectory2 . basename($_FILES['QRCode2']['name']);

  // Create the target directory if it doesn't exist
  if (!is_dir($targetDirectory2)) {
      mkdir($targetDirectory2, 0755, true);
  }

  // Move the uploaded file to the desired directory
  if (move_uploaded_file($_FILES['QRCode2']['tmp_name'], $qrCodePath2)) {
      // File upload successful
  } else {
      echo "Error moving uploaded file.";
      exit();
  }
} else {
  // No new file uploaded, maintain the existing QR code
  $qrCodePath2 = isset($aboutData['QRCode2']) ? $aboutData['QRCode2'] : '';
}



// Update data in the database
if ($aboutData) {
  // If record exists, update it
  $sql = "UPDATE about SET DormName='$dormName', LandlordName='$landlordName', email='$email', Location='$location', ContactNumber='$contactNumber', FacebookLink='$facebookLink', FacebookName='$facebookName', QRCode='$qrCodePath', PaymentNumber='$paymentNumber', QRCode2='$qrCodePath2', PaymentNumber2='$paymentNumber2'";
} else {
  // If record doesn't exist, insert a new one
  $sql = "INSERT INTO about (DormName, LandlordName, email, Location, ContactNumber, FacebookLink, FacebookName, QRCode, PaymentNumber, QRCode2, PaymentNumber2)
          VALUES ('$dormName', '$landlordName', '$email', '$location', '$contactNumber', '$facebookLink', '$facebookLink', '$qrCodePath', '$paymentNumber', '$qrCodePath2', '$paymentNumber2')";
}

if ($conn->query($sql) === TRUE) {
  header("Location: website_sett.php");
  // Refresh the data after update
  $aboutData = getAboutData($conn);
} else {
  echo "Error updating data: " . $conn->error;
}
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
            </div>
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Website Settings</h4>
                    <!-- Display data from the database in the form -->
                    <form class="forms-sample" action="website_sett.php" method="post" enctype="multipart/form-data">
                      <div class="form-group">
                        <label for="dormName">Dorm Name</label>
                        <input type="text" class="form-control" id="dormName" name="dormName" placeholder="Dorm Name" value="<?php echo isset($aboutData['DormName']) ? $aboutData['DormName'] : ''; ?>">
                      </div>
                      <div class="form-group">
                        <label for="landlordName">Landlord's Name</label>
                        <input type="text" class="form-control" id="landlordName" name="landlordName" placeholder="Full Name" value="<?php echo isset($aboutData['LandlordName']) ? $aboutData['LandlordName'] : ''; ?>">
                      </div>
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="email@gmail.com" value="<?php echo isset($aboutData['email']) ? $aboutData['email'] : ''; ?>">
                      </div>
                      <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Location" value="<?php echo isset($aboutData['Location']) ? $aboutData['Location'] : ''; ?>">
                      </div>
                      <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Contact No." value="<?php echo isset($aboutData['ContactNumber']) ? $aboutData['ContactNumber'] : ''; ?>">
                      </div>
                      <div class="form-group">
                        <label for="facebookLink">Facebook Link</label>
                        <input type="text" class="form-control" id="facebookLink" name="facebookLink" placeholder="https://facebook.com/" value="<?php echo isset($aboutData['FacebookLink']) ? $aboutData['FacebookLink'] : ''; ?>">
                      </div>
                      <div class="form-group">
                        <label for="facebookLink">Facebook Name</label>
                        <input type="text" class="form-control" id="facebookName" name="facebookName" placeholder="Facebook Name" value="<?php echo isset($aboutData['FacebookName']) ? $aboutData['FacebookName'] : ''; ?>">
                      </div>
                      <div class="form-group">
                          <label for="qrcode">QR Code</label>
                          <input type="file" class="form-control-file" id="qrcode" name="QRCode" accept=".png, .jpg, .jpeg">
                      </div>

    <!-- Display the uploaded image if it exists -->
    <?php if (isset($aboutData['QRCode'])): ?>
        <div class="form-group">
            <img src="<?php echo $aboutData['QRCode']; ?>" alt="Uploaded QR Code" style="width: 300px; height: 300px;">
        </div>
    <?php endif; ?>
                      <div class="form-group">
                          <label for="paymentNumber">Gcash Number</label>
                          <input type="text" class="form-control" id="paymentNumber" name="paymentNumber" placeholder="Number" value="<?php echo isset($aboutData['PaymentNumber']) ? $aboutData['PaymentNumber'] : ''; ?>">
                        </div>
                        <div class="form-group">
                          <label for="qrcode2">QR Code</label>
                          <input type="file" class="form-control-file" id="qrcode2" name="QRCode2" accept=".png, .jpg, .jpeg">
                      </div>

    <!-- Display the uploaded image if it exists -->
    <?php if (isset($aboutData['QRCode2'])): ?>
        <div class="form-group">
            <img src="<?php echo $aboutData['QRCode2']; ?>" alt="Uploaded QR Code" style="width: 300px; height: 300px;">
        </div>
    <?php endif; ?>
    <div class="form-group">
                          <label for="paymentNumber2">PayMaya Number</label>
                          <input type="text" class="form-control" id="paymentNumber2" name="paymentNumber2" placeholder="Number" value="<?php echo isset($aboutData['PaymentNumber2']) ? $aboutData['PaymentNumber2'] : ''; ?>">
                        </div>
                      <button type="submit" class="btn btn-primary"  onclick="showSuccessToast()" name="submit">UPDATE</button>
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
    
    <?php include '../modals.php'; ?>    
   <?php include '../scripts.php'; ?>
  </body>
</html>
