<?php
header('Content-Type: application/json');

// Include authentication first (it will check and exit if not authenticated)
require_once '../authentication.php';

// Include database connection
require_once '../config/conn.php';
require_once __DIR__ . '/../../config/audit_logger.php';
require_once __DIR__ . '/../../config/helpers.php';

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

// Get and sanitize input data (normalize for uniform capitalization)
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$item_name = isset($_POST['item_name']) ? normalizeTitleCase($_POST['item_name']) : '';
$description = isset($_POST['description']) ? normalizeSentenceCase($_POST['description']) : null;
// Use POST stock_number if provided (e.g. from Inventory page); otherwise use existing item's (e.g. from Verification page)
$stock_number = isset($_POST['stock_number']) && trim((string)$_POST['stock_number']) !== '' ? trim($_POST['stock_number']) : null;
$category = isset($_POST['category']) ? normalizeTitleCase($_POST['category']) : '';
$unit_of_measure = isset($_POST['unit_of_measure']) && trim($_POST['unit_of_measure']) !== '' ? normalizeLowerCase($_POST['unit_of_measure']) : null;
$unit_value = isset($_POST['unit_value']) && $_POST['unit_value'] !== '' ? floatval($_POST['unit_value']) : null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

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

// Check if item exists and get old values for audit
$checkSql = "SELECT id, item_name, stock_number, quantity FROM inventory WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$oldItem = $result->fetch_assoc();
$checkStmt->close();

if (!$oldItem) {
    echo json_encode(['success' => false, 'message' => 'Inventory item not found']);
    exit;
}

// Build changes summary for audit
$changes = [];
if ($oldItem['item_name'] != $item_name) $changes[] = "Item: {$oldItem['item_name']} → {$item_name}";
if ($oldItem['quantity'] != $quantity) $changes[] = "Qty: {$oldItem['quantity']} → {$quantity}";
$changesSummary = !empty($changes) ? implode('; ', $changes) : 'Record updated';

// Prepare SQL statement - last_restocked = CURDATE() matches updated_at (same DB timestamp)
// Status column has been removed; quantity now fully drives on-screen badges.
$sql = "UPDATE inventory SET item_name = ?, description = ?, category = ?, unit_of_measure = ?, unit_value = ?, quantity = ?, last_restocked = CURDATE() WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters - s=string, i=integer, d=double/decimal
$stmt->bind_param("ssssddi", 
    $item_name, 
    $description, 
    $category, 
    $unit_of_measure, 
    $unit_value, 
    $quantity, 
    $id
);

// Execute statement
if ($stmt->execute()) {
    $logStockNumber = $stock_number !== null ? $stock_number : ($oldItem['stock_number'] ?? null);
    logInventoryChange($conn, 'EDIT', $id, $item_name, $logStockNumber, $changesSummary);
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