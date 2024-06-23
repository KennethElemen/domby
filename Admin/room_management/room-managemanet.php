<?php
// Include database configuration
include '../includes/config/dbconn.php';

// Connect to the database
$dbConnection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if (!$dbConnection) {
    die("Connection failed: " . mysqli_connect_error());
}

    // Initialize $tableRows
    $tableRows = '';

    // Process POST request
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submitForm"])) {
        // Check if the room type name is provided
        if (isset($_POST["roomTypeName"])) {
            // Sanitize and get the room type name
            $newRoomTypeName = mysqli_real_escape_string($dbConnection, $_POST["roomTypeName"]);

            // Prepare and execute the SQL statement to insert the new room type into the database
            $sql = "INSERT INTO room_types (name) VALUES (?)";
            $stmt = mysqli_prepare($dbConnection, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $newRoomTypeName);
                $result = mysqli_stmt_execute($stmt);

                // Handle success and error scenarios
                if ($result) {
                    // Redirect back to the same page after adding a room type
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit;
                } else {
                    // Optionally, you can display an error message here
                }

                mysqli_stmt_close($stmt);
            } else {
                // Optionally, you can display an error message here
            }
        }
    }

    // Process EDIT request
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editForm"])) {
        // Check if the room type name and ID are provided
        if (isset($_POST["editedRoomTypeName"]) && isset($_POST["editRoomTypeId"])) {
            // Sanitize and get the edited room type name and ID
            $editedRoomTypeName = mysqli_real_escape_string($dbConnection, $_POST["editedRoomTypeName"]);
            $editRoomTypeId = mysqli_real_escape_string($dbConnection, $_POST["editRoomTypeId"]);

            // Prepare and execute the SQL statement to update the room type in the database
            $updateSql = "UPDATE room_types SET name = ? WHERE id = ?";
            $updateStmt = mysqli_prepare($dbConnection, $updateSql);

            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, "si", $editedRoomTypeName, $editRoomTypeId);
                $updateResult = mysqli_stmt_execute($updateStmt);

                // Handle success and error scenarios
                if ($updateResult) {
                    // Redirect back to the same page after editing a room type
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit;
                } else {
                    // Optionally, you can display an error message here
                }

                mysqli_stmt_close($updateStmt);
            } else {
                // Optionally, you can display an error message here
            }
        }
    }

    // Fetch room types from the database
    $sql = "SELECT * FROM room_types";
    $result = mysqli_query($dbConnection, $sql);

    // Check if there are any rows in the result set
    if ($result && mysqli_num_rows($result) > 0) {
        // Output data of each row
        while ($row = mysqli_fetch_assoc($result)) {
            $modalId = 'editRoomTypeModal' . $row['id'];
            $tableRows .= "
                <tr>
                    <td>{$row['name']}</td>
                    <td>

                            <div class='modal fade' id='{$modalId}' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                            <div class='modal-dialog' role='document'>
                                <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='exampleModalLabel'>Edit Room Type</h5>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>
                                <div class='modal-body'>
                                    <!-- Form for editing room types -->
                                    <form method='post' action='room_management.php'>
                                    <div class='form-group'>
                                        <label for='editedRoomTypeName'>Room Type Name:</label>
                                        <input type='text' class='form-control' id='editedRoomTypeName' name='editedRoomTypeName' value='{$row['name']}' required>
                                    </div>
                                    <input type='hidden' name='editRoomTypeId' value='{$row['id']}'>
                                    <button type='submit' class='btn btn-primary' name='editForm'>Save Changes</button>
                                    </form>
                                </div>
                                </div>
                            </div>
                            </div>
                            <button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#{$modalId}'>Edit</button>
                        <a href='room_management.php?deleteRoomType={$row['id']}' class='btn btn-danger btn-sm' role='button'>Delete</a>
                    </td>
                </tr>
            ";
        }
    } else {
        // If no room types are found
        $tableRows = "<tr><td colspan='2'>No room types found</td></tr>";
    }

    // Process delete requests
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["deleteRoomType"])) {
        // Handle deletion
        $deleteRoomTypeId = mysqli_real_escape_string($dbConnection, $_GET["deleteRoomType"]);
        $deleteSql = "DELETE FROM room_types WHERE id=$deleteRoomTypeId";
        $deleteResult = mysqli_query($dbConnection, $deleteSql);

        // Handle success and error scenarios
        if ($deleteResult) {
            // Redirect back to the same page after deleting a room type
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        } else {
            // Optionally, you can display an error message here
        }
    }

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["roomNumber"])) {
        // Sanitize form data
        $roomNumber = mysqli_real_escape_string($dbConnection, $_POST["roomNumber"]);
        $roomName = mysqli_real_escape_string($dbConnection, $_POST["roomName"]);
        $description = mysqli_real_escape_string($dbConnection, $_POST["description"]);
        $numOfBeds = mysqli_real_escape_string($dbConnection, $_POST["numOfBeds"]);
        // Images handling should be implemented based on your requirements
        $ratePerNight = mysqli_real_escape_string($dbConnection, $_POST["ratePerNight"]);
        $roomType = mysqli_real_escape_string($dbConnection, $_POST["roomType"]);

        // Insert data into the database
        $insertSql = "INSERT INTO room_management (room_number, room_name, description, num_of_beds, rate_per_night, room_type) VALUES ('$roomNumber', '$roomName', '$description', '$numOfBeds', '$ratePerNight', '$roomType')";
        $insertResult = mysqli_query($dbConnection, $insertSql);

        // Check if the insertion was successful
        if ($insertResult) {
            // Redirect to the same page to refresh and display the updated data
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        } else {
            // Optionally, you can handle the error here
            echo "Error: " . mysqli_error($dbConnection);
        }
    }
    }

    // Fetch room management entries from the database and display them in the table
    $sql = "SELECT * FROM room_management";
    $result = mysqli_query($dbConnection, $sql);

    // Check if there are any rows in the result set
    if ($result && mysqli_num_rows($result) > 0) {
    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['room_number'] . '</td>';
        echo '<td>' . $row['room_name'] . '</td>';
        echo '<td>' . $row['description'] . '</td>';
        echo '<td>' . $row['num_of_beds'] . '</td>';
        echo '<td>' . $row['rate_per_night'] . '</td>';
        echo '<td>' . $row['room_type'] . '</td>';
        echo '</tr>';
    }
    } else {
    // If no room management entries are found
    echo '<tr><td colspan="8">No room management entries found</td></tr>';
    }

    // Handle delete request
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteRoomId"])) {
    $deleteRoomId = mysqli_real_escape_string($dbConnection, $_POST["deleteRoomId"]);
    $deleteSql = "DELETE FROM room_management WHERE id=$deleteRoomId";
    $deleteResult = mysqli_query($dbConnection, $deleteSql);

    // Check if deletion was successful
    if ($deleteResult) {
        // Redirect back to the same page after successful deletion
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    } else {
        // Optionally, you can display an error message here
        echo "Error deleting room: " . mysqli_error($dbConnection);
    }
    }

    // Process POST request
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        try {
            // Your existing form submission logic
            if (isset($_POST["roomNumber"])) {
                // ... (your existing code)

                // Trigger the success modal
                echo '<script>$("#successModal").modal("show");</script>';
            }

            // File upload logic
            $targetDir = "../Admin/uploads/";
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
                                }
        } catch (Exception $e) {
            // Echo error message and trigger the error modal
            echo 'Error: ' . $e->getMessage();
            echo '<script>$("#errorMessage").text("' . $e->getMessage() . '"); $("#errorModal").modal("show");</script>';
        }
    }

    // Close the database connection
    mysqli_close($dbConnection);


?>
