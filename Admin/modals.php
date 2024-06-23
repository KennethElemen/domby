
<div class="modal fade" id="add-room">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Add Room Type</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form for adding room types -->
        <form method="post" action="room_management.php">
          <div class="form-group">
            <label for="roomTypeName">Room Type Name:</label>
            <input type="text" class="form-control" id="roomTypeName" name="roomTypeName" required>
          </div>
          <button type="submit" class="btn btn-primary" name="submitForm">Add Room Type</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>


<!-- Add this modal after the existing HTML code -->
<div class="modal fade" id="tenantModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12">Tenant Details</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <td class="day-cell <?php echo ($day == date('j') && $month == date('n') && $year == date('Y')) ? 'selected-day' : ''; ?>">
                    <a href="#" class="day-button">
                        <?php echo $day; ?>
                        <?php foreach ($tenantDetails as $tenant): ?>
                            <div class="tenant-details">
                                <strong>Tenant Name:</strong> <?php echo $tenant['Name']; ?><br>
                                <strong>Check-in Date:</strong> <?php echo $tenant['check_in_date']; ?><br>
                                <strong>Check-out Date:</strong> <?php echo $tenant['check_out_date']; ?><br>
                                <strong>Room Number:</strong> <?php echo $tenant['room_number']; ?><br>
                            </div>
                        <?php endforeach; ?>
                    </a>
                </td>
            </div>
        </div>
    </div>
</div>


   <!-- /.modal edit-room-type -->
    <div class="modal fade" id="edit-room-type">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12" id="exampleModalLabel">Edit Room Type</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for editing room types -->
                <form method="post" action="room_management.php">
                        <!-- Hidden input for editRoomTypeId -->
                        <input type="hidden" id="editRoomTypeId" name="editRoomTypeId">

                        <div class="form-group">
                            <label for="roomTypeName">Room Type Name:</label>
                            <input type="text" class="form-control" id="roomTypeName" name="roomTypeName" value="" required>
                        </div>

                        <button type="submit" class="btn btn-primary" name="updateRoomType">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


<?php

// Establish database connection
$dbConnection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

// Room Management
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Form Submission for Room Management
    if (isset($_POST["roomNumber"])) {
        $roomNumber = mysqli_real_escape_string($dbConnection, $_POST["roomNumber"]);
        $roomName = mysqli_real_escape_string($dbConnection, $_POST["roomName"]);

        // Check if the room number already exists
        $checkRoomSql = "SELECT room_number FROM room_management WHERE room_number = '$roomNumber'";
        $checkRoomResult = mysqli_query($dbConnection, $checkRoomSql);

        if ($checkRoomResult && mysqli_num_rows($checkRoomResult) > 0) {
            $roomNumberError = "Room number already exists.";
        }

        // Check if the room name already exists
        $checkRoomNameSql = "SELECT room_name FROM room_management WHERE room_name = '$roomName'";
        $checkRoomNameResult = mysqli_query($dbConnection, $checkRoomNameSql);

        if ($checkRoomNameResult && mysqli_num_rows($checkRoomNameResult) > 0) {
            $roomNameError = "Room name already exists.";
        }

        if (!isset($roomNumberError) && !isset($roomNameError)) {
            // Your existing code for inserting into database goes here
            // If everything is successful, you can redirect or perform any other action
        }
    }
}

// Close database connection
mysqli_close($dbConnection);

?>

<?php

    // Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check for description length
    if (strlen($_POST["description"]) > 555) {
        // Redirect back to the form page with error message
         echo "<script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Description is too long!',
                      showConfirmButton: false,
                      timer: 3000
                    }).then(() => {
                        window.location.href = 'room_management.php';
                    });
                  </script>";
        exit;
    }

    // Establish database connection
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Form Submission for Room Management
    if (isset($_POST["roomNumber"])) {
        $roomNumber = mysqli_real_escape_string($dbConnection, $_POST["roomNumber"]);
        $roomName = mysqli_real_escape_string($dbConnection, $_POST["roomName"]);

        // Check if the room number already exists
        $checkRoomNumberSql = "SELECT room_number FROM room_management WHERE room_number = '$roomNumber'";
        $checkRoomNumberResult = mysqli_query($dbConnection, $checkRoomNumberSql);
        if ($checkRoomNumberResult && mysqli_num_rows($checkRoomNumberResult) > 0) {
              echo "<script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Room number already exists. Please try again with a different number.!',
                      showConfirmButton: false,
                      timer: 3000
                    }).then(() => {
                        window.location.href = 'room_management.php';
                    });
                  </script>";
            exit;
        }

       // Check if the room name already exists
