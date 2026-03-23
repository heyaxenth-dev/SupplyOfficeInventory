<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
require_once __DIR__ . '/../config/inventory_categories.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold">Formal Verification</h1>
            <p>Verify inventory data integrity and compliance with business rules.</p>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <!-- Run Verification Panel -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Run Verification</h5>
                        <p class="text-muted">Click the button below to run the formal verification algorithm on all inventory data.</p>
                        <button id="runVerificationBtn" class="btn btn-primary btn-lg">
                            <i class="bi bi-play-circle"></i> Run Verification
                        </button>
                        <button id="loadingBtn" class="btn btn-primary btn-lg d-none" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Running Verification...
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Verification Status</h5>
                        <div id="verification-status" class="text-center py-4">
                            <div class="alert alert-secondary" role="alert">
                                <i class="bi bi-info-circle"></i> No verification run yet. Click "Run Verification" to start.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Panel -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Summary</h5>
                        <div id="summary-panel" class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Items</h6>
                                        <h3 id="summary-total-items">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Transactions</h6>
                                        <h3 id="summary-total-transactions">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Errors</h6>
                                        <h3 id="summary-error-count">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Warnings</h6>
                                        <h3 id="summary-warning-count">-</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Errors Panel -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Errors</span>
                            <span class="badge bg-danger" id="error-badge">0</span>
                        </h5>
                        <div id="error-list" class="mt-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No errors found. Run verification to check for issues.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warnings Panel -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Warnings</span>
                            <span class="badge bg-warning" id="warning-badge">0</span>
                        </h5>
                        <div id="warning-list" class="mt-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No warnings found. Run verification to check for issues.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<!-- Edit Inventory Modal (for quick fix from verification results) -->
<div class="modal fade" id="verificationEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationEditForm">
                <input type="hidden" id="ve_item_id" name="id">
                <input type="hidden" id="ve_stock_number_hidden" name="stock_number">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ve_item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ve_item_name" name="item_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ve_stock_number" class="form-label">Stock Number</label>
                            <input type="text" class="form-control" id="ve_stock_number" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="ve_description" class="form-label">Description</label>
                            <textarea class="form-control" id="ve_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ve_category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="ve_category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach (getInventoryCategories() as $inv_cat): ?>
                                <option value="<?php echo htmlspecialchars($inv_cat); ?>"><?php echo htmlspecialchars($inv_cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ve_unit_of_measure" class="form-label">Unit of Measure</label>
                            <select class="form-select" id="ve_unit_of_measure" name="unit_of_measure">
                                <option value="">Select Unit</option>
                                <option value="pc">pc</option>
                                <option value="pc.">pc.</option>
                                <option value="set">set</option>
                                <option value="roll">roll</option>
                                <option value="bot">bot</option>
                                <option value="gallon">gallon</option>
                                <option value="can">can</option>
                                <option value="cake">cake</option>
                                <option value="box">box</option>
                                <option value="pack">pack</option>
                                <option value="bottle">bottle</option>
                                <option value="piece">piece</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ve_unit_value" class="form-label">Unit Value (₱)</label>
                            <input type="number" class="form-control" id="ve_unit_value" name="unit_value" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ve_quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="ve_quantity" name="quantity" required min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <div id="ve_status_preview" class="form-control bg-light" style="padding: 0.5rem 0.75rem;">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto (based on quantity)</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ve_last_restocked" class="form-label">Last Restocked</label>
                            <input type="date" class="form-control" id="ve_last_restocked" name="last_restocked">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php';
?>

<!-- Verification CSS -->
<link href="assets/css/verification.css" rel="stylesheet">
<!-- SweetAlert2 -->
<script src="assets/js/sweetalert2.all.min.js"></script>
<!-- Verification JavaScript -->
<script src="assets/js/verification.js"></script>

