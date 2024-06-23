<?php

function displayAnnouncementById($announcementId)
{
      include '../../includes/config/dbconn.php';

   
   

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the announcement based on the provided ID
    $sql = "SELECT announcer_name, announcement_title, announcement_content, contact_email, publish_date FROM announcements WHERE announcement_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $announcementId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data of the latest announcement
        $row = $result->fetch_assoc();
        echo "<p id='announcerNameDisplay'>" . $row["announcer_name"] . "</p>";
        echo "<p id='titleDisplay'>" . $row["announcement_title"] . "</p>";
        echo "<p id='contentDisplay'>" . $row["announcement_content"] . "</p>";
        echo "<p id='contactEmailDisplay'>" . $row["contact_email"] . "</p>";
        echo "<p id='publishDateDisplay'>" . $row["publish_date"] . "</p>";
    } else {
        echo "No announcement available.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}


$announcementId = 1; 
displayAnnouncementById($announcementId);

function getLatestAnnouncementId()
{
    $conn = dbconn(); // Assuming you have a function named dbconn() for database connection

    // Fetch the latest announcement ID
    $sql = "SELECT MAX(announcement_id) AS latest_id FROM announcements";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["latest_id"];
    } else {
        return null;
    }

    // Close connection
    $conn->close();
}

?>
