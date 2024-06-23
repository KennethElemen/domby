<?php
session_start();


function signOut()
{
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the landing page (index.php)
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Check if the 'Signout' parameter is set in the URL
if (isset($_GET['Signout'])) {
    // Call the signOut function to perform the signout process
    signOut();
}
?>
