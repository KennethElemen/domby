<div class="modal fade" id="add-roomtype">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Room Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="Room Type">Room Type</label>
                            <input type="text" class="form-control" id="Room Type" placeholder="Room Type">
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
<!-- /.modal add-roomtype -->
<div class="modal fade" id="edit-roomtype">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Room Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="Room Type">Room Type</label>
                            <input type="text" class="form-control" id="Room Type" placeholder="Room Type">
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
<!-- /.modal edit-roomtype -->
<div class="modal fade" id="add-payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">CREATE PAYMENT</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
            
                    <div class="card-body">
                           
                            <?php
                            

                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $roomNumber = $_POST['room_number'];
                                $fullName = $_POST['full_name'];
                                $email = $_POST['email'];
                                $amount = $_POST['amount'];
                                $paymentMethod = $_POST['payment_method'];
                                $paymentType = $_POST['payment_type'];

                                $targetDir = "../uploads/";
                                $targetFile = $targetDir . basename($_FILES['img']['name']);
                                $uploadOk = 1;
                                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                                if (file_exists($targetFile)) {
                                    echo "Sorry, file already exists.";
                                    $uploadOk = 0;
                                }

                                if ($_FILES['img']['size'] > 500000000) {
                                    echo "Sorry, your file is too large.";
                                    $uploadOk = 0;
                                }

                                if ($imageFileType !== 'jpg' && $imageFileType !== 'png' && $imageFileType !== 'jpeg') {
                                    echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
                                    $uploadOk = 0;
                                }

                                if ($uploadOk === 0) {
                                    echo "Sorry, your file was not uploaded.";
                                } else {
                                    if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                                        echo "The file " . htmlspecialchars(basename($_FILES['img']['name'])) . " has been uploaded.";

                                        $imageData = base64_encode(file_get_contents($targetFile));
                                        include '../../includes/config/dbconn.php';

                                            // Log received data
                                            

                                            $conn = new mysqli($servername, $username, $password, $dbname);

                                            // Check the connection
                                            if ($conn->connect_error) {
                                                die("Connection failed: " . $conn->connect_error);
                                            }

                                        $sql = "INSERT INTO payment (RoomNumber, Name, EmailID, Amount, PaymentMethod, PaymentType, ProofOfPayment, Status)
                                                VALUES (?, ?, ?, ?, ?, ?, ?, '')";

                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("sssssss", $roomNumber, $fullName, $email, $amount, $paymentMethod, $paymentType, $imageData);

                                        if ($stmt->execute()) {
                                         header("Location: ../errorpage/paymentSuccess.php");
                                        } else {
                                            echo "Error: " . $stmt->error;
                                        }

                                        $stmt->close();
                                        $conn->close();
                                    } else {
                                        echo "Sorry, there was an error uploading your file.";
                                    }
                                }
                            }
                            ?>
                                <?php
                            // Database connection parameters
                         
                            $dbConnection = new mysqli($servername, $username, $password, $dbname);

                            // Check the connection
                            if ($dbConnection->connect_error) {
                                die("Connection failed: " . $dbConnection->connect_error);
                            }

                            // SQL query to retrieve room numbers from the 'room_management' table
                            $sql = "SELECT room_number FROM room_management";
                            $result = $dbConnection->query($sql);

                            // Check for errors in SQL query
                            if (!$result) {
                                die("Error in SQL query: " . $dbConnection->error);
                            }

                            // Initialize an empty array to store room numbers
                            $room_numbers = [];

                            // If there are results, fetch and add room numbers to the array
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $room_numbers[] = $row['room_number'];
                                }
                            }

                            // Close the database connection
                            $dbConnection->close();
                        ?>
                            <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                          <label for="room_number" style="color: black;">Room Number</label>
                            <select class="selectpicker form-control" data-live-search="true" id="room_number" name="room_number" required>
                                <?php
                                    // Get the selected room number if it's already set
                                    $selected_room = isset($_POST['room_number']) ? $_POST['room_number'] : '';

                                    // Loop through room numbers to generate options
                                    foreach ($room_numbers as $room) {
                                        $selected = ($room == $selected_room) ? 'selected' : '';
                                        echo "<option value=\"$room\" $selected>$room</option>";
                                    }
                                ?>
                            </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName1">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" placeholder="Full Name" name="full_name">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName1">Email</label>
                                    <input type="text" class="form-control" id="email" placeholder="Email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName1">Amount</label>
                                    <input type="number" class="form-control" id="amount" placeholder="" name="amount">
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Payment Method</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="payment_method">
                                            <option>Cash</option>
                                            <option>Online</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Payment Type</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="payment_type">
                                            <option>Monthly Payment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">File upload</label>
                                    <div class="col-sm-12">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="img">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                <button class="btn btn-light">Cancel</button>
                            </form>
                        </div>
                    <!-- /.card-body -->
                    
           
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal add-room_management -->
<div class="modal fade" id="edit-room_management">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Room Management</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                       <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="Room Number">Room Number</label>
                                <input type="text" class="form-control" id="Room Number" placeholder="Room Number">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Room Name">Room Name</label>
                                <input type="text" class="form-control" id="Room Name" placeholder="Room Name">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Description">Description</label>
                                <textarea class="form-control" id="Description" rows="2"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="No. of Beds">No. of Beds</label>
                                <input type="number" class="form-control" id="No. of Beds" placeholder="No. of Beds">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Images">Choose Images</label>
                                <input type="file" class="form-control" id="Images" placeholder="Images" multiple>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Rate per Night">Rate per Night</label>
                                <input type="number" class="form-control" id="Rate per Night" placeholder="Rate per Night">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Room Type">Room Type</label>
                                <select class="form-control">
                                    <option>Category1</option>
                                    <option>Category2</option>
                                    <option>Category3</option>
                                    <option>Category4</option>
                                </select>
                            </div>
                            </div>
                        <button type="submit" class="btn btn-primary ">Submit</button>
                        <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button> 
                        
                    </div>
                    <!-- /.card-body -->
                    
                </form>
              </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal edit-room_management -->