$checkRoomNameSql = "SELECT room_name FROM room_management WHERE room_name = '$roomName'";
$checkRoomNameResult = mysqli_query($dbConnection, $checkRoomNameSql);
if ($checkRoomNameResult && mysqli_num_rows($checkRoomNameResult) > 0) {
    echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Room name already exists. Please try again with a different name!',
              showConfirmButton: false,
              timer: 3000
            }).then(() => {
                window.location.href = 'room_management.php';
            });
          </script>";
    exit;
}

// Validate uploaded images
$allowedTypes = array('image/jpeg', 'image/png');
$totalImages = count($_FILES['images']['name']);
$validImageCount = 0;

for ($i = 0; $i < $totalImages; $i++) {
    $fileType = $_FILES['images']['type'][$i];
    // Check if the file type is allowed
    if (in_array($fileType, $allowedTypes)) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            // Check file size
            if ($_FILES['images']['size'][$i] > 5242880) { // 5 MB (in bytes)
                echo "<script>
                        Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: 'File size is too large. Maximum allowed size is 5MB',
                          showConfirmButton: false,
                          timer: 3000
                        }).then(() => {
                            window.location.href = 'room_management.php';
                        });
                      </script>";
                exit;
            }
            $validImageCount++;
        }
    } else {
        echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Invalid file type. Only JPEG and PNG files are allowed',
                  showConfirmButton: false,
                  timer: 3000
                }).then(() => {
                    window.location.href = 'room_management.php';
                });
              </script>";
        exit;
    }
}

// Check if valid image count is within the range
if ($validImageCount < 3 || $validImageCount > 10) {
    echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Choose a minimum of 3 and a maximum of 10 images',
              showConfirmButton: false,
              timer: 3000
            }).then(() => {
                window.location.href = 'room_management.php';
            });
          </script>";
    exit;
}


        // All validations passed, proceed with database insertion
        $roomNumber = mysqli_real_escape_string($dbConnection, $_POST["roomNumber"]);
        $roomName = mysqli_real_escape_string($dbConnection, $_POST["roomName"]);
        $description = mysqli_real_escape_string($dbConnection, $_POST["description"]);
        $numOfBeds = mysqli_real_escape_string($dbConnection, $_POST["numOfBeds"]);
        $ratePerNight = mysqli_real_escape_string($dbConnection, $_POST["ratePerNight"]);
        $ratePerMonth = mysqli_real_escape_string($dbConnection, $_POST["ratePerMonth"]);
        $downPayment = mysqli_real_escape_string($dbConnection, $_POST["downPayment"]);
        $roomType = mysqli_real_escape_string($dbConnection, $_POST["roomType"]);
        $maxOccupants = mysqli_real_escape_string($dbConnection, $_POST["maxOccupants"]);

        // Handle image uploads
        $imagePaths = [];
        $uploadDirectory = "../uploads/";

        for ($i = 0; $i < $totalImages; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $imageName = $_FILES['images']['name'][$i];
                $imageTmpName = $_FILES['images']['tmp_name'][$i];
                $imagePath = $uploadDirectory . $imageName;

                if (move_uploaded_file($imageTmpName, $imagePath)) {
                    $imagePaths[] = $imagePath;
                } else {
                    // Handle upload error if needed
                    echo "<script>alert('Error uploading image: " . $_FILES["images"]["error"][$i] . "');</script>";
                    exit;
                }
            }
        }

        // Insert data into the database, including image paths
        $imagePathsStr = implode(",", $imagePaths);
        $insertSql = "INSERT INTO room_management (room_number, room_name, description, num_of_beds, rate_per_night, rate_per_month, down_payment, room_type, images, max_occupants) VALUES ('$roomNumber', '$roomName', '$description', '$numOfBeds', '$ratePerNight','$ratePerMonth', '$downPayment', '$roomType', '$imagePathsStr', '$maxOccupants')";
        $insertResult = mysqli_query($dbConnection, $insertSql);

        if ($insertResult) {
            // Show success SweetAlert and redirect to the room management page
            echo "<script>
                    Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: 'Room added successfully',
                      showConfirmButton: false,
                      timer: 1500
                    }).then(() => {
                        window.location.href = 'room_management.php';
                    });
                  </script>";
            exit;
        } else {
            // Show error SweetAlert
            echo "<script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'An error occurred!',
                      showConfirmButton: false,
                      timer: 1500
                    }).then(() => {
                        window.location.href = 'room_management.php';
                    });
                  </script>";
        }
    }
}

?>

