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
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover align-middle" id="datatable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Item</th>
                                    <th scope="col">Stock Number</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Unit of Measure</th>
                                    <th scope="col" class="text-end">Quantity</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Last Restocked</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch inventory items from database
                                $sql = "SELECT * FROM inventory ORDER BY id DESC";
                                $result = $conn->query($sql);
                                
                                if ($result && $result->num_rows > 0) {
                                    $lowStockThreshold = 10;
                                    while ($row = $result->fetch_assoc()) {
                                        $quantity = intval($row['quantity']);
                                        $statusDisplay = '';
                                        if ($quantity === 0) {
                                            $statusDisplay = '<span class="badge bg-danger">Out of Stock</span>';
                                        } elseif ($quantity <= $lowStockThreshold) {
                                            $statusDisplay = '<span class="badge bg-warning text-dark">Low Stock</span>';
                                        } else {
                                            $statusDisplay = '<span class="badge bg-success">In Stock</span>';
                                        }
                                        
                                        $lastRestocked = $row['updated_at'] ? date('M d, Y', strtotime($row['updated_at'])) : 'N/A';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['stock_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['unit_of_measure'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($row['quantity']); ?></td>
                                    <td><?php echo $statusDisplay; ?></td>
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
                        </div>

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