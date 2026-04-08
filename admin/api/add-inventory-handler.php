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

// Check if inventory table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'inventory'");
if ($tableCheck->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Inventory table does not exist. Please run the SQL script to create it.']);
    exit;
}

// Get and sanitize input data (normalize for uniform capitalization)
$item_name = isset($_POST['item_name']) ? normalizeTitleCase($_POST['item_name']) : '';
$description = isset($_POST['description']) ? normalizeSentenceCase($_POST['description']) : null;
$category = isset($_POST['category']) ? normalizeTitleCase($_POST['category']) : '';
$unit_of_measure = isset($_POST['unit_of_measure']) && trim($_POST['unit_of_measure']) !== '' ? normalizeLowerCase($_POST['unit_of_measure']) : null;
$unit_value = isset($_POST['unit_value']) && $_POST['unit_value'] !== '' ? floatval($_POST['unit_value']) : null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

// Auto-generate stock number based on item name
// Format: First 2 letters (uppercase) + 4 random digits
function generateStockNumber($itemName, $conn) {
    // Remove special characters and get only letters
    $cleanName = preg_replace('/[^a-zA-Z]/', '', $itemName);
    
    // Get first 2 letters, convert to uppercase
    if (strlen($cleanName) >= 2) {
        $prefix = strtoupper(substr($cleanName, 0, 2));
    } elseif (strlen($cleanName) == 1) {
        // If only 1 letter, use it twice
        $prefix = strtoupper($cleanName . $cleanName);
    } else {
        // If no letters found, use 'XX' as default
        $prefix = 'XX';
    }
    
    // Generate unique stock number
    $maxAttempts = 10;
    $attempt = 0;
    
    do {
        // Generate 4 random digits
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $stock_number = $prefix . $randomDigits;
        
        // Check if stock number already exists
        $checkSql = "SELECT COUNT(*) as count FROM inventory WHERE stock_number = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $stock_number);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();
        
        $attempt++;
        
        // If stock number doesn't exist or max attempts reached, return it
        if ($row['count'] == 0 || $attempt >= $maxAttempts) {
            return $stock_number;
        }
    } while ($attempt < $maxAttempts);
    
    // Fallback: add timestamp if still duplicate after max attempts
    return $prefix . substr(time(), -4);
}

$stock_number = generateStockNumber($item_name, $conn);

// Validate required fields
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

// Prepare SQL statement - last_restocked = CURDATE() to match created_at
// Status column has been removed; quantity now fully drives on-screen badges.
$sql = "INSERT INTO inventory (item_name, description, stock_number, category, unit_of_measure, unit_value, quantity, last_restocked) 
        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters - s=string, i=integer, d=double/decimal (7 params, last_restocked uses CURDATE())
$stmt->bind_param("sssssdi", 
    $item_name, 
    $description, 
    $stock_number, 
    $category, 
    $unit_of_measure, 
    $unit_value, 
    $quantity
);

// Execute statement
if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    logInventoryChange($conn, 'ADD', $newId, $item_name, $stock_number, "Added: Qty {$quantity}, Category: {$category}");
    echo json_encode([
        'success' => true, 
        'message' => 'Inventory item added successfully! Stock Number: ' . $stock_number,
        'id' => $newId,
        'stock_number' => $stock_number
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to add inventory item: ' . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>