<!-- /.modal add-roomtype -->
<div class="modal fade" id="add-room_management">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12">Add Room Management</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
          <form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                                <label for="roomNumber">Room Number</label>
                                <input type="number" class="form-control" name="roomNumber" id="roomNumber" placeholder="Room Number" required maxlength="4">
                               <ul class="list-arrow">
                                              <li>Choose a unique room number</li>
                                            </ul>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="roomName">Room Name</label>
                                <input type="text" class="form-control" name="roomName" id="roomName" placeholder="Room Name" required maxlength="30">
                                <ul class="list-arrow">
                                              <li>Choose a unique room name</li>
                                            </ul>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="2" required maxlength="355"></textarea>
                                  <div id="DescriptionError" class="text-danger"></div>
                            </div>
                        <div class="form-group col-md-6">
                            <label for="numOfBeds">No. of Beds</label>
                            <input type="number" class="form-control" name="numOfBeds" placeholder="No. of Beds" required>
                             
                        </div>
                        <div class="form-group col-md-6">
                            <label for="images">Choose Images</label>
                            <input type="file" class="form-control" name="images[]" placeholder="Images" multiple required>
                              <div id="FileError" class="text-danger"></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ratePerMonth">Rate per Month</label>
                            <input type="number" class="form-control" name="ratePerMonth" placeholder="Rate per Month" required>
                              
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ratePerNight">Rate per Night</label>
                            <input type="number" class="form-control" name="ratePerNight" placeholder="Rate per Night" required>
                             
                        </div>
                        <div class="form-group col-md-6">
                            <label for="downPayment">Down Payment</label>
                            <input type="number" class="form-control" name="downPayment" placeholder="Down Payment" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="maxOccupants">Max Occupants</label>
                            <input type="number" class="form-control" name="maxOccupants" placeholder="Max Occupants" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="roomType">Room Type</label>
                            <select class="form-control" name="roomType" required>
                                <?php
                                // Fetch room types from the database and generate options
                                $dbConnection = new mysqli($servername, $username, $password, $dbname);

                                // Check the connection
                                if ($dbConnection->connect_error) {
                                    die("Connection failed: " . $dbConnection->connect_error);
                                }

                                $sql = "SELECT name FROM room_types";
                                $result = mysqli_query($dbConnection, $sql);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                    }
                                } else {
                                    echo '<option>No room types found</option>';
                                }

                                mysqli_close($dbConnection);
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" id="addroom" class="btn btn-primary" name="submitRoomForm">Submit</button>
                    <button type="button" class="btn btn-default">Cancel</button>
                </div>
                <!-- /.card-body -->
            </form>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Function to validate description length
        document.getElementById("description").addEventListener("input", function() {
            var description = this.value;
            var descriptionError = document.getElementById("DescriptionError");
            if (description.length > 555) {
                descriptionError.textContent = "Description must be less than 255 characters";
                disableSubmitButton(true);
            } else {
                descriptionError.textContent = "";
                checkErrors(); // Call function to recheck for errors
            }
        });

        // Function to validate file upload
        document.querySelector('input[type="file"]').addEventListener("change", function() {
            var files = this.files;
            var imageFiles = [];
            var fileCount = 0;
            var fileError = document.getElementById("FileError");

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var fileType = file.type.split("/")[0];
                if (fileType === "image") {
                    imageFiles.push(file);
                }
                // Check file size
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    fileError.textContent = "File size exceeds 5MB limit";
                    disableSubmitButton(true);
                    return; // Exit function if file size exceeds limit
                }
            }

            fileCount = imageFiles.length;

            if (fileCount === 0) {
                fileError.textContent = "No image files selected";
                disableSubmitButton(true);
            } else if (fileCount < 3 || fileCount > 10) {
                fileError.textContent = "Choose a minimum of 3 and a maximum of 10 images";
                disableSubmitButton(true);
            } else if (files.length !== fileCount) {
                fileError.textContent = "Image only";
                disableSubmitButton(true);
            } else {
                fileError.textContent = "";
                checkErrors(); // Call function to recheck for errors
            }
        });

        // Function to disable or enable the submit button
        function disableSubmitButton(disable) {
            var submitButton = document.getElementById("addroom");
            submitButton.disabled = disable;
        }

        // Function to perform general form validation
        function checkErrors() {
            var description = document.getElementById("description").value;
            var files = document.querySelector('input[type="file"]').files;
            var imageFiles = [];
            var fileCount = 0;
            var descriptionError = document.getElementById("DescriptionError");
            var fileError = document.getElementById("FileError");

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var fileType = file.type.split("/")[0];
                if (fileType === "image") {
                    imageFiles.push(file);
                }
                // Check file size
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    fileError.textContent = "File size exceeds 5MB limit";
                    disableSubmitButton(true);
                    return; // Exit function if file size exceeds limit
                }
            }

            fileCount = imageFiles.length;

            if (description.length > 555) {
                descriptionError.textContent = "Description must be less than 255 characters";
                disableSubmitButton(true);
                return; // Exit function if there's an error
            }

            if (fileCount === 0) {
                fileError.textContent = "No image files selected";
                disableSubmitButton(true);
                return; // Exit function if there's an error
            } else if (fileCount < 3 || fileCount > 10) {
                fileError.textContent = "Choose a minimum of 3 and a maximum of 10 images";
                disableSubmitButton(true);
                return; // Exit function if there's an error
            } else if (files.length !== fileCount) {
                fileError.textContent = "Image only";
                disableSubmitButton(true);
                return; // Exit function if there's an error
            }

            // If no errors found, enable the submit button
            descriptionError.textContent = "";
            fileError.textContent = "";
            disableSubmitButton(false);
        }

        // Initial check for errors on page load
        checkErrors();

        // Recheck for errors whenever the form is submitted
        document.querySelector('form').addEventListener("submit", function(event) {
            checkErrors();
            if (document.getElementById("addroom").disabled) {
                event.preventDefault(); // Prevent form submission if submit button is disabled
            }
        });
    });
