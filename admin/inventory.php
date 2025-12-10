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
            <h1 class="fw-bold">Inventory</h1>
            <p>Manage and track all supplies in the inventory.</p>
        </div>
        <div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addInventoryModal"><i
                    class="bi bi-plus"></i> Add Item</button>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body mt-3">
                        <!-- <h5 class="card-title">Datatables</h5> -->

                        <!-- Table with stripped rows -->
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Stock Number</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Unit of Measure</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Last Restocked</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch inventory items from database
                                $sql = "SELECT * FROM inventory ORDER BY id DESC";
                                $result = $conn->query($sql);
                                
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $statusClass = '';
                                        switch($row['status']) {
                                            case 'In Stock':
                                                $statusClass = 'badge bg-success';
                                                break;
                                            case 'Low Stock':
                                                $statusClass = 'badge bg-warning';
                                                break;
                                            case 'Out of Stock':
                                                $statusClass = 'badge bg-danger';
                                                break;
                                            default:
                                                $statusClass = 'badge bg-secondary';
                                        }
                                        
                                        $lastRestocked = $row['last_restocked'] ? date('M d, Y', strtotime($row['last_restocked'])) : 'N/A';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['stock_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['unit_of_measure'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($row['quantity']); ?></td>
                                    <td><span
                                            class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                    </td>
                                    <td><?php echo $lastRestocked; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info border-0 view-item"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-item-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                            data-description="<?php echo htmlspecialchars($row['description'] ?? ''); ?>"
                                            data-stock-number="<?php echo htmlspecialchars($row['stock_number']); ?>"
                                            data-category="<?php echo htmlspecialchars($row['category']); ?>"
                                            data-unit-of-measure="<?php echo htmlspecialchars($row['unit_of_measure'] ?? ''); ?>"
                                            data-unit-value="<?php echo $row['unit_value'] ?? ''; ?>"
                                            data-quantity="<?php echo $row['quantity']; ?>"
                                            data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                            data-last-restocked="<?php echo $row['last_restocked'] ?? ''; ?>"
                                            data-created-at="<?php echo $row['created_at'] ?? ''; ?>"
                                            data-updated-at="<?php echo $row['updated_at'] ?? ''; ?>"
                                            title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary border-0 edit-item"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-item-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                            data-description="<?php echo htmlspecialchars($row['description'] ?? ''); ?>"
                                            data-stock-number="<?php echo htmlspecialchars($row['stock_number']); ?>"
                                            data-category="<?php echo htmlspecialchars($row['category']); ?>"
                                            data-unit-of-measure="<?php echo htmlspecialchars($row['unit_of_measure'] ?? ''); ?>"
                                            data-unit-value="<?php echo $row['unit_value'] ?? ''; ?>"
                                            data-quantity="<?php echo $row['quantity']; ?>"
                                            data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                            data-last-restocked="<?php echo $row['last_restocked'] ?? ''; ?>"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger border-0 delete-item"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-item-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="9" class="text-center">No inventory items found. Click "Add Item" to
                                        get started.</td>
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
include 'add-inventory.php';
include 'includes/footer.php';
?>

<!-- SweetAlert2 -->
<script src="assets/js/sweetalert2.all.min.js"></script>

<script>
// DataTable is initialized in main.js
// No need to initialize here to avoid reinitialization error
</script>