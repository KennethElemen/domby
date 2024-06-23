
    
    <?php
    // Database connection details
    include '../../includes/config/dbconn.php';

    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Room Type Management
    $tableRows = '';




  

    // Display Room Types
    $sqlRoomTypes = "SELECT * FROM room_types";
    $resultRoomTypes = mysqli_query($dbConnection, $sqlRoomTypes);

    if ($resultRoomTypes && mysqli_num_rows($resultRoomTypes) > 0) {
    while ($rowRoomType = mysqli_fetch_assoc($resultRoomTypes)) {
        $tableRows .= '<tr>';
        $tableRows .= '<td>' . $rowRoomType['name'] . '</td>';
        $tableRows .= '<td>';
        $tableRows .= '<button type="button" class="btn btn-primary btn-sm edit-btn mr-2" data-toggle="modal" data-target="#edit-room-type" data-room-id="' . $rowRoomType['id'] . '">Edit</button>';
        $tableRows .= '<button type="button" class="btn btn-danger btn-sm delete-btn" data-room-id="' . $rowRoomType['id'] . '">Delete</button>';
        $tableRows .= '</td>';
        $tableRows .= '</tr>';
    }
} else {
    $tableRows .= '<tr><td colspan="2">No room types found</td></tr>';
}

   
    ?>

    <!DOCTYPE html>
        <html lang="en">
        <?php include '../head.php'; ?>


        <head>
             <script src="../../assets/js/sweetalert.min.js"></script>
        <style>
        .custom-img {
            padding: 20px;
            height: 300px;
            object-fit: cover; /* Adjust the object-fit property as needed */
            max-width: 300px; /* Ensure the image does not exceed its container */
        }

        .custom-nav-link {
            font-size: 1.5rem; /* Adjust the font size using rem units for better responsiveness */
            transition: color 0.3s ease;
        }

        .custom-nav-link:hover {
            color: purple;
        }


        </style>
        </head>
        <body>
          

        <div class="container-scroller">
            <!-- partial:../../partials/_navbar.html -->
            <?php include '../topbar.php'; ?>
            <!-- partial -->

            <div class="container-fluid page-body-wrapper">
            <!-- partial:../../partials/_sidebar.html -->
            <?php include '../sidebar.php'; ?>
            <!-- partial -->

            <div class="main-panel">
                  <div id="error-message" style="color: red; font-weight: bold;"></div>
                <div class="content-wrapper">
                <div class="page-header">
                    <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" data-toggle="modal" data-target="#add-room_management">
                    <i class="mdi mdi-plus"></i>Add Room
                    </button>
                </div>

            <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h1 class="mb-12">Room Management<ul class="list-arrow">
                                                <li>Add a room here and it will display in the available rooms</li>
                                                </ul></h1>
                                    <div class="table-responsive">
                                        <table class="table" id="example3" style="font-size:10px; width: 100%;">
                                        <?php
                           // Room Management - Delete Record
                                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteRoomId"])) {
                                    // Step 1: Get the ID of the record to be deleted
                                    $deleteRoomId = mysqli_real_escape_string($dbConnection, $_POST["deleteRoomId"]);

                                    // Step 2: Retrieve the file paths from the database for the record
                                    $selectSql = "SELECT images FROM room_management WHERE roomID = '$deleteRoomId'";
                                    $selectResult = mysqli_query($dbConnection, $selectSql);

                                    if ($selectResult && $row = mysqli_fetch_assoc($selectResult)) {
                                        $imagePaths = explode(",", $row["images"]);

                                        // Step 3: Delete the record from the database
                                        $deleteSql = "DELETE FROM room_management WHERE roomID = '$deleteRoomId'";
                                        $deleteResult = mysqli_query($dbConnection, $deleteSql);

                                        if ($deleteResult) {
                                            // Step 4: Delete the associated files from the uploads folder
                                            foreach ($imagePaths as $imagePath) {
                                                $fullImagePath = "../uploads/" . $imagePath;
                                                if (file_exists($fullImagePath)) {
                                                    unlink($fullImagePath);
                                                }
                                            }

                                            // Show success Swal message and redirect
                                            echo "<script>
                                                    Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success',
                                                    text: 'Record deleted successfully',
                                                    showConfirmButton: false,
                                                    timer: 1500
                                                    }).then(() => {
                                                        window.location.href = '{$_SERVER['PHP_SELF']}';
                                                    });
                                                </script>";
                                            exit;
                                        } else {
                                            // Show error Swal message
                                            echo "<script>
                                                    Swal.fire({
                                                    icon: 'error',
                                                    title: 'Oops...',
                                                    text: 'Error deleting record',
                                                    showConfirmButton: false,
                                                    timer: 3000
                                                    }).then(() => {
                                                        window.location.href = '{$_SERVER['PHP_SELF']}';
                                                    });
                                                </script>";
                                            exit;
                                        }
                                    } else {
                                        // Show error Swal message
                                        echo "<script>
                                                Swal.fire({
                                                icon: 'error',
                                                title: 'Oops...',
                                                text: 'Error retrieving record',
                                                showConfirmButton: false,
                                                timer: 3000
                                                }).then(() => {
                                                    window.location.href = '{$_SERVER['PHP_SELF']}';
                                                });
                                            </script>";
                                        exit;
                                    }
                                }

                                        ?>
                                            <!-- Table Header -->
                                            <thead>
                                                <tr>
                                                    <th>Room Number</th>
                                                    <th>Room Name</th>
                                                    <th>Description</th>
                                                    <th>Images</th>
                                                    <th>Rate Per Night</th>
                                                    <th>Rate Per Month</th>
                                                    <th>Down Payment</th>
                                                    <th>Room Type</th>
                                                    <th>No. of Beds</th>
                                                    <th>Occupants</th>
                                                    <th>Action</th>
                                                
                                                </tr>
                                            </thead>
                                            <!-- Table Body -->
                                            <tbody>
                                                <?php
                                                    // Fetch room management entries from the database and display them in the table
                                                    $dbConnection = new mysqli($servername, $username, $password, $dbname);

                                                    // Check the connection
                                                    if ($dbConnection->connect_error) {
                                                        die("Connection failed: " . $dbConnection->connect_error);
                                                    }

                                                    $sql = "SELECT * FROM room_management";
                                                    $result = mysqli_query($dbConnection, $sql);

                                                    if ($result && mysqli_num_rows($result) > 0) {
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            $roomNumber = $row['room_number'];

                                                            // Fetch the count of accepted reservations for the current room
                                                            $reservationSql = "SELECT COUNT(*) as current_occupants FROM reservations WHERE room_number = '$roomNumber' AND status = 'accepted'";
                                                            $reservationResult = mysqli_query($dbConnection, $reservationSql);
                                                            $currentOccupants = 0;

                                                            if ($reservationResult && mysqli_num_rows($reservationResult) > 0) {
                                                                $reservationData = mysqli_fetch_assoc($reservationResult);
                                                                $currentOccupants = $reservationData['current_occupants'];
                                                            }

                                                            // Calculate available occupants
                                                            $availableOccupants = $row['max_occupants'] - $currentOccupants;

                                                            // Display only a portion of the description
                                                            $shortDescription = strlen($row['description']) > 24 ? substr($row['description'], 0, 24) . '...' : $row['description'];

                                                            echo '<tr>';
                                                            echo '<td>' . $row['room_number'] . '</td>';
                                                            echo '<td>' . $row['room_name'] . '</td>';
                                                            echo '<td>';
                                                            echo $shortDescription;

                                                            // Display "more" link and modal for long descriptions
                                                            if (strlen($row['description']) > 24) {
                                                                echo ' <a href="#" data-toggle="modal" data-target="#descriptionModal' . $row['roomID'] . '">more</a>';
                                                                echo '<div class="modal fade" id="descriptionModal' . $row['roomID'] . '" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">';
                                                                echo '<div class="modal-dialog" role="document">';
                                                                echo '<div class="modal-content" style="max-width: 700px;">';
                                                                echo '<div class="modal-header">';
                                                                echo '<h5 class="modal-title" id="descriptionModalLabel">Room Description</h5>';
                                                                echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                                                echo '<span aria-hidden="true">&times;</span>';
                                                                echo '</button>';
                                                                echo '</div>';
                                                                echo '<div class="modal-body" style="word-wrap: break-word;">'; // Apply word-wrap CSS property
                                                                // Apply the Bootstrap text-wrap class to ensure long words wrap
                                                                echo '<p class="text-wrap">' . nl2br($row['description']) . '</p>';
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                                                                echo '</div>';
                                                                echo '</div>';
                                                                echo '</div>';
                                                                echo '</div>';
                                                            }


                                                            echo '</td>';
                                                        
                                                            echo '<td>';
                                                            echo '<button type="button" class="btn btn-success btn-sm view-btn" onclick="viewRoom(' . $row['roomID'] . ', \'' . $row['images'] . '\', \'' . $row['room_name'] . '\')">View</button>';
                                                            echo '</td>';
                                                            echo '<td>' . $row['rate_per_night'] . '</td>';
                                                            echo '<td>' . $row['rate_per_month'] . '</td>';
                                                            echo '<td>' . $row['down_payment'] . '</td>';
                                                            echo '<td>' . $row['room_type'] . '</td>';
                                                            echo '<td>' . $row['num_of_beds'] . '</td>';

                                                            // Inside the while loop where you display room management entries
                                                            echo '<td>';
                                                            echo '<span class="badge badge-' . ($availableOccupants > 0 ? 'success' : 'danger') . '" data-toggle="modal" data-target="#occupantsModal' . $row['roomID'] . '">' . $currentOccupants . '/' . $row['max_occupants'] . '</span>';
                                                            echo '<div class="modal fade" id="occupantsModal' . $row['roomID'] . '" tabindex="-1" role="dialog" aria-labelledby="occupantsModalLabel" aria-hidden="true">';
                                                            echo '<div class="modal-dialog" role="document">';
                                                            echo '<div class="modal-content">';
                                                            echo '<div class="modal-header">';
                                                            echo '<h5 class="modal-title" id="occupantsModalLabel">Occupants for Room ' . $row['room_number'] . '</h5>';
                                                            echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                                            echo '<span aria-hidden="true">&times;</span>';
                                                            echo '</button>';
                                                            echo '</div>';
                                                            echo '<div class="modal-body">';

                                                            // Fetch and display tenant information
                                                            $tenantSql = "SELECT Email FROM reservations WHERE room_number = '$roomNumber' AND status = 'accepted'";
                                                            $tenantResult = mysqli_query($dbConnection, $tenantSql);

                                                            if ($tenantResult && mysqli_num_rows($tenantResult) > 0) {
                                                                echo '<ul>';
                                                                while ($tenantRow = mysqli_fetch_assoc($tenantResult)) {
                                                                    echo '<li>' . $tenantRow['Email'] . '</li>';
                                                                }
                                                                echo '</ul>';
                                                            } else {
                                                                echo 'No occupants in this room.';
                                                            }

                                                            echo '</div>';
                                                            echo '<div class="modal-footer">';
                                                            echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                                                            echo '</div>';
                                                            echo '</div>';
                                                            echo '</div>';
                                                            echo '</div>';
                                                            echo '</td>';
                                                            echo '<td>';
                                                            echo '<button type="button" class="btn btn-primary btn-sm edit-room-btn mr-2" data-toggle="modal" data-target="#edit-room_management" data-room-id="' . $row['roomID'] . '">Edit</button>';

                                                            echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" class="d-inline-block" style="display:inline;">';
                                                            echo '<input type="hidden" name="deleteRoomId" value="' . $row['roomID'] . '">';
                                                            echo '<button type="submit" class="btn btn-danger btn-sm">Delete</button>';
                                                            echo '</form>';
                                                            echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="9">No room management entries found</td></tr>';
                                                    }

                                                   
                                                    ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="page-header">
                        <!-- Add Room Type Button -->
                        <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" data-toggle="modal" data-target="#add-room">
                            <i class="mdi mdi-plus"></i>Add Room Type
                        </button>
                        
                    </div>
                       <?php
                    // Add Room Type
                    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submitForm"])) {
                        if (isset($_POST["roomTypeName"])) {
                            $newRoomTypeName = mysqli_real_escape_string($dbConnection, $_POST["roomTypeName"]);

                            // Check if the room type name already exists
                            $checkRoomTypeSql = "SELECT * FROM room_types WHERE name = '$newRoomTypeName'";
                            $checkRoomTypeResult = mysqli_query($dbConnection, $checkRoomTypeSql);

                            if ($checkRoomTypeResult && mysqli_num_rows($checkRoomTypeResult) > 0) {
                                // Room type name already exists, show error message using Swal
                                echo "<script>
                                        Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Room type already exists. Please try again with a different name.!',
                                        showConfirmButton: false,
                                        timer: 3000
                                        }).then(() => {
                                            window.location.href = '{$_SERVER['PHP_SELF']}';
                                        });
                                    </script>";
                                exit;
                            }

                            // Insert the new room type into the database
                            $sql = "INSERT INTO room_types (name) VALUES (?)";
                            $stmt = mysqli_prepare($dbConnection, $sql);

                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, "s", $newRoomTypeName);
                                $result = mysqli_stmt_execute($stmt);

                                if ($result) {
                                    // Show success Swal message and redirect
                                    echo "<script>
                                            Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: 'Room type added successfully',
                                            showConfirmButton: false,
                                            timer: 1500
                                            }).then(() => {
                                                window.location.href = '{$_SERVER['PHP_SELF']}';
                                            });
                                        </script>";
                                    exit;
                                } else {
                                    // Show error Swal message
                                    echo "<script>
                                            Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Error adding room type',
                                            showConfirmButton: false,
                                            timer: 3000
                                            }).then(() => {
                                                window.location.href = '{$_SERVER['PHP_SELF']}';
                                            });
                                        </script>";
                                    exit;
                                }

                                mysqli_stmt_close($stmt);
                            } else {
                                // Show error Swal message
                                echo "<script>
                                        Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Error preparing statement',
                                        showConfirmButton: false,
                                        timer: 3000
                                        }).then(() => {
                                            window.location.href = '{$_SERVER['PHP_SELF']}';
                                        });
                                    </script>";
                                exit;
                            }
                        }
                    }
                    
                 // Delete Room Type
                    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["deleteRoomType"])) {
                        $deleteRoomTypeId = mysqli_real_escape_string($dbConnection, $_GET["deleteRoomType"]);

                        // Check if the room type is associated with any rooms
                        $checkRoomsSql = "SELECT * FROM room_management WHERE room_type = '$deleteRoomTypeId'";
                        $checkRoomsResult = mysqli_query($dbConnection, $checkRoomsSql);

                        if ($checkRoomsResult && mysqli_num_rows($checkRoomsResult) > 0) {
                            // Room type is associated with rooms, show error message using Swal
                            echo "<script>
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Cannot delete room type as it is associated with existing rooms!',
                                    showConfirmButton: false,
                                    timer: 3000
                                    }).then(() => {
                                        window.location.href = '{$_SERVER['PHP_SELF']}';
                                    });
                                </script>";
                            exit;
                        }

                                    // If no rooms associated, proceed with deletion
                                    $deleteSql = "DELETE FROM room_types WHERE id=$deleteRoomTypeId";
                                    $deleteResult = mysqli_query($dbConnection, $deleteSql);

                                    if ($deleteResult) {
                                        // Show success Swal message and redirect
                                        echo "<script>
                                                Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: 'Room type deleted successfully',
                                                showConfirmButton: false,
                                                timer: 1500
                                                }).then(() => {
                                                    window.location.href = '{$_SERVER['PHP_SELF']}';
                                                });
                                            </script>";
                                        exit;
                                    } else {
                                        // Show error Swal message
                                        echo "<script>
                                                Swal.fire({
                                                icon: 'error',
                                                title: 'Oops...',
                                                text: 'Error deleting room type',
                                                showConfirmButton: false,
                                                timer: 3000
                                                }).then(() => {
                                                    window.location.href = '{$_SERVER['PHP_SELF']}';
                                                });
                                            </script>";
                                        exit;
                                    }
                                }
                           
