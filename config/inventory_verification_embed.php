<?php
/**
 * Embedded inventory verification panel + edit modal (admin/inventory.php and client/inventory.php).
 * Requires Bootstrap JS and jQuery (same as verification dashboard).
 */
require_once __DIR__ . '/inventory_categories.php';
?>
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card border-primary border-opacity-25">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3"
                role="button"
                data-bs-toggle="collapse"
                data-bs-target="#embeddedInventoryVerification"
                aria-expanded="false"
                aria-controls="embeddedInventoryVerification">
                <div>
                    <i class="bi bi-shield-check text-primary me-2"></i>
                    <strong>Inventory data verification</strong>
                    <span class="text-muted small ms-1 d-none d-md-inline">— check quantity, status, and related records</span>
                </div>
                <span class="small text-primary"><i class="bi bi-chevron-expand me-1"></i>Show / hide</span>
            </div>
            <div class="collapse" id="embeddedInventoryVerification">
                <div class="card-body border-top">
                    <p class="text-muted small mb-3">Same checks as the Formal Verification page. Use <strong>Edit</strong> on a finding to correct the item.</p>
                    <div class="mb-4">
                        <button id="runVerificationBtn" type="button" class="btn btn-primary">
                            <i class="bi bi-play-circle"></i> Run verification
                        </button>
                        <button id="loadingBtn" type="button" class="btn btn-primary d-none" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Running verification…
                        </button>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small">Status</h6>
                        <div id="verification-status" class="py-2">
                            <div class="alert alert-secondary mb-0" role="alert">
                                <i class="bi bi-info-circle"></i> Expand this panel and click <strong>Run verification</strong> to start.
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small">Summary</h6>
                        <div id="summary-panel" class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="card bg-primary text-white mb-0">
                                    <div class="card-body text-center py-3">
                                        <div class="small">Total items</div>
                                        <h4 class="mb-0" id="summary-total-items">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-info text-white mb-0">
                                    <div class="card-body text-center py-3">
                                        <div class="small">Transactions</div>
                                        <h4 class="mb-0" id="summary-total-transactions">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-danger text-white mb-0">
                                    <div class="card-body text-center py-3">
                                        <div class="small">Errors</div>
                                        <h4 class="mb-0" id="summary-error-count">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-warning text-white mb-0">
                                    <div class="card-body text-center py-3">
                                        <div class="small">Warnings</div>
                                        <h4 class="mb-0" id="summary-warning-count">-</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <h6 class="d-flex justify-content-between align-items-center">
                                <span>Errors</span>
                                <span class="badge bg-danger" id="error-badge">0</span>
                            </h6>
                            <div id="error-list" class="mt-2">
                                <div class="alert alert-light border small mb-0">
                                    <i class="bi bi-info-circle"></i> No results yet.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="d-flex justify-content-between align-items-center">
                                <span>Warnings</span>
                                <span class="badge bg-warning text-dark" id="warning-badge">0</span>
                            </h6>
                            <div id="warning-list" class="mt-2">
                                <div class="alert alert-light border small mb-0">
                                    <i class="bi bi-info-circle"></i> No results yet.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verificationEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit inventory item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationEditForm">
                <input type="hidden" id="ve_item_id" name="id">
                <input type="hidden" id="ve_stock_number_hidden" name="stock_number">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ve_item_name" class="form-label">Item name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ve_item_name" name="item_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ve_stock_number" class="form-label">Stock number</label>
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
                                <option value="">Select category</option>
                                <?php foreach (getInventoryCategories() as $inv_cat): ?>
                                <option value="<?php echo htmlspecialchars($inv_cat); ?>"><?php echo htmlspecialchars($inv_cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ve_unit_of_measure" class="form-label">Unit of measure</label>
                            <select class="form-select" id="ve_unit_of_measure" name="unit_of_measure">
                                <option value="">Select unit</option>
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
                            <label for="ve_unit_value" class="form-label">Unit value (₱)</label>
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
                            <label for="ve_last_restocked" class="form-label">Last restocked</label>
                            <input type="date" class="form-control" id="ve_last_restocked" name="last_restocked">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update item</button>
                </div>
            </form>
        </div>
    </div>
</div>
