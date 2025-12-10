<?php
header('Content-Type: application/json');

// Include authentication first (it will check and exit if not authenticated)
require_once '../authentication.php';

// Include database connection
require_once '../config/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if database connection is valid
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get and sanitize input data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$currentUserId = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;

// Validate ID
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

// Prevent deleting own account
if ($id == $currentUserId) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

// Check if user exists
$checkSql = "SELECT id, name, email FROM users WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$user = $result->fetch_assoc();
$checkStmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Prepare SQL statement
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("i", $id);

// Execute statement
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'User "' . htmlspecialchars($user['name']) . '" has been deleted successfully!'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to delete user: ' . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>