</script>





<?php
// Update the room management
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateRoomManagement"])) {
    // Check for description length
    if (strlen($_POST["description"]) > 555) {
        // Redirect back to the form page with error message using Swal
        echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Description is too long!',
                  showConfirmButton: false,
                  timer: 3000
                }).then(() => {
                    window.location.href = 'room_management.php';
                });
              </script>";
        exit;
    }

    // Establish database connection
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Retrieve form data
    $editRoomId = mysqli_real_escape_string($dbConnection, $_POST["editRoomId"]);
    $updatedRoomNumber = mysqli_real_escape_string($dbConnection, $_POST["room_number"]);
    $updatedRoomName = mysqli_real_escape_string($dbConnection, $_POST["room_name"]);
    $updatedDescription = mysqli_real_escape_string($dbConnection, $_POST["description"]);
    $updatedNumOfBeds = mysqli_real_escape_string($dbConnection, $_POST["num_of_beds"]);
    $updatedRatePerNight = mysqli_real_escape_string($dbConnection, $_POST["rate_per_night"]);
    $updatedRatePerMonth = mysqli_real_escape_string($dbConnection, $_POST["rate_per_month"]);
    $updatedDownPayment = mysqli_real_escape_string($dbConnection, $_POST["down_payment"]);
    $updatedRoomType = mysqli_real_escape_string($dbConnection, $_POST["room_type"]);

    // Check if the room number already exists
    $checkRoomNumberSql = "SELECT room_number FROM room_management WHERE room_number = '$updatedRoomNumber' AND roomID != $editRoomId";
    $checkRoomNumberResult = mysqli_query($dbConnection, $checkRoomNumberSql);
    if ($checkRoomNumberResult && mysqli_num_rows($checkRoomNumberResult) > 0) {
        // Room number already exists, show error message using Swal
        echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Room number already exists. Please try again with a different number.!',
                  showConfirmButton: false,
                  timer: 3000
                }).then(() => {
                    window.location.href = 'room_management.php';
                });
              </script>";
        exit;
    }

    // Check if the room name already exists
    $checkRoomNameSql = "SELECT room_name FROM room_management WHERE room_name = '$updatedRoomName' AND roomID != $editRoomId";
    $checkRoomNameResult = mysqli_query($dbConnection, $checkRoomNameSql);
    if ($checkRoomNameResult && mysqli_num_rows($checkRoomNameResult) > 0) {
        // Room name already exists, show error message using Swal
        echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Room name already exists. Please try again with a different name.!',
                  showConfirmButton: false,
                  timer: 3000
                }).then(() => {
                    window.location.href = 'room_management.php';
                });
              </script>";
        exit;
    }

    // Update the room details in the database
    $updateSql = "UPDATE room_management SET room_number = '$updatedRoomNumber', room_name = '$updatedRoomName', Description = '$updatedDescription', num_of_beds = '$updatedNumOfBeds', rate_per_night = '$updatedRatePerNight', rate_per_month = '$updatedRatePerMonth', down_payment = '$updatedDownPayment', room_type = '$updatedRoomType' WHERE roomID = $editRoomId";

    $updateResult = mysqli_query($dbConnection, $updateSql);

    if ($updateResult) {
        // Redirect to the same page to update the table on the website
        echo "<script>
                Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: 'Room details updated successfully',
                  showConfirmButton: false,
                  timer: 1500
                }).then(() => {
                    window.location.href = '{$_SERVER['PHP_SELF']}';
                });
              </script>";
        exit;
    } else {
        // Handle error if needed
        echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error updating room details',
                  showConfirmButton: false,
                  timer: 3000
                }).then(() => {
                    window.location.href = 'room_management.php';
                });
              </script>";
        exit;
    }
}

