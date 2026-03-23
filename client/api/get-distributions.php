<?php
header('Content-Type: application/json');

require_once '../authentication.php';
require_once '../config/conn.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID', 'distributions' => []]);
    exit;
}

$tableCheck = $conn->query("SHOW TABLES LIKE 'inventory_distributions'");
if (!$tableCheck || $tableCheck->num_rows === 0) {
    echo json_encode(['success' => true, 'distributions' => [], 'table_missing' => true]);
    exit;
}

$sql = "SELECT id, department, quantity, quantity_before, quantity_after, user_name, created_at 
        FROM inventory_distributions 
        WHERE inventory_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'distributions' => $list]);
