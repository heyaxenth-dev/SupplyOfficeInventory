<?php
header('Content-Type: application/json');

// Include database connection
require_once '../config/conn.php';
require_once '../authentication.php';

// Check if user is authenticated
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

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
$item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
$stock_number = isset($_POST['stock_number']) ? trim($_POST['stock_number']) : null;
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$unit_of_measure = isset($_POST['unit_of_measure']) ? trim($_POST['unit_of_measure']) : null;
$unit_value = isset($_POST['unit_value']) && $_POST['unit_value'] !== '' ? floatval($_POST['unit_value']) : null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : 'In Stock';
$last_restocked = isset($_POST['last_restocked']) && $_POST['last_restocked'] !== '' ? $_POST['last_restocked'] : null;

// Validate required fields
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

if (empty($item_name)) {
    echo json_encode(['success' => false, 'message' => 'Item name is required']);
    exit;
}

if (empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Category is required']);
    exit;
}

if ($quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be a positive number']);
    exit;
}

// Validate status
$valid_statuses = ['In Stock', 'Low Stock', 'Out of Stock'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Check if item exists
$checkSql = "SELECT id FROM inventory WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$checkStmt->close();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Inventory item not found']);
    exit;
}

// Prepare SQL statement
$sql = "UPDATE inventory SET item_name = ?, description = ?, category = ?, unit_of_measure = ?, unit_value = ?, quantity = ?, status = ?, last_restocked = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters - s=string, i=integer, d=double/decimal
$stmt->bind_param("sssssdiss", 
    $item_name, 
    $description, 
    $category, 
    $unit_of_measure, 
    $unit_value, 
    $quantity, 
    $status, 
    $last_restocked,
    $id
);

// Execute statement
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Inventory item updated successfully!'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to update inventory item: ' . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>