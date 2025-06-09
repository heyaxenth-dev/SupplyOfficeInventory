<?php
session_start();

// Check if the admin is not authenticated
function checkLogin(){
    if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
        // Set session variables for status message
        $_SESSION['status_text'] = "Please Login to Access the Page";
        $_SESSION['status_code'] = "warning";
        
        // Redirect to login page
        header("Location: ../index.php");
        exit; // Exit script to prevent further execution
    }
}

?>