?>

<!-- /.modal edit-room_management -->
<div class="modal fade" id="edit-room_management">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12">Edit Room Management</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editRoomForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="room_number">Room Number</label>
                                <input type="text" class="form-control" id="room_number" name="room_number" placeholder="Room Number" value="<?php echo $roomNumber; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="room_name">Room Name</label>
                                <input type="text" class="form-control" id="room_name" name="room_name" placeholder="Room Name" value="<?php echo $roomName; ?>">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2"><?php echo $description; ?></textarea>
                                <span id="descriptionError" class="text-danger"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="num_of_beds">No. of Beds</label>
                                <input type="number" class="form-control" id="num_of_beds" name="num_of_beds" placeholder="No. of Beds" value="<?php echo $numOfBeds; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rate_per_night">Rate per Night</label>
                                <input type="number" class="form-control" id="rate_per_night" name="rate_per_night" placeholder="Rate per Night" value="<?php echo $ratePerNight; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rate_per_month">Rate per Month</label>
                                <input type="number" class="form-control" id="rate_per_month" name="rate_per_month" placeholder="Rate per Month" value="<?php echo $ratePerMonth; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="down_payment">Down Payment</label>
                                <input type="number" class="form-control" id="down_payment" name="down_payment" placeholder="Down Payment" value="<?php echo $downPayment; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="room_type">Room Type</label>
                                <select class="form-control" id="room_type" name="room_type">
                                    <?php
                                    // Fetch room types only once
                                    $dbConnection = new mysqli($servername, $username, $password, $dbname);

                                    // Check the connection
                                    if ($dbConnection->connect_error) {
                                        die("Connection failed: " . $dbConnection->connect_error);
                                    }

                                    $sql = "SELECT name FROM room_types";
                                    $result = mysqli_query($dbConnection, $sql);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option' . ($row['name'] == $roomType ? ' selected' : '') . '>' . $row['name'] . '</option>';
                                        }
                                    } else {
                                        echo '<option>No room types found</option>';
                                    }

                                    mysqli_close($dbConnection);
                                    ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="editRoomId" id="editRoomId" value="">
                        <button type="submit" class="btn btn-primary" id="updateRoomManagementBtn" name="updateRoomManagement">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Function to validate description length
        document.getElementById("description").addEventListener("input", function() {
            var description = this.value.trim();
            var descriptionError = document.getElementById("descriptionError");
            if (description.length > 555) {
                descriptionError.textContent = "Description must be less than 255 characters";
                disableSubmitButton(true);
            } else {
                descriptionError.textContent = "";
                checkErrors();
            }
        });

        // Function to disable or enable the submit button
        function disableSubmitButton(disable) {
            var submitButton = document.getElementById("updateRoomManagementBtn");
            submitButton.disabled = disable;
        }

        // Function to check for errors
        function checkErrors() {
            var descriptionError = document.getElementById("descriptionError").textContent;
            if (descriptionError === "") {
                disableSubmitButton(false);
            } else {
                disableSubmitButton(true);
            }
        }

        // Initial check for errors on page load
        checkErrors();
    });
</script>



<head>
    <style>
    .custom-img {
        padding: 20px;
        height: 300px;
        object-fit: cover;
        width: 100%; /* Ensure the width is 100% to maintain responsiveness */
        
    }

     .custom-nav-link {
        font-size: 18px; /* Adjust the font size as needed */
        transition: color 0.3s ease; /* Add a smooth transition effect */
    }

    .custom-nav-link:hover {
        color: purple; /* Change the hover color to purple */
        
    }
  

    </style>
</head>
 


    <div class="modal fade" id="viewRoomModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12" id="exampleModalLabel">Room Images</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 300px; text-align: center;">
                <div id="view-modal-images" class="carousel slide" data-ride="carousel" style="max-height: 300px;">
                    <div class="carousel-inner"></div>
                    <a class="carousel-control-prev" href="#view-modal-images" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#view-modal-images" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- /.modal view-room_management -->
