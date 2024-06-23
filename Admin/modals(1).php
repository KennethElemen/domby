<div class="modal fade" id="add-room">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Room Type</h5>
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
                <h5 class="modal-title">Tenant Details</h5>
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
                <h5 class="modal-title" id="exampleModalLabel">Edit Room Type</h5>
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




<!-- /.modal add-roomtype -->
<div class="modal fade" id="add-room_management">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Room Management</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
           <form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="roomNumber">Room Number</label>
                            <input type="number" class="form-control" name="roomNumber" placeholder="Room Number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="roomName">Room Name</label>
                            <input type="text" class="form-control" name="roomName" placeholder="Room Name" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" rows="2" required></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="numOfBeds">No. of Beds</label>
                            <input type="number" class="form-control" name="numOfBeds" placeholder="No. of Beds" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="images">Choose Images</label>
                            <input type="file" class="form-control" name="images[]" placeholder="Images" multiple required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ratePerNight">Rate per Month</label>
                            <input type="number" class="form-control" name="ratePerNight" placeholder="Rate per Month" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ratePerMonth">Rate per Night</label>
                            <input type="number" class="form-control" name="ratePerMonth" placeholder="Rate per Night" required>
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
                    <button type="submit" class="btn btn-primary" name="submitRoomForm">Submit</button>
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




<!-- /.modal edit-room_management -->
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
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
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
                                <label for="Description">Description</label>
                                <textarea class="form-control" id="Description" name="Description" rows="2"><?php echo $description; ?></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="num_of_beds">No. of Beds</label>
                                <input type="number" class="form-control" id="num_of_beds" name="num_of_beds" placeholder="No. of Beds" value="<?php echo $numOfBeds; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rate_per_night">Rate per Month</label>
                                <input type="number" class="form-control" id="rate_per_night" name="rate_per_night" placeholder="Rate per Month" value="<?php echo $ratePerNight; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rate_per_month">Rate per Night</label>
                                <input type="number" class="form-control" id="rate_per_month" name="rate_per_month" placeholder="Rate per Night" value="<?php echo $ratePerMonth; ?>">
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
                        <button type="submit" class="btn btn-primary" name="updateRoomManagement">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>




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
                <h5 class="modal-title" id="exampleModalLabel">Room Images</h5>
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
<!-- /.modal add-payment -->
<div class="modal fade" id="add-announcement">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Announcement</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>  
            <div class="modal-body">
                <form role="form" method="post" action="">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="AnnouncerName">Announcer's Name</label>
                                <input type="text" class="form-control" name="AnnouncerName" id="AnnouncerName" placeholder="Your Name" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Title">Announcement Title</label>
                                <input type="text" class="form-control" name="Title" id="announcement_title" placeholder="Title of Announcement">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="Content">Announcement Content</label>
                                <textarea class="form-control" name="Content" id="announcement_content" placeholder="Enter your announcement here"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="hidden" class="form-control" name="PublishDate" id="publish_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group col-md-12">
                                <input type="hidden" class="form-control" name="PublishTime" id="publish_time" value="<?php echo date('H:i:s'); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="showSuccessToast()">Submit Announcement</button>
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
                <h4 class="modal-title">Edit Tenant Info</h4>
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