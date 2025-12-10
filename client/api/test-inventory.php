<?php
// Simple test script to check database and table setup
header('Content-Type: text/html; charset=utf-8');

require_once '../config/conn.php';

echo "<h2>Inventory System Diagnostic</h2>";

// Check database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
}

// Check if database exists
$dbCheck = $conn->query("SELECT DATABASE()");
if ($dbCheck) {
    $dbName = $dbCheck->fetch_array()[0];
    echo "<p style='color: green;'>✅ Connected to database: " . $dbName . "</p>";
}

// Check if inventory table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'inventory'");
if ($tableCheck->num_rows == 0) {
    echo "<p style='color: red;'>❌ Inventory table does not exist!</p>";
    echo "<p>Please run the SQL script: <code>admin/config/create_inventory_table.sql</code></p>";
} else {
    echo "<p style='color: green;'>✅ Inventory table exists</p>";
    
    // Check table structure
    $columns = $conn->query("DESCRIBE inventory");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $columns->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count existing records
    $count = $conn->query("SELECT COUNT(*) as count FROM inventory");
    $row = $count->fetch_assoc();
    echo "<p>Total records in inventory: <strong>" . $row['count'] . "</strong></p>";
}

// Test stock number generation
echo "<h3>Stock Number Generation Test:</h3>";
function testGenerateStockNumber($itemName, $conn) {
    $cleanName = preg_replace('/[^a-zA-Z]/', '', $itemName);
    
    if (strlen($cleanName) >= 2) {
        $prefix = strtoupper(substr($cleanName, 0, 2));
    } elseif (strlen($cleanName) == 1) {
        $prefix = strtoupper($cleanName . $cleanName);
    } else {
        $prefix = 'XX';
    }
    
    $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    return $prefix . $randomDigits;
}

$testItems = ["Acrylic Color", "Battery", "Ballpen", "A", "123 Test"];
foreach ($testItems as $item) {
    $stockNum = testGenerateStockNumber($item, $conn);
    echo "<p><strong>" . htmlspecialchars($item) . "</strong> → <code>" . $stockNum . "</code></p>";
}

$conn->close();
?>