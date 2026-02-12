<?php
header('Content-Type: application/json');

require_once '../authentication.php';
require_once '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

$stmt = $conn->prepare("SELECT id, item_name, description, stock_number, category, unit_of_measure, unit_value, quantity, status, last_restocked, created_at, updated_at FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$row = $result->fetch_assoc();
echo json_encode([
    'success' => true,
    'item' => [
        'id' => (int)$row['id'],
        'item_name' => $row['item_name'],
        'description' => $row['description'] ?? '',
        'stock_number' => $row['stock_number'] ?? '',
        'category' => $row['category'],
        'unit_of_measure' => $row['unit_of_measure'] ?? '',
        'unit_value' => $row['unit_value'],
        'quantity' => (int)$row['quantity'],
        'status' => $row['status'],
        'last_restocked' => $row['last_restocked'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ]
]);
$conn->close();
?>
