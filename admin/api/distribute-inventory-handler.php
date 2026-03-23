<?php
header('Content-Type: application/json');

require_once '../authentication.php';
require_once '../config/conn.php';
require_once __DIR__ . '/../../config/audit_logger.php';
require_once __DIR__ . '/../../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$department = isset($_POST['department']) ? normalizeTitleCase($_POST['department']) : '';
$distributeQty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

if ($department === '' || $department === null) {
    echo json_encode(['success' => false, 'message' => 'Department is required']);
    exit;
}

if ($distributeQty <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

$sql = "SELECT id, item_name, stock_number, quantity FROM inventory WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Inventory item not found']);
    exit;
}

$currentQty = (int) $item['quantity'];
if ($distributeQty > $currentQty) {
    echo json_encode([
        'success' => false,
        'message' => 'Not enough stock. Available: ' . $currentQty . ', requested: ' . $distributeQty
    ]);
    exit;
}

$newQty = $currentQty - $distributeQty;
$status = ($newQty === 0) ? 'Out of Stock' : (($newQty <= 10) ? 'Low Stock' : 'In Stock');

$updateSql = "UPDATE inventory SET quantity = ?, status = ?, last_restocked = CURDATE() WHERE id = ?";
$upd = $conn->prepare($updateSql);
if (!$upd) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$upd->bind_param("isi", $newQty, $status, $id);

if ($upd->execute()) {
    $stockNum = $item['stock_number'] ?? '';
    $changesSummary = 'Distributed ' . $distributeQty . ' to ' . $department . '; Qty: ' . $currentQty . ' → ' . $newQty;
    logInventoryChange($conn, 'EDIT', $id, $item['item_name'], $stockNum, $changesSummary);
    echo json_encode([
        'success' => true,
        'message' => 'Successfully distributed ' . $distributeQty . ' unit(s) to ' . $department . '.',
        'new_quantity' => $newQty
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update inventory: ' . $upd->error]);
}

$upd->close();
$conn->close();
