<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $check_in_date = $_POST["check_in_date"];
    $check_out_date = $_POST["check_out_date"];

    include '..//config/dbconn.php';
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


    try {
        // Start a transaction
        $conn->begin_transaction();

        // Room is available, proceed with reservation
        $full_name = $_POST["Name"];
        $email = $_POST["email"];
        $contact_number = $_POST["contact_number"];
        $type_of_stay = $_POST["type_of_stay"];
        $room_number = $_POST["room_number"];

        // Use prepared statements to prevent SQL injection
        $sqlInsertReservation = $conn->prepare("INSERT INTO reservations (Name, email, contact_number, check_in_date, check_out_date, type_of_stay, room_number) 
                                               VALUES (?, ?, ?, ?, ?, ?, ?)");

        $sqlInsertReservation->bind_param("sssssss", $full_name, $email, $contact_number, $check_in_date, $check_out_date, $type_of_stay, $room_number);

        if ($sqlInsertReservation->execute()) {
            $reservation_id = $conn->insert_id;
            // Include the reservemail.php file
            include 'reservemail.php';
            // Commit the transaction
            $conn->commit();
            header("Location: ../../errorpage/Successful.php");
            exit(); // Ensure script execution stops after redirection
        } else {
            // Rollback the transaction in case of an error
            $conn->rollback();
            echo "Error inserting reservation: " . $sqlInsertReservation->error;
            // You might want to handle this error more gracefully or log it
            header("Location: ../../errorpage/unsuccessful.php");
            exit(); // Ensure script execution stops after redirection
        }
    } catch (Exception $e) {
        // Handle exceptions and rollback the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        // You might want to handle this error more gracefully or log it
       header("Location: ../../errorpage/unsuccessful.php");
        exit(); // Ensure script execution stops after redirection
    } finally {
        // Close the database connection
        $conn->close();
    }
}
?>
