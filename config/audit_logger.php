<?php
/**
 * Audit logger for inventory changes
 * Logs who made what changes to inventory items
 */

function logInventoryChange($conn, $action, $itemId = null, $itemName = null, $stockNumber = null, $changesSummary = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Get current user from session
    $userId = null;
    $userName = 'System';
    $userRole = null;

    if (isset($_SESSION['admin_id'])) {
        $userId = $_SESSION['admin_id'];
        $userName = $_SESSION['admin_name'] ?? 'Admin';
        $userRole = $_SESSION['admin_role'] ?? 'Admin';
    } elseif (isset($_SESSION['client_id'])) {
        $userId = $_SESSION['client_id'];
        $userName = $_SESSION['client_name'] ?? 'Staff';
        $userRole = $_SESSION['client_role'] ?? 'Staff';
    }

    // Check if audit table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'inventory_audit_log'");
    if ($tableCheck->num_rows == 0) {
        return;
    }

    $stmt = $conn->prepare("INSERT INTO inventory_audit_log (user_id, user_name, user_role, action, item_id, item_name, stock_number, changes_summary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) return;

    // Types must match: user_id(i), user_name(s), user_role(s), action(s), item_id(i), item_name(s), stock_number(s), changes_summary(s)
    // Bugfix: "ississss" (i-s-s-i-...) wrongly bound action as int — action column was empty/wrong in DB.
    $types = implode('', ['i', 's', 's', 's', 'i', 's', 's', 's']);
    $stmt->bind_param($types, $userId, $userName, $userRole, $action, $itemId, $itemName, $stockNumber, $changesSummary);
    $stmt->execute();
    $stmt->close();
}
