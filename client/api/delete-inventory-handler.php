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

// Validate ID
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

// Check if item exists
$checkSql = "SELECT id, item_name FROM inventory WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$item = $result->fetch_assoc();
$checkStmt->close();

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Inventory item not found']);
    exit;
}

// Prepare SQL statement
$sql = "DELETE FROM inventory WHERE id = ?";
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
        'message' => 'Inventory item "' . htmlspecialchars($item['item_name']) . '" has been deleted successfully!'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to delete inventory item: ' . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>