<?php 
include 'authentication.php';
include 'config/conn.php';
include __DIR__ . '/../config/helpers.php';
include 'includes/login-credentials.php';

$profile_message = '';
$profile_message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id = $_SESSION['client_id'] ?? null;
    if (!$user_id) {
        $profile_message = 'Session expired. Please log in again.';
        $profile_message_type = 'danger';
    } elseif ($_POST['action'] === 'update_profile') {
        $new_name = isset($_POST['fullName']) ? normalizeTitleCase($_POST['fullName']) : '';
        $new_email = isset($_POST['email']) ? trim($_POST['email']) : '';
        if (empty($new_name)) {
            $profile_message = 'Full name is required.';
            $profile_message_type = 'danger';
        } elseif (empty($new_email)) {
            $profile_message = 'Email is required.';
            $profile_message_type = 'danger';
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $profile_message = 'Invalid email format.';
            $profile_message_type = 'danger';
        } else {
            $emailCheck = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $emailCheck->bind_param("si", $new_email, $user_id);
            $emailCheck->execute();
            if ($emailCheck->get_result()->num_rows > 0) {
                $profile_message = 'This email is already in use.';
                $profile_message_type = 'danger';
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
                if ($stmt->execute()) {
                    $name = $new_name;
                    $email = $new_email;
                    $profile_message = 'Profile updated successfully.';
                    $profile_message_type = 'success';
                } else {
                    $profile_message = 'Failed to update profile. Please try again.';
                    $profile_message_type = 'danger';
                }
                $stmt->close();
            }
            $emailCheck->close();
        }
    } elseif ($_POST['action'] === 'change_password') {
        $current = $_POST['password'] ?? '';
        $new_pass = $_POST['newpassword'] ?? '';
        $renew = $_POST['renewpassword'] ?? '';
        if (empty($current) || empty($new_pass) || empty($renew)) {
            $profile_message = 'All password fields are required.';
            $profile_message_type = 'danger';
        } elseif (strlen($new_pass) < 6) {
            $profile_message = 'New password must be at least 6 characters.';
            $profile_message_type = 'danger';
        } elseif ($new_pass !== $renew) {
            $profile_message = 'New password and confirmation do not match.';
            $profile_message_type = 'danger';
        } else {
            $check = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $check->bind_param("i", $user_id);
            $check->execute();
            $row = $check->get_result()->fetch_assoc();
            $check->close();
            if (!$row || !password_verify($current, $row['password'])) {
                $profile_message = 'Current password is incorrect.';
                $profile_message_type = 'danger';
            } else {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hash, $user_id);
                if ($stmt->execute()) {
                    $profile_message = 'Password changed successfully.';
                    $profile_message_type = 'success';
                } else {
                    $profile_message = 'Failed to change password. Please try again.';
                    $profile_message_type = 'danger';
                }
                $stmt->close();
            }
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Profile</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div>
    <!-- End Page Title -->

    <?php if (!empty($profile_message)): ?>
    <div class="alert alert-<?= htmlspecialchars($profile_message_type) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($profile_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="assets/img/user-profile.png" alt="Profile" class="rounded-circle" />
                        <h2><?= htmlspecialchars($name ?? '') ?></h2>
                        <h3><?= htmlspecialchars($role ?? '') ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">
                                    Overview
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">
                                    Edit Profile
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">
                                    Change Password
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content pt-2">
                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">Profile Details</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Full Name</div>
                                    <div class="col-lg-9 col-md-8"><?= htmlspecialchars($name ?? '') ?></div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Role</div>
                                    <div class="col-lg-9 col-md-8"><?= htmlspecialchars($role ?? '') ?></div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8"><?= htmlspecialchars($email ?? '') ?></div>
                                </div>
                            </div>

                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                <!-- Profile Edit Form -->
                                <form method="post" action="">
                                    <input type="hidden" name="action" value="update_profile" />

                                    <div class="row mb-3">
                                        <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="fullName" type="text" class="form-control" id="fullName"
                                                value="<?= htmlspecialchars($name ?? '') ?>" required />
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="email" type="email" class="form-control" id="Email"
                                                value="<?= htmlspecialchars($email ?? '') ?>" required />
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                                <!-- End Profile Edit Form -->
                            </div>

                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <!-- Change Password Form -->
                                <form method="post" action="">
                                    <input type="hidden" name="action" value="change_password" />
                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="password" type="password" class="form-control"
                                                id="currentPassword" required />
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newpassword" type="password" class="form-control"
                                                id="newPassword" minlength="6" required />
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="renewpassword" type="password" class="form-control"
                                                id="renewPassword" minlength="6" required />
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">
                                            Change Password
                                        </button>
                                    </div>
                                </form>
                                <!-- End Change Password Form -->
                            </div>
                        </div>
                        <!-- End Bordered Tabs -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- End #main -->
<?php 
include 'includes/footer.php';
?>