<div class="modal fade" id="view-room_management">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Room Management Images</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="w3-content" style="max-width:800px">
                        <img class="mySlides" src="../../pages/images/aae2fff8fff75e8aa26c1306f2425244012897b6.jpg" style="width:100%">
                        <img class="mySlides" src="../../pages/images/download.jfif" style="width:100%">
                        <img class="mySlides" src="../../pages/images/1573116959_guestroom-mob_2.jpg" style="width:100%">
                    </div>

                    <div class="w3-center">
                        <div class="w3-section">
                            <button class="w3-button w3-light-grey" onclick="plusDivs(-1)">❮ Prev</button>
                            <button class="w3-button w3-light-grey" onclick="plusDivs(1)">Next ❯</button>
                        </div>
                        <button class="w3-button demo" onclick="currentDiv(1)">1</button> 
                        <button class="w3-button demo" onclick="currentDiv(2)">2</button> 
                        <button class="w3-button demo" onclick="currentDiv(3)">3</button> 
                    </div>
                    <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal view-room_management -->
<div class="modal fade" id="view-proof-of-payment">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Proof of Payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="w3-content" style="max-width:800px">
                        <img class="mySlides" src="assets/images/res/proof.png" style="width:100%">
                    </div>
                    <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="add-customer">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Tenant</h4>
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
<div class="modal fade" id="add-payment">
    <div class="modal-dialog modal-md">
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
                        <div class="form-group col-md-6">
                            <label for="First Name">Room Number</label>
                            <input type="text" class="form-control" id="First Name" placeholder="Room Number">
                        </div>
                            <div class="form-group col-md-6">
                            <label for="Address">Name</label>
                            <input type="text" class="form-control" id="Address" placeholder="Name">
                        </div>
                            <div class="form-group col-md-6">
                            <label for="Contact">Payment Method</label>
                            <select class="form-control">
                                    <option>Offline</option>
                                    <option>Online</option>
                                </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Address">Amount</label>
                            <input type="text" class="form-control" id="Address" placeholder="Amount">
                        </div>
                        <div class="form-group col-md-6">
                                <label for="Images">Choose Images</label>
                                <input type="file" class="form-control" id="Images" placeholder="Images" multiple>
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
<!-- /.modal add-payment -->
<div class="modal fade" id="add-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add User</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-md-12">
                            <label for="User Name">User Name</label>
                            <input type="text" class="form-control" id="User Name" placeholder="User Name">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Password">Password</label>
                            <input type="text" class="form-control" id="Password" placeholder="Password">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Contact Name">Name</label>
                            <input type="text" class="form-control" id="Contact Name" placeholder="Name">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Contact Number">Contact Number</label>
                            <input type="text" class="form-control" id="Contact Number" placeholder="Contact Number">
                        </div>
                            <div class="form-group col-md-12">
                            <label for="Account Type">Account Type</label>
                            <input type="text" class="form-control" id="Account Type" placeholder="Account Type">
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
<!-- /.modal add-user -->



<div class="modal fade" id="edit-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit User Info</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-sample" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
                                <label for="roomNumber">Room Number</label>
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
                               <select class="form-control" id="Gender" name="gender">
                                    <option <?php echo (isset($_SESSION['gender']) && $_SESSION['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option <?php echo (isset($_SESSION['gender']) && $_SESSION['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" class="form-control" id="age" name="age" placeholder="Age" value="<?php echo isset($_SESSION['age']) ? $_SESSION['age'] : ''; ?>" />
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
                                <input type="text" class="form-control" id="Address" name="Address" placeholder="Address" value="<?php echo isset($_SESSION['Address']) ? $_SESSION['Address'] : ''; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contactNumber">Contact Number</label>
                                <input type="number" class="form-control" id="ContactNumber" name="ContactNumber" placeholder="Contact Number" value="<?php echo isset($_SESSION['ContactNumber']) ? $_SESSION['ContactNumber'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Payment" style="color: black;">Payment</label>
                                <!-- Hidden input field to submit $rate -->
                                <input type="hidden" name="rate" value="<?php echo isset($rate) ? $rate : ''; ?>">
                                <!-- Display $rate value for reference -->
                                <input type="number" class="form-control" id="rate_per_night" name="Rate Per month" value="<?php echo isset($rate) ? $rate : ''; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emergencyContactName">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="GuardianName" name="GuardianName" placeholder="GuardianName" value="<?php echo isset($_SESSION['GuardianName']) ? $_SESSION['GuardianName'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emergencyNumber">Emergency Number</label>
                                <input type="number" class="form-control" id="EmergencyNumber" name="EmergencyNumber" placeholder="Emergency Number" value="<?php echo isset($_SESSION['EmergencyNumber']) ? $_SESSION['EmergencyNumber'] : ''; ?>" />
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">UPDATE</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<!-- /.modal edit-user -->

<!-- /.modal edit-user -->