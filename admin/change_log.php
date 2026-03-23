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

<style>
@media print {

    #header,
    #sidebar,
    aside.sidebar,
    footer,
    .toggle-sidebar-btn,
    .no-print,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate,
    .dt-buttons {
        display: none !important;
    }

    body {
        background: #fff !important;
    }

    #main {
        margin: 0 !important;
        padding: 1rem !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    #changeLogTable {
        width: 100% !important;
        font-size: 10pt;
    }

    #changeLogTable th,
    #changeLogTable td {
        border: 1px solid #333 !important;
        padding: 0.35rem !important;
    }

    .badge {
        border: 1px solid #333 !important;
        color: #000 !important;
        background: #f5f5f5 !important;
    }
}
</style>

<main id="main" class="main">

    <div class="pagetitle d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="fw-bold">Change Log</h1>
            <p class="mb-0">Track who made what changes to the inventory.</p>
        </div>
        <?php if ($tableExists->num_rows > 0 && !empty($logs)): ?>
        <div class="no-print">
            <button type="button" class="btn btn-outline-primary" id="btnPrintChangeLog" title="Print this table">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
        <?php endif; ?>
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
                            No changes recorded yet. Inventory changes will appear here once items are added, edited, or
                            deleted.
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
                                        <td><span
                                                class="badge bg-secondary"><?php echo htmlspecialchars($log['user_role'] ?? '-'); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $actionLabels = ['ADD' => 'Added Item', 'EDIT' => 'Updated Stocks', 'DELETE' => 'Deleted Item'];
                                            // Normalize action (legacy rows may be empty due to old bind_param bug)
                                            $rawAction = trim((string)($log['action'] ?? ''));
                                            $sum = (string)($log['changes_summary'] ?? '');
                                            if ($rawAction === '' || $rawAction === '0') {
                                                if (stripos($sum, 'Added:') === 0) {
                                                    $rawAction = 'ADD';
                                                } elseif (stripos($sum, 'deleted') !== false || stripos($sum, 'Item deleted') !== false) {
                                                    $rawAction = 'DELETE';
                                                } else {
                                                    $rawAction = 'EDIT';
                                                }
                                            }
                                            $isDistribute = (stripos($sum, 'Distributed ') === 0);
                                            if ($isDistribute) {
                                                $actionLabel = 'Distributed';
                                                $actionClass = 'info';
                                            } else {
                                                $actionLabel = $actionLabels[$rawAction] ?? $rawAction;
                                                $actionClass = $rawAction === 'ADD' ? 'success' : ($rawAction === 'DELETE' ? 'danger' : 'primary');
                                            }
                                            ?>
                                            <span
                                                class="badge bg-<?php echo $actionClass; ?>"><?php echo htmlspecialchars($actionLabel ?: '-'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['item_name'] ?? '-'); ?></td>
                                        <td><code><?php echo htmlspecialchars(!empty($log['stock_number']) ? $log['stock_number'] : '-'); ?></code>
                                        </td>
                                        <td><small
                                                class="text-muted"><?php echo htmlspecialchars($log['changes_summary'] ?? '-'); ?></small>
                                        </td>
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
            order: [
                [0, 'desc']
            ],
            pageLength: 25
        });

        $('#btnPrintChangeLog').on('click', function() {
            var table = $('#changeLogTable').DataTable();
            var prevLen = table.page.len();
            // Show all rows for printing
            table.page.len(-1).draw(false);
            window.print();
            setTimeout(function() {
                table.page.len(prevLen).draw(false);
            }, 500);
        });
    }
});
</script>