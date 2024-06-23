<?php
include '../../includes/config/dbconn.php';
include '../../includes/config/mailer.php';

// Start or resume the session
session_start();

// Create a separate MySQLi connection for announcements
$connAnnouncements = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connAnnouncements->connect_error) {
    die("Connection failed: " . $connAnnouncements->connect_error);
}


// Fetch only the latest announcement outside of the POST request
$result = $connAnnouncements->query("SELECT * FROM announcements ORDER BY announcement_id DESC LIMIT 1");
$announcementsHTML = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcementsHTML .= "<div class='container'>
                              <div class='row justify-content-center'>
                                  <div class='col-lg-12'>
                                      <div class='card'>
                                          <div class='card-body'>
                                              <h1 class='card-title mb-4' style='color: #1e1e2d; font-weight: 500; font-size: 32px;'>" . $row["announcement_title"] . "</h1>
                                              <p class='card-text text-muted mb-3' style='font-size: 15px;'>" . $row["announcer_name"] . "</p>
                                              <p class='card-text mb-4' style='font-size: 16px; color: #455056;'>" . $row["announcement_content"] . "</p>
                                          </div>
                                          <div class='card-footer bg-transparent border-top-0'>
                                              <small class='text-muted'>" . date('F j, Y', strtotime($row["publish_date"])) . "&nbsp; " . date('h:i A', strtotime($row["publish_time"])) . "</small>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>";
    }
} else {
    $announcementsHTML = "<p class='no-announcement'>No announcements available.</p>";
}

// Close the connection for announcements

?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../head.php'; ?>
  <head>
      <style>
        /* Add your additional styles here */
        .custom-header {
            /* Your custom styles for the header */
            color: #007bff; /* Change the color to your preference */
            font-size: 24px; /* Change the font size to your preference */
            text-align: center;
        }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <?php include '../topbar.php'; ?>
      <div class="container-fluid page-body-wrapper">
        <?php include '../sidebar.php'; ?>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" data-toggle="modal" data-target="#add-announcement">
                <i class="mdi mdi-bullhorn"></i>   Reminder
              </button>
            </div>
            
            <div class="row">
              <div class="col-md-6 grid-margin stretch-card">
                  <div class="card">
                      <div class="card-body shadow-lg">
                          <div id="announcementContent">
                              <?php echo $announcementsHTML; ?>
                          </div>
                      </div>
                  </div>
              </div>
            </div>

            <?php include '../modals.php'; ?>
            <?php include '../footer.php'; ?>
          </div>
        </div>
      </div>
    </div>

    <?php include '../scripts.php'; ?>
  </body>
</html>
