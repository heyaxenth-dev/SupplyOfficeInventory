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
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : null;
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validate required fields
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit;
}

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (empty($role)) {
    echo json_encode(['success' => false, 'message' => 'Role is required']);
    exit;
}

// Validate role
$valid_roles = ['Admin', 'Staff'];
if (!in_array($role, $valid_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

// Validate password if provided
if (!empty($password)) {
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }
}

// Check if user exists
$checkSql = "SELECT id, email FROM users WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$existingUser = $checkResult->fetch_assoc();
$checkStmt->close();

if (!$existingUser) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Check if email already exists (excluding current user)
if ($existingUser['email'] !== $email) {
    $emailCheckSql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $emailCheckStmt = $conn->prepare($emailCheckSql);
    $emailCheckStmt->bind_param("si", $email, $id);
    $emailCheckStmt->execute();
    $emailCheckResult = $emailCheckStmt->get_result();
    $emailCheckStmt->close();
    
    if ($emailCheckResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
}

// Prepare SQL statement
if (!empty($password)) {
    // Update with password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET name = ?, email = ?, role = ?, department = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $email, $role, $department, $hashed_password, $id);
} else {
    // Update without password
    $sql = "UPDATE users SET name = ?, email = ?, role = ?, department = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $role, $department, $id);
}

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Execute statement
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'User updated successfully!'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to update user: ' . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>