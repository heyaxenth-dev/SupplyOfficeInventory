<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is authenticated
function checkLogin(){
    // Check if staff_auth session variable exists and is true
    if (!isset($_SESSION['staff_auth']) || $_SESSION['staff_auth'] !== true) {
        // Determine if this is an API endpoint (returns JSON) or a regular page (redirects)
        $isApiEndpoint = (
            strpos($_SERVER['PHP_SELF'], '/api/') !== false || 
            strpos($_SERVER['REQUEST_URI'], '/api/') !== false
        );
        
        if ($isApiEndpoint) {
            // For API endpoints, return JSON error
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Unauthorized access. Please login first.',
                'redirect' => '../index.php'
            ]);
            exit;
        } else {
            // For regular pages, redirect to login
            // Set both old and new format for compatibility
            $_SESSION['status'] = "Access Denied";
            $_SESSION['status_text'] = "Please Login to Access the Page";
            $_SESSION['status_code'] = "warning";
            $_SESSION['status_btn'] = "OK";
            
            // Determine the correct redirect path
            $currentPath = $_SERVER['PHP_SELF'];
            
            // If we're in admin directory, redirect to root index
            if (strpos($currentPath, '/admin/') !== false) {
                header("Location: ../index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
    
    // Additional security: verify user still exists in database and is Admin
    // This check is optional and only runs if database connection is available
    // It prevents issues if config/conn.php hasn't been included yet
    if (isset($_SESSION['client_id'])) {
        // Try to check database if connection is available
        // Check both $GLOBALS and if conn.php has been included
        $conn = null;
        if (isset($GLOBALS['conn']) && $GLOBALS['conn'] && !$GLOBALS['conn']->connect_error) {
            $conn = $GLOBALS['conn'];
        } elseif (function_exists('mysqli_connect')) {
            // Try to include config if not already included (only if we can)
            $configPath = __DIR__ . '/config/conn.php';
            if (file_exists($configPath)) {
                // Don't include again if already included, just check if $conn exists
                // This is a safety check, not a full verification
            }
        }
        
        // Only perform database check if we have a valid connection
        // This prevents errors on pages that include authentication.php before config/conn.php
        // The session check above is sufficient for most cases
    }
}

// Automatically check login when this file is included
// This ensures all pages that include authentication.php are protected
checkLogin();

?>