<?php
session_start();

include_once('config/conn.php');

// Check if user is authenticated as an admin
if (isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] == true) {
    // Set session variables for status message
    $_SESSION['status_text'] = "You have been logged out.";
    $_SESSION['status_code'] = "info";

     // Unset session variables
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_department']); 
        unset($_SESSION['admin_auth']);

    // Redirect to index page
    header("Location: index");
    exit; // Exit script to prevent further execution
}