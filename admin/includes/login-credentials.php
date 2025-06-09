<?php 
// Get session user ID
$user_id = $_SESSION['admin_id'] ?? '';
$role = $_SESSION['admin_role'] ?? '';

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($user = $result->fetch_assoc()) {
        $name = $user['name'];
        $email = $user['email'];
        $department = $user['department'];
    }
} 
?>