<div class="modal fade" id="add-customer">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12">Add Tenant</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="Full Name">Name</label>
                            <input type="text" class="form-control" id="Full Name" placeholder="Full Name">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Contact Number">Contact Number</label>
                            <input type="text" class="form-control" id="Contact Number" placeholder="Contact Number">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Age">Age</label>
                            <input type="number" class="form-control" id="Age" placeholder="Age">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Full Name">Guardian</label>
                            <input type="text" class="form-control" id="Full Name" placeholder="Full Name">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Contact Number">Guardian's Contact Number</label>
                            <input type="text" class="form-control" id="Contact Number" placeholder="Contact Number">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Address">Address</label>
                            <input type="text" class="form-control" id="Address" placeholder="Address">
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal add-customer -->
<div class="modal fade" id="edit-customer">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Tenant Info</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="Full Name">Name</label>
                            <input type="text" class="form-control" id="Full Name" placeholder="Full Name">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Contact Number">Contact Number</label>
                            <input type="text" class="form-control" id="Contact Number" placeholder="Contact Number">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Age">Age</label>
                            <input type="number" class="form-control" id="Age" placeholder="Age">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Full Name">Guardian</label>
                            <input type="text" class="form-control" id="Full Name" placeholder="Full Name">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Contact Number">Guardian's Contact Number</label>
                            <input type="text" class="form-control" id="Contact Number" placeholder="Contact Number">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Address">Address</label>
                            <input type="text" class="form-control" id="Address" placeholder="Address">
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal edit-customer -->
<div class="modal fade" id="add-guardian">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Guardian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="First Name">Full Name</label>
                            <input type="text" class="form-control" id="First Name" placeholder="First Name">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Address">Address</label>
                            <input type="text" class="form-control" id="Address" placeholder="Address">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Contact">Contact</label>
                            <input type="number" class="form-control" id="Contact" placeholder="Contact">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Ward/Customer">Ward/Customer</label>
                            <select class="form-control">
                                    <option>Category1</option>
                                    <option>Category2</option>
                                    <option>Category3</option>
                                    <option>Category4</option>
                                </select>
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal add-guardian -->
<div class="modal fade" id="edit-guardian">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Adeditd Guardian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="First Name">Full Name</label>
                            <input type="text" class="form-control" id="First Name" placeholder="First Name">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Address">Address</label>
                            <input type="text" class="form-control" id="Address" placeholder="Address">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Contact">Contact</label>
                            <input type="number" class="form-control" id="Contact" placeholder="Contact">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Ward/Customer">Ward/Customer</label>
                            <select class="form-control">
                                    <option>Category1</option>
                                    <option>Category2</option>
                                    <option>Category3</option>
                                    <option>Category4</option>
                                </select>
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal add-guardian -->
<div class="modal fade" id="add-booking">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Booking</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                            <label for="Room">Room</label>
                            <select class="form-control">
                                    <option>Category 1</option>
                                    <option>Category 2</option>
                                    <option>Category 3</option>
                                    <option>Category 4</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Customer">Customer</label>
                            <select class="form-control">
                                    <option>Juliana</option>
                                    <option>Alexa</option>
                                    <option>Joe</option>
                                    <option>Corden</option>
                            </select>
                        </div>
                            <div class="form-group col-md-12">
                                <label for="Started Date">Started Date</label>
                                <input type="date" class="form-control" id="Started Date" placeholder="Started Date">
                            </div> 
                            <div class="form-group col-md-12">
                                <label for="End Date">End Date</label>
                                <input type="date" class="form-control" id="End Date" placeholder="End Date">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Total Amount">Total Amount</label>
                                <input type="number" class="form-control" id="Total Amount" placeholder="PHP">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Status">Status</label>
                                <input type="text" class="form-control" id="Status" placeholder="Status">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal add-booking -->
<div class="modal fade" id="edit-booking">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Booking</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="Room">Room</label>
                                <select class="form-control">
                                    <option>Category 1</option>
                                    <option>Category 2</option>
                                    <option>Category 3</option>
                                    <option>Category 4</option>
                                </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="Customer">Customer</label>
                            <select class="form-control">
                                    <option>Juliana</option>
                                    <option>Alexa</option>
                                    <option>Joe</option>
                                    <option>Corden</option>
                            </select>
                        </div>
                            <div class="form-group col-md-12">
                                <label for="Started Date">Started Date</label>
                                <input type="date" class="form-control" id="Started Date" placeholder="Started Date">
                            </div> 
                            <div class="form-group col-md-12">
                                <label for="End Date">End Date</label>
                                <input type="date" class="form-control" id="End Date" placeholder="End Date">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Total Amount">Total Amount</label>
                                <input type="number" class="form-control" id="Total Amount" placeholder="PHP">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Status">Status</label>
                                <input type="text" class="form-control" id="Status" placeholder="Status">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal edit-booking -->
