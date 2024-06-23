<?php

function getRoomCount($servername, $username, $password, $dbname) {
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    $query = "SELECT COUNT(*) as total_rooms FROM room_management";
    $result = mysqli_query($dbConnection, $query);

    if (!$result) {
        die('Error: ' . mysqli_error($dbConnection));
    }

    $totalRooms = 0;

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalRooms = $row['total_rooms'];
    } else {
        echo "Error in the database query";
    }

    mysqli_close($dbConnection);

    return $totalRooms;
}

function getRoomTypeCount($servername, $username, $password, $dbname) {
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    $roomTypeQuery = "SELECT COUNT(*) as total_room_types FROM room_types";
    $roomTypeResult = mysqli_query($dbConnection, $roomTypeQuery);

    if (!$roomTypeResult) {
        die('Error: ' . mysqli_error($dbConnection));
    }

    $totalRoomTypes = 0;

    if ($roomTypeResult) {
        $roomTypeRow = mysqli_fetch_assoc($roomTypeResult);
        $totalRoomTypes = $roomTypeRow['total_room_types'];
    } else {
        echo "Error in the database query";
    }

    mysqli_close($dbConnection);

    return $totalRoomTypes;
}

function getAcceptedReservationCount($servername, $username, $password, $dbname) {
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    $reservationQuery = "SELECT COUNT(*) as total_accepted_reservations FROM reservations WHERE status = 'accepted'";
    $reservationResult = mysqli_query($dbConnection, $reservationQuery);

    if (!$reservationResult) {
        die('Error: ' . mysqli_error($dbConnection));
    }

    $totalAcceptedReservations = 0;

    if ($reservationResult) {
        $reservationRow = mysqli_fetch_assoc($reservationResult);
        $totalAcceptedReservations = $reservationRow['total_accepted_reservations'];
    } else {
        echo "Error in the database query";
    }

    mysqli_close($dbConnection);

    return $totalAcceptedReservations;
}


function getPendingPaymentsCount($servername, $username, $password, $dbname) {
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    $pendingPaymentsQuery = "SELECT COUNT(*) as pending_payments FROM payment WHERE status = 'pending'";
    $pendingPaymentsResult = mysqli_query($dbConnection, $pendingPaymentsQuery);

    if (!$pendingPaymentsResult) {
        die('Error: ' . mysqli_error($dbConnection));
    }

    $pendingPaymentsCount = 0;

    if ($pendingPaymentsResult) {
        $pendingPaymentsRow = mysqli_fetch_assoc($pendingPaymentsResult);
        $pendingPaymentsCount = $pendingPaymentsRow['pending_payments'];
    } else {
        echo "Error in the database query";
    }

    mysqli_close($dbConnection);

    return $pendingPaymentsCount;
}

function getUnconfirmedBookingsCount($servername, $username, $password, $dbname) {
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    $unconfirmedBookingsQuery = "SELECT COUNT(*) as unconfirmed_bookings FROM reservations WHERE status IS NULL";
    $unconfirmedBookingsResult = mysqli_query($dbConnection, $unconfirmedBookingsQuery);

    if (!$unconfirmedBookingsResult) {
        die('Error: ' . mysqli_error($dbConnection));
    }

    $unconfirmedBookingsCount = 0;

    if ($unconfirmedBookingsResult) {
        $unconfirmedBookingsRow = mysqli_fetch_assoc($unconfirmedBookingsResult);
        $unconfirmedBookingsCount = $unconfirmedBookingsRow['unconfirmed_bookings'];
    } else {
        echo "Error in the database query";
    }

    mysqli_close($dbConnection);

    return $unconfirmedBookingsCount;
}

