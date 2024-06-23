<?php

  include '../../includes/config/dbconn.php';

    // Log received data


    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

if (isset($_POST['submit'])) {
    // Retrieve form data
    $name = $_POST['fullName'];
    $tenantType = $_POST['tenantType'];
    $roomType = $_POST['roomType'];
    $checkInDate = $_POST['checkInDate'];
    $checkOutDate = $_POST['checkOutDate'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contactNumber = $_POST['contactNumber'];
    $emergencyContactName = $_POST['emergencyContactName'];
    $emergencyNumber = $_POST['emergencyNumber'];

    // Perform SQL insertion
    $sql = "INSERT INTO tenantprofile (TenantID, Name, TenantType, RoomType, CheckIn, CheckOut, Gender, Age, EmailID, Address, ContactNumber, GuardianName, EmergencyNumber) VALUES (NULL, '$name', '$tenantType', '$roomType', '$checkInDate', '$checkOutDate', '$gender', '$age', '$email', '$address', '$contactNumber', '$emergencyContactName', '$emergencyNumber')";

    if (mysqli_query($conn, $sql)) {
        echo "Record added successfully";

        // Retrieve and echo values from the inserted record in tenantprofile table
        $lastInsertedID = mysqli_insert_id($conn);
        $selectQuery = "SELECT CheckIn, CheckOut, TenantType, RoomType, EmailID FROM tenantprofile WHERE TenantID = $lastInsertedID";
        $result = mysqli_query($conn, $selectQuery);

        if ($row = mysqli_fetch_assoc($result)) {
            echo "Check In: " . $row['CheckIn'] . "<br>";
            echo "Check Out: " . $row['CheckOut'] . "<br>";
            echo "Tenant Type: " . $row['TenantType'] . "<br>";
            echo "Room Type: " . $row['RoomType'] . "<br>";
            echo "Email Address: " . $row['EmailID'] . "<br>";
        } else {
            echo "Error retrieving values from the database";
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
