<?php 
include './config/conn.php';
session_start();


if (isset($_POST['registerBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize and validate input data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $department = $conn->real_escape_string($_POST['department']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (name, email, role, department, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $role, $department, $hashed_password);
    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['status'] = "Success";
        $_SESSION['status_text'] = "Registration successful!";
        $_SESSION['status_code'] = "success";
        $_SESSION['status_btn'] = "Ok";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['status'] = "Error";
        $_SESSION['status_text'] = "Registration failed!" . $stmt->error;
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Ok";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    }
    $stmt->close();
    
}

if (isset($_POST['loginBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();
if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password
        if ($user['role'] === 'Admin') {
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['admin_auth'] = true; // Set admin authentication session variable
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_department'] = $user['department'];

                // Redirect to admin dashboard
                header("Location: admin/verification_dashboard.php");
                exit;
            } else {
                $_SESSION['status'] = "Error";
                $_SESSION['status_text'] = "Incorrect password!";
                $_SESSION['status_code'] = "error";
                $_SESSION['status_btn'] = "Ok";
                header("Location: index.php");
                exit;
            }
            # code...
        }else if ($user['role'] === 'Staff') {
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['staff_auth'] = true; // Set staff authentication session variable
                $_SESSION['client_id'] = $user['id'];
                $_SESSION['client_name'] = $user['name'];
                $_SESSION['client_email'] = $user['email'];
                $_SESSION['client_role'] = $user['role'];
                $_SESSION['client_department'] = $user['department'];

                // Redirect to staff dashboard
                header("Location: client/verification_dashboard.php");
                exit;
            } else {
                $_SESSION['status'] = "Error";
                $_SESSION['status_text'] = "Incorrect password!";
                $_SESSION['status_code'] = "error";
                $_SESSION['status_btn'] = "Ok";
                header("Location: index.php");
                exit;
            }
        }else {
            $_SESSION['status'] = "Error";
            $_SESSION['status_text'] = "Invalid role!";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Ok";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['status'] = "Error";
        $_SESSION['status_text'] = "No user found with that email!";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Ok";
        header("Location: index.php");
        exit;
    }
    # code...
}
?>