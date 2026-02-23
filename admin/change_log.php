<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Check if audit table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'inventory_audit_log'");
$logs = [];
if ($tableExists->num_rows > 0) {
    $sql = "SELECT * FROM inventory_audit_log ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
    }
}
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1 class="fw-bold">Change Log</h1>
        <p>Track who made what changes to the inventory.</p>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php if ($tableExists->num_rows == 0): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Audit log table not found.</strong> Run the SQL script 
                            <code>config/inventory_audit_log.sql</code> to create the table and start tracking changes.
                        </div>
                        <?php elseif (empty($logs)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No changes recorded yet. Inventory changes will appear here once items are added, edited, or deleted.
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="changeLogTable">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                        <th>Item</th>
                                        <th>Stock No.</th>
                                        <th>Changes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></td>
                                        <td><strong><?php echo htmlspecialchars($log['user_name']); ?></strong></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($log['user_role'] ?? '-'); ?></span></td>
                                        <td>
                                            <?php
                                            $actionLabels = ['ADD' => 'Added Item', 'EDIT' => 'Updated Stocks', 'DELETE' => 'Deleted Item'];
                                            $actionLabel = $actionLabels[$log['action']] ?? $log['action'];
                                            $actionClass = $log['action'] === 'ADD' ? 'success' : ($log['action'] === 'DELETE' ? 'danger' : 'primary');
                                            ?>
                                            <span class="badge bg-<?php echo $actionClass; ?>"><?php echo htmlspecialchars($actionLabel); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['item_name'] ?? '-'); ?></td>
                                        <td><code><?php echo htmlspecialchars(!empty($log['stock_number']) ? $log['stock_number'] : '-'); ?></code></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($log['changes_summary'] ?? '-'); ?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<?php 
include 'includes/footer.php';
?>

<script>
// Initialize DataTable if table has data
$(document).ready(function() {
    if ($('#changeLogTable tbody tr').length > 0) {
        $('#changeLogTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25
        });
    }
});
</script>
