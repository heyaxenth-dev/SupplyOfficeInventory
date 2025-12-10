<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold">User Management</h1>
            <p>Manage system users and their access permissions.</p>
        </div>
        <div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus"></i> Add User
            </button>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body mt-3">
                        <!-- Table with stripped rows -->
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch all users from database
                                // Try to include created_at if column exists
                                $columnsCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
                                $hasCreatedAt = $columnsCheck->num_rows > 0;
                                
                                if ($hasCreatedAt) {
                                    $sql = "SELECT id, name, email, role, department, created_at FROM users ORDER BY created_at DESC";
                                } else {
                                    $sql = "SELECT id, name, email, role, department FROM users ORDER BY id DESC";
                                }
                                $result = $conn->query($sql);
                                
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $roleClass = '';
                                        switch($row['role']) {
                                            case 'Admin':
                                                $roleClass = 'badge bg-danger';
                                                break;
                                            case 'Staff':
                                                $roleClass = 'badge bg-primary';
                                                break;
                                            default:
                                                $roleClass = 'badge bg-secondary';
                                        }
                                        
                                        // Get created_at if it exists
                                        $dateCreated = 'N/A';
                                        if (isset($row['created_at']) && $row['created_at']) {
                                            $dateCreated = date('M d, Y', strtotime($row['created_at']));
                                        }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><span
                                            class="<?php echo $roleClass; ?>"><?php echo htmlspecialchars($row['role']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['department'] ?? 'N/A'); ?></td>
                                    <td><?php echo $dateCreated; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info border-0 view-user"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                            data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                            data-department="<?php echo htmlspecialchars($row['department'] ?? ''); ?>"
                                            data-created-at="<?php echo isset($row['created_at']) ? $row['created_at'] : ''; ?>"
                                            title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary border-0 edit-user"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                            data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                            data-department="<?php echo htmlspecialchars($row['department'] ?? ''); ?>"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger border-0 delete-user"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center">No users found. Click "Add User" to get started.
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->

<?php 
include 'add-user.php';
include 'includes/footer.php';
?>

<!-- SweetAlert2 -->
<script src="assets/js/sweetalert2.all.min.js"></script>

<script>
// DataTable is initialized in main.js
// No need to initialize here to avoid reinitialization error
</script>