<?php
/**
 * Persist a distribution row when the inventory_distributions table exists.
 */
function recordInventoryDistribution($conn, $inventoryId, $department, $quantity, $quantityBefore, $quantityAfter) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $userId = 0;
    $userName = 'System';
    if (isset($_SESSION['admin_id'])) {
        $userId = (int) $_SESSION['admin_id'];
        $userName = $_SESSION['admin_name'] ?? 'Admin';
    } elseif (isset($_SESSION['client_id'])) {
        $userId = (int) $_SESSION['client_id'];
        $userName = $_SESSION['client_name'] ?? 'Staff';
    }

    $check = $conn->query("SHOW TABLES LIKE 'inventory_distributions'");
    if (!$check || $check->num_rows === 0) {
        return;
    }

    $sql = "INSERT INTO inventory_distributions (inventory_id, department, quantity, quantity_before, quantity_after, user_id, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return;
    }
    $stmt->bind_param(
        "isiiiis",
        $inventoryId,
        $department,
        $quantity,
        $quantityBefore,
        $quantityAfter,
        $userId,
        $userName
    ); // user_id 0 = unknown if session missing
    $stmt->execute();
    $stmt->close();
}
