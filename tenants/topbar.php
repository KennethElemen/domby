<?php

function signOut() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the landing page (index.php)
    header("Location: ../../index.php");
    exit();
}

// Check if the 'Signout' parameter is set in the URL
if (isset($_GET['Signout'])) {
    // Call the signOut function to perform the signout process
    signOut();
}


?>

<style>
    .max-height-120 {
    max-height: 150px; /* Set your desired max height */
    width: auto; /* Maintain aspect ratio */
    margin-top:20px;
}

</style>
<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
     <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <!-- Replace "DormBell" text with your logo image -->
        <img src="../../assets/images/Logo12.svg" alt="DormBell" class="img-fluid max-height-120 mx-auto">
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile">
                <a class="nav-link" href="?Signout=1">
                    <i class="mdi mdi-logout mr-2 text-primary"></i> Sign Out
                </a>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
