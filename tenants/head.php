
 <head>
   
<?php
require_once('../../includes/config/dbconn.php');
require_once('../../includes/function/check_session.php');// Include your functions file

// Check if the user is a tenant, otherwise redirect to index
checkSession($conn, ['tenant']);

// The rest of your code for tenants goes here
?>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dormitory Management System</title>

    
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/style1.css">
    <link rel="stylesheet" href="../../assets/css/jquery.toast.min.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" />
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.css">

    <link rel="stylesheet" href="../../evo-calendar.min.css">
    <link rel="stylesheet" href="../../evo-calendar.css">
    
    <!-- Add FullCalendar library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
      <style>
          .mySlides {display:none}
      </style>
<script src="../../Chart.js/Chart.bundle.min.js"></script>


<!-- Bootstrap JavaScript and Popper.js -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>


  </head>