<div class="modal fade" id="add-payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="Start Date">Start Date</label>
                            <input type="date" class="form-control" id="Start Date" placeholder="Start Date">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="End Date">End Date</label>
                            <input type="date" class="form-control" id="End Date" placeholder="End Date">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Payment Amount">Payment Amount</label>
                            <input type="number" class="form-control" id="Payment Amount" placeholder="PHP">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Discount">Discount</label>
                            <input type="number" class="form-control" id="Discount" placeholder="PHP">
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?php
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcerName = $_POST["AnnouncerName"];
    $title = $_POST["Title"];
    $content = $_POST["Content"];
    $publishDate = $_POST["PublishDate"];
    $publishTime = $_POST["PublishTime"];

    // Use prepared statements to prevent SQL injection
    $stmt = $connAnnouncements->prepare("INSERT INTO announcements (announcer_name, announcement_title, announcement_content, publish_date, publish_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $announcerName, $title, $content, $publishDate, $publishTime);

    // Execute the statement
    if ($stmt->execute()) {
        // Announcement added successfully
        // Display a success toast using jQuery
       echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Announcement added successfully!',
                    showConfirmButton: false,
                    timer: 3500
                });
              </script>";

        // Get the inserted announcement ID
        $announcementId = $stmt->insert_id;

        // Close the statement
        $stmt->close();

        // Get the ID of the last sent announcement from the session
        $lastSentAnnouncementId = isset($_SESSION['last_sent_announcement_id']) ? $_SESSION['last_sent_announcement_id'] : null;

        // Check if there is a new announcement since the last email was sent
        if ($lastSentAnnouncementId !== $announcementId) {
        // Email message content
        $emailSubject = "New Announcement";
        $emailBody = <<<HTML
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Contract Details">
    <style type="text/css">
        a:hover {
            text-decoration: underline !important;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f2f3f8; font-family: Arial, sans-serif;">
    <table cellspacing="0" align="center" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="font-family: Arial, sans-serif;">
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 670px; margin: 0 auto; background-color: #ffffff; border-radius: 3px; box-shadow: 0 6px 18px 0 rgba(0,0,0,.06);">
                    <tr>
                        <td style="height: 40px;"></td>
                    </tr>
                  
                            <td style="padding: 0 35px;">
                                <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px;">Announcemnt</h1>
                                <hr>
                                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                     Dear Tenant, 
                                </p>
                                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                  A new announcement has been added:
                                </p>
                                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                    <strong>Title:</strong> $title
                                </p>
                              
                                 <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                                    <strong>Content:</strong> $content
                                </p>
                                 <p>Please check it out.</p>
                                <p>Thank you.</p>
                                <center>
                                    <a href="https://dormbell.online/Guest/login.php" style="background-color: #20e277; text-decoration: none; display: inline-block; font-weight: 500; color: #ffffff; text-transform: uppercase; font-size: 14px; padding: 10px 24px; border-radius: 50px;">Log in</a>
                                </center>
                            </td>
                        </tr>


                        <td style="height: 40px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;



        // Get all tenant emails
        $tenantEmailsQuery = $connAnnouncements->query("SELECT email FROM tenantprofile");
        if ($tenantEmailsQuery->num_rows > 0) {
            while ($row = $tenantEmailsQuery->fetch_assoc()) {
                // Send email to each tenant using sendEmail function
                sendEmail($row["email"], $emailSubject, $emailBody);
            }
        }

        // Store the ID of the last sent announcement in the session
        $_SESSION['last_sent_announcement_id'] = $announcementId;
    }
     } else {
        // Error: Display an error Swal message
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error adding announcement: " . $stmt->error . "',
                    showConfirmButton: false,
                    timer: 3500
                });
              </script>";

        // Close the statement
        $stmt->close();
    }
}

?>

