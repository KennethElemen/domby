<?php

function displayLatestAnnouncement()
{
  include '../includes/config/dbconn.php';

   

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the latest announcement from the database
    $sql = "SELECT announcer_name, announcement_title, announcement_content, contact_email, publish_date FROM announcements ORDER BY publish_date DESC LIMIT 1";
    $result = $conn->query($sql);

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

    // Close connection
    $conn->close();
}

?>
