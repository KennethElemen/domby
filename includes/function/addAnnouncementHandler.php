<?php

include_once '../function/addAnnouncement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcerName = $_POST["AnnouncerName"];
    $title = $_POST["Title"];
    $content = $_POST["Content"];
    $contactEmail = $_POST["ContactEmail"];
    $publishDate = $_POST["PublishDate"];

    $success = addAnnouncement($announcerName, $title, $content, $contactEmail, $publishDate);

    if ($success) {
        header('Location: ../../Admin/announcement/announcement.php');
        exit();
    } else {
        echo "Error adding announcement.";
    }
}

?>