<div class="modal fade" id="add-announcement">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12">Add Announcement</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>  
            <div class="modal-body">
                <form role="form" method="post" action="" onsubmit="return validateAnnouncement()">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="AnnouncerName">Announcer's Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="AnnouncerName" id="AnnouncerName" placeholder="Your Name" required>
                                    <div class="input-group-append" id="announcer-clear" style="display: none;">
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearField('AnnouncerName')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="announcer-error" class="text-danger"></div><!-- Error message placeholder for Announcer's Name -->
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Title">Announcement Title</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="Title" id="announcement_title" placeholder="Title of Announcement">
                                    <div class="input-group-append" id="title-clear" style="display: none;">
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearField('announcement_title')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="title-error" class="text-danger"></div><!-- Error message placeholder for Announcement Title -->
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Content">Announcement Content</label>
                                <div class="input-group">
                                    <textarea class="form-control" name="Content" id="announcement_content" placeholder="Enter your announcement here"></textarea>
                                    <div class="input-group-append" id="content-clear" style="display: none;">
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearField('announcement_content')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="content-error" class="text-danger"></div><!-- Error message placeholder -->
                            </div>
                            <div class="form-group col-md-12">
                                <input type="hidden" class="form-control" name="PublishDate" id="publish_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group col-md-12">
                                <input type="hidden" class="form-control" name="PublishTime" id="publish_time" value="<?php echo date('H:i:s'); ?>">
                            </div>
                        </div>
                        <div id="error-container"></div><!-- Error container -->
                        <div class="mt-3"><!-- Add margin top for spacing -->
                            <button type="submit" class="btn btn-primary">Submit Announcement</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    document.getElementById('announcement_content').addEventListener('input', function() {
        validateAnnouncement();
    });

    document.getElementById('AnnouncerName').addEventListener('input', function() {
        validateAnnouncement();
    });

    document.getElementById('announcement_title').addEventListener('input', function() {
        validateAnnouncement();
    });

    function clearField(fieldId) {
        document.getElementById(fieldId).value = '';
    }

    function validateAnnouncement() {
        var announcerName = document.getElementById('AnnouncerName').value;
        var title = document.getElementById('announcement_title').value;
        var content = document.getElementById('announcement_content').value;

        var errors = [];

        // Validation for Announcer's Name
        if (announcerName.trim() === '') {
            document.getElementById('AnnouncerName').classList.add('is-invalid');
            errors.push("Please enter Announcer's Name");
            document.getElementById('announcer-clear').style.display = 'block'; // Show clear button
        } else if (announcerName.length > 50) {
            document.getElementById('AnnouncerName').classList.add('is-invalid');
            errors.push("Announcer's Name should be less than 50 characters");
            document.getElementById('announcer-clear').style.display = 'block'; // Show clear button
        } else {
            document.getElementById('AnnouncerName').classList.remove('is-invalid');
            document.getElementById('announcer-clear').style.display = 'none'; // Hide clear button
        }

        // Validation for Announcement Title
        if (title.trim() === '') {
            document.getElementById('announcement_title').classList.add('is-invalid');
            errors.push("Please enter Announcement Title");
            document.getElementById('title-clear').style.display = 'block'; // Show clear button
        } else if (title.length > 50) {
            document.getElementById('announcement_title').classList.add('is-invalid');
            errors.push("Announcement Title should be less than 50 characters");
            document.getElementById('title-clear').style.display = 'block'; // Show clear button
        } else {
            document.getElementById('announcement_title').classList.remove('is-invalid');
            document.getElementById('title-clear').style.display = 'none'; // Hide clear button
        }

        // Validation for Announcement Content
        if (content.length > 755) {
            document.getElementById('announcement_content').classList.add('is-invalid');
            errors.push('Announcement content too long.');
            document.getElementById('content-clear').style.display = 'block'; // Show clear button
        } else {
            document.getElementById('announcement_content').classList.remove('is-invalid');
            document.getElementById('content-clear').style.display = 'none'; // Hide clear button
        }

        // Displaying errors
        var errorContainer = document.getElementById('error-container');
        errorContainer.innerHTML = ''; // Clear previous errors

        if (errors.length > 0) {
            for (var i = 0; i < errors.length; i++) {
                var errorDiv = document.createElement('div');
                errorDiv.classList.add('text-danger');
                errorDiv.textContent = errors[i];
                errorContainer.appendChild(errorDiv);
            }
            return false; // Prevent form submission
        } else {
            return true; // Allow form submission
        }
    }
</script>





<div class="modal fade" id="edit-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-12">Edit Tenant Info</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                            <input class="form-control" placeholder="Full Name" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tenant Type</label>
                            <div class="col-sm-4">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="radio" class="form-check-input" name="membershipRadios" id="membershipRadios1" value="" checked> Regular </label>
                              </div>
                            </div>
                            <div class="col-sm-5">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="radio" class="form-check-input" name="membershipRadios" id="membershipRadios2" value="option2"> Transient </label>
                              </div>
                            </div>
                          </div>
                        </div>          
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Room Type</label>
                            <div class="col-sm-9">
                            <input class="form-control" placeholder="Room Type" />
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Start Date</label>
                            <div class="col-sm-4">
                            <input type="date" id="start-date" name="start-date">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">End Date</label>
                            <div class="col-sm-4">
                            <input type="date" id="end-date" name="end-date">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Gender</label>
                            <div class="col-sm-9">
                              <select class="form-control">
                                <option>Male</option>
                                <option>Female</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Age</label>
                            <div class="col-sm-9">
                              <input class="form-control" placeholder="Age" />
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9">
                            <input class="form-control" placeholder="Address" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Contact Number</label>
                            <div class="col-sm-9">
                            <input class="form-control" placeholder="Contact Number" />
                            </div>
                          </div>
                        </div>
                      </div>
                      <p class="card-description"> Emergency Contact </p>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                            <input class="form-control" placeholder="Full Name" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Contact Number</label>
                            <div class="col-sm-9">
                            <input class="form-control" placeholder="Emergency Number" />
                            </div>
                          </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> 
                    </div>
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal edit-user -->