function getTenantCountByStatus($servername, $username, $password, $dbname) {
    // Establish connection
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Query to count tenants based on conditions
    $query = "SELECT 
                COUNT(CASE WHEN r.type_of_stay = 'Long-term' THEN 1 END) AS Long_term_count,
                COUNT(CASE WHEN r.type_of_stay = 'Transient' THEN 1 END) AS Transient_count
            FROM reservations r
            WHERE r.status = 'Accepted'";

    // Execute the query
    $result = mysqli_query($dbConnection, $query);

    // Check if query executed successfully
    if (!$result) {
        die('Error: ' . mysqli_error($dbConnection));
    }

    // Initialize tenant count array
    $tenantCounts = array('Long-term' => 0, 'Transient' => 0);

    // Check if there are results
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Assign counts to array
        $tenantCounts['Long-term'] = $row['Long_term_count'];
        $tenantCounts['Transient'] = $row['Transient_count'];
    } else {
        // No results found
        echo "No tenants found matching the criteria.";
    }

    // Close connection
    mysqli_close($dbConnection);

    // Return tenant counts
    return $tenantCounts;
}





function getInquiriesByMonth($servername, $username, $password, $dbname) {
    // Establish database connection
    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }

    // Prepare and execute the query
    $query = "SELECT MONTH(`date`) AS month, COUNT(*) AS total_inquiries 
              FROM reservations 
              WHERE `date` IS NOT NULL 
              AND `status` = 'accepted' 
              GROUP BY MONTH(`date`) 
              ORDER BY MONTH(`date`)";
    $statement = $dbConnection->prepare($query);
    $statement->execute();
    $result = $statement->get_result();

    // Initialize an array to store the results
    $inquiriesByMonth = array();

    // Fetch the results and store them in the array
    while($row = $result->fetch_assoc()) {
        $monthNumber = $row['month'];
        $total_inquiries = $row['total_inquiries'];
        
        // Convert month number to month name
        $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));

        // Store the results in the array
        $inquiriesByMonth[$monthName] = $total_inquiries;
    }

    // Close the statement and database connection
    $statement->close();
    $dbConnection->close();

    // Fill in missing months with zero inquiries
    $inquiriesByMonth = fillMissingMonths($inquiriesByMonth);

    // Return the array containing the number of inquiries by month
    return $inquiriesByMonth;
}

// Function to fill missing months with zero inquiries
function fillMissingMonths($inquiriesByMonth) {
    // Get all month names
    $allMonths = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

    // Loop through all month names
    foreach ($allMonths as $month) {
        // If month name not found in inquiries array, add it with zero inquiries
        if (!array_key_exists($month, $inquiriesByMonth)) {
            $inquiriesByMonth[$month] = 0;
        }
    }

    // Sort the array by month
    ksort($inquiriesByMonth);

    return $inquiriesByMonth;
}




// Function to count total occupants compared to maximum occupants of the rooms
function getTotalOccupantsCount($servername, $username, $password, $dbname) {
    $dbConnection = new mysqli($servername, $username, $password, $dbname);
    
    if ($dbConnection->connect_error) {
        die("Connection failed: " . $dbConnection->connect_error);
    }
    
    // Query to calculate the sum of maximum occupants from room_management
    $query = "SELECT SUM(max_occupants) AS total_max_occupants FROM room_management";
    $result = $dbConnection->query($query);
    
    // Get the sum of maximum occupants
    $totalMaxOccupants = 0;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalMaxOccupants = $row['total_max_occupants'];
    }
    
    // Query to calculate the sum of actual occupants from accepted reservations
    $query = "SELECT IFNULL(SUM(occupants), 0) AS total_actual_occupants
              FROM (
                  SELECT room_number, COUNT(room_number) AS occupants
                  FROM reservations
                  WHERE status = 'accepted'
                  GROUP BY room_number
              ) r";
    
    $result = $dbConnection->query($query);
    
    // Get the sum of actual occupants from accepted reservations
    $totalActualOccupants = 0;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalActualOccupants = $row['total_actual_occupants'];
    }
    
    // Calculate the occupancy percentage
    if ($totalMaxOccupants > 0) {
        $occupancyPercentage = ($totalActualOccupants / $totalMaxOccupants) * 100;
    } else {
        $occupancyPercentage = 0;
    }
    
    return array(
        'total_max_occupants' => $totalMaxOccupants,
        'total_actual_occupants' => $totalActualOccupants,
        'occupancy_percentage' => $occupancyPercentage
    );
}



?>