// Update Room Type
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateRoomType"])) {
    if (isset($_POST["editRoomTypeId"], $_POST["roomTypeName"])) {
        $editRoomTypeId = mysqli_real_escape_string($dbConnection, $_POST["editRoomTypeId"]);
        $updatedRoomTypeName = mysqli_real_escape_string($dbConnection, $_POST["roomTypeName"]);

        // Update the room type in the database
        $updateSql = "UPDATE room_types SET name = '$updatedRoomTypeName' WHERE id = $editRoomTypeId";
        $updateResult = mysqli_query($dbConnection, $updateSql);

        if ($updateResult) {
            // Success notification with Swal
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Room type updated successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '{$_SERVER['PHP_SELF']}'; // Redirect after notification
                    });
                  </script>";
            exit;
        } else {
            // Error notification with Swal
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating room type: " . mysqli_error($dbConnection) . "',
                        showConfirmButton: false,
                        timer: 1500
                    });
                  </script>";
        }
    }
}


                     mysqli_close($dbConnection);
                    ?>                             
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body shadow-lg">
                                    <h1 class="mb-12">Room Types</h1>
                                    <!-- Room Types Table -->
                                    <div class="table-responsive">
                                        <table class="table" id="example1">
                                            <!-- Table Header for Room Types -->
                                            <thead>
                                                <tr>
                                                    <th>Room Type</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <!-- Table Body for Room Types -->
                                            <tbody id="roomTypeTableBody">
                                                <?php echo $tableRows; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
       <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const roomId = this.getAttribute('data-room-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to delete this room type?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?deleteRoomType=' + roomId;
                    }
                });
            });
        });
    });
</script>
                         
                                              
        </body>
        </html>