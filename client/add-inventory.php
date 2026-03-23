<?php
require_once __DIR__ . '/../config/inventory_categories.php';
?>
<!-- Add Inventory Modal -->
<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="addInventoryModal" tabindex="-1"
    aria-labelledby="addInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInventoryModalLabel">Add New Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addInventoryForm" method="POST" action="api/add-inventory-handler.php">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="item_name" class="form-label">Item Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                            <small class="form-text text-muted">Stock number will be auto-generated based on item
                                name</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach (getInventoryCategories() as $inv_cat): ?>
                                <option value="<?php echo htmlspecialchars($inv_cat); ?>">
                                    <?php echo htmlspecialchars($inv_cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_of_measure" class="form-label">Unit of Measure</label>
                            <select class="form-select" id="unit_of_measure" name="unit_of_measure">
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
                            <label for="unit_value" class="form-label">Unit Value (₱)</label>
                            <input type="number" class="form-control" id="unit_value" name="unit_value" step="0.01"
                                min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-control bg-light" style="padding: 0.5rem 0.75rem;">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto (based on
                                    quantity)</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_restocked" class="form-label">Last Restocked</label>
                            <input type="date" class="form-control" id="last_restocked" name="last_restocked">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle form submission via AJAX
    $('#addInventoryForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();

        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...'
        );

        $.ajax({
            url: 'api/add-inventory-handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message ||
                            'Inventory item added successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Close modal and reset form
                    $('#addInventoryModal').modal('hide');
                    $('#addInventoryForm')[0].reset();

                    // Reload the page to show new item in table
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message ||
                            'Failed to add inventory item. Please try again.'
                    });

                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                // Try to parse error response
                var errorMessage =
                    'An error occurred while adding the inventory item. Please try again.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // If response is not JSON, show the raw response or default message
                    if (xhr.responseText) {
                        errorMessage = xhr.responseText.substring(0, 200);
                    }
                }

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });

                // Re-enable submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Reset form when modal is closed
    $('#addInventoryModal').on('hidden.bs.modal', function() {
        $('#addInventoryForm')[0].reset();
        $('#addInventoryForm').find('button[type="submit"]').prop('disabled', false).html('Add Item');
    });

    // Handle view button click
    $(document).on('click', '.view-item', function() {
        var id = $(this).data('id');
        var itemName = $(this).data('item-name');
        var description = $(this).data('description');
        var stockNumber = $(this).data('stock-number');
        var category = $(this).data('category');
        var unitOfMeasure = $(this).data('unit-of-measure');
        var unitValue = $(this).data('unit-value');
        var quantity = $(this).data('quantity');
        var status = $(this).data('status');
        var lastRestocked = $(this).data('last-restocked');
        var createdAt = $(this).data('created-at');
        var updatedAt = $(this).data('updated-at');

        // Format dates
        var formatDate = function(dateString) {
            if (!dateString || dateString === '') return 'N/A';
            var date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        };

        var formatDateTime = function(dateString) {
            if (!dateString || dateString === '') return 'N/A';
            var date = new Date(dateString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        };

        // Get status display - matches quantity
        var lowStockThreshold = 10;
        var statusHtml = '';
        if (quantity === 0) {
            statusHtml =
                '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Out of Stock</span>';
        } else if (quantity <= lowStockThreshold) {
            statusHtml =
                '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Low Stock</span>';
        } else {
            statusHtml =
                '<span class="badge bg-success"><i class="bi bi-check-circle"></i> In Stock</span>';
        }

        // Number formatting function
        var number_format = function(num) {
            if (!num && num !== 0) return '0';
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };


        // Populate view modal
        $('#view_item_name').text(itemName);
        $('#view_stock_number').text(stockNumber);
        $('#view_description').text(description || 'No description provided');
        $('#view_category').text(category);
        $('#view_unit_of_measure').text(unitOfMeasure || 'N/A');
        $('#view_unit_value').text(unitValue ? '₱' + parseFloat(unitValue).toFixed(2) : 'N/A');
        $('#view_quantity').text(number_format(quantity));
        $('#view_status').html(statusHtml);
        // Last Restocked matches Last Updated (same update timestamp)
        $('#view_last_restocked').text(formatDateTime(updatedAt));
        $('#view_created_at').text(formatDateTime(createdAt));
        $('#view_updated_at').text(formatDateTime(updatedAt));

        // Calculate total value if unit value exists
        if (unitValue && quantity) {
            var totalValue = parseFloat(unitValue) * parseInt(quantity);
            $('#view_total_value').text('₱' + number_format(totalValue.toFixed(2)));
        } else {
            $('#view_total_value').text('N/A');
        }

        // Show view modal
        $('#viewInventoryModal').modal('show');
    });

    // Handle edit button click
    $(document).on('click', '.edit-item', function() {
        var id = $(this).data('id');
        var itemName = $(this).data('item-name');
        var description = $(this).data('description');
        var stockNumber = $(this).data('stock-number');
        var category = $(this).data('category');
        var unitOfMeasure = $(this).data('unit-of-measure');
        var unitValue = $(this).data('unit-value');
        var quantity = $(this).data('quantity');
        var status = $(this).data('status');
        var lastRestocked = $(this).data('last-restocked');

        // Populate edit form
        $('#edit_item_id').val(id);
        $('#edit_item_name').val(itemName);
        $('#edit_description').val(description);
        $('#edit_stock_number').val(stockNumber);
        var $editCat = $('#edit_category');
        $editCat.find('option[data-legacy]').remove();
        $editCat.val(category);
        if (category && $editCat.val() !== String(category)) {
            $editCat.append($('<option>', {
                value: category,
                text: category + ' (legacy)',
                'data-legacy': '1',
                selected: true
            }));
        }
        $('#edit_unit_of_measure').val(unitOfMeasure);
        $('#edit_unit_value').val(unitValue);
        $('#edit_quantity').val(quantity);
        // Status is auto-calculated from quantity - update preview
        var qty = parseInt(quantity) || 0;
        var statusText = qty === 0 ? 'Out of Stock' : (qty <= 10 ? 'Low Stock' : 'In Stock');
        $('#edit_status_preview').html(
            '<small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto: ' + statusText +
            '</small>');
        $('#edit_last_restocked').val(lastRestocked);

        // Show edit modal
        $('#editInventoryModal').modal('show');
    });

    // Update status preview when quantity changes in edit form
    $('#edit_quantity').on('input', function() {
        var qty = parseInt($(this).val()) || 0;
        var statusText = qty === 0 ? 'Out of Stock' : (qty <= 10 ? 'Low Stock' : 'In Stock');
        $('#edit_status_preview').html(
            '<small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto: ' + statusText +
            '</small>');
    });

    // Handle edit form submission
    $('#editInventoryForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();

        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
        );

        $.ajax({
            url: 'api/edit-inventory-handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message ||
                            'Inventory item updated successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $('#editInventoryModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message ||
                            'Failed to update inventory item. Please try again.'
                    });

                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                var errorMessage =
                    'An error occurred while updating the inventory item. Please try again.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    if (xhr.responseText) {
                        errorMessage = xhr.responseText.substring(0, 200);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });

                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Distribute to department — open modal
    $(document).on('click', '.distribute-item', function() {
        var id = $(this).data('id');
        var itemName = $(this).data('item-name');
        var stockNumber = $(this).data('stock-number');
        var qty = parseInt($(this).data('quantity'), 10) || 0;

        $('#distribute_item_id').val(id);
        $('#distribute_item_display').text(itemName);
        $('#distribute_stock_display').text(stockNumber || '-');
        $('#distribute_available_qty').text(qty);
        $('#distribute_department').val('');
        $('#distribute_quantity').val('').attr('max', qty > 0 ? qty : 1);
        if (qty <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Out of stock',
                text: 'This item has no quantity available to distribute.'
            });
            return;
        }
        $('#distributeInventoryModal').modal('show');
    });

    $('#distributeInventoryForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        var maxQ = parseInt($('#distribute_quantity').attr('max'), 10) || 0;
        var reqQ = parseInt($('#distribute_quantity').val(), 10) || 0;
        if (reqQ > maxQ) {
            Swal.fire({ icon: 'error', title: 'Invalid quantity', text: 'Cannot distribute more than available (' + maxQ + ').' });
            return;
        }

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            url: 'api/distribute-inventory-handler.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Distributed',
                        text: response.message || 'Distribution recorded.',
                        showConfirmButton: false,
                        timer: 1800
                    });
                    $('#distributeInventoryModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 1800);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Distribution failed.' });
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    if (r.message) msg = r.message;
                } catch (err) {}
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    $('#distributeInventoryModal').on('hidden.bs.modal', function() {
        $('#distributeInventoryForm')[0].reset();
        $('#distributeInventoryForm').find('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Confirm distribution');
    });

    // Handle delete button click
    $(document).on('click', '.delete-item', function() {
        var id = $(this).data('id');
        var itemName = $(this).data('item-name');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/delete-inventory-handler.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message ||
                                    'Inventory item has been deleted.',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message ||
                                    'Failed to delete inventory item. Please try again.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage =
                            'An error occurred while deleting the inventory item. Please try again.';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            if (xhr.responseText) {
                                errorMessage = xhr.responseText.substring(0, 200);
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });
});
</script>

<!-- Edit Inventory Modal -->
<div class="modal fade" id="editInventoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInventoryModalLabel">Edit Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editInventoryForm" method="POST" action="api/edit-inventory-handler.php">
                <input type="hidden" id="edit_item_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_item_name" class="form-label">Item Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_item_name" name="item_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_stock_number" class="form-label">Stock Number</label>
                            <input type="text" class="form-control" id="edit_stock_number" name="stock_number" readonly>
                            <small class="form-text text-muted">Stock number cannot be changed</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_category" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach (getInventoryCategories() as $inv_cat): ?>
                                <option value="<?php echo htmlspecialchars($inv_cat); ?>">
                                    <?php echo htmlspecialchars($inv_cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_unit_of_measure" class="form-label">Unit of Measure</label>
                            <select class="form-select" id="edit_unit_of_measure" name="unit_of_measure">
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
                            <label for="edit_unit_value" class="form-label">Unit Value (₱)</label>
                            <input type="number" class="form-control" id="edit_unit_value" name="unit_value" step="0.01"
                                min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">Quantity <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity" required
                                min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <div id="edit_status_preview" class="form-control bg-light"
                                style="padding: 0.5rem 0.75rem;">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto (based on
                                    quantity)</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_last_restocked" class="form-label">Last Restocked</label>
                            <input type="date" class="form-control" id="edit_last_restocked" name="last_restocked">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Distribute to Department Modal -->
<div class="modal fade" id="distributeInventoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="distributeInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="distributeInventoryModalLabel">
                    <i class="bi bi-box-arrow-right me-2"></i>Distribute to Department
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="distributeInventoryForm">
                <input type="hidden" id="distribute_item_id" name="id">
                <div class="modal-body">
                    <p class="text-muted small mb-3">Stock will be reduced by the quantity you send to the department.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item</label>
                        <p class="mb-0" id="distribute_item_display"></p>
                        <small class="text-muted">Stock No.: <span id="distribute_stock_display"></span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Available quantity</label>
                        <p class="mb-0 fs-5 fw-bold text-primary" id="distribute_available_qty"></p>
                    </div>
                    <div class="mb-3">
                        <label for="distribute_department" class="form-label">Department <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="distribute_department" name="department" required
                            placeholder="e.g. Accounting, IT, Registrar">
                    </div>
                    <div class="mb-3">
                        <label for="distribute_quantity" class="form-label">Quantity to distribute <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="distribute_quantity" name="quantity" required min="1" step="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Confirm distribution</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Inventory Modal -->
<div class="modal fade" id="viewInventoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="viewInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewInventoryModalLabel">
                    <i class="bi bi-info-circle"></i> Inventory Item Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h4 class="mb-0" id="view_item_name"></h4>
                        <p class="text-muted mb-0">Stock Number: <strong id="view_stock_number"></strong></p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Description</label>
                        <p id="view_description" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Category</label>
                        <p id="view_category" class="mb-0"></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Unit of Measure</label>
                        <p id="view_unit_of_measure" class="mb-0"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Unit Value</label>
                        <p id="view_unit_value" class="mb-0"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Quantity</label>
                        <p id="view_quantity" class="mb-0 fs-5"></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Total Value</label>
                        <p id="view_total_value" class="mb-0 fs-5 text-primary fw-bold"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Status</label>
                        <p id="view_status" class="mb-0"></p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Last Restocked</label>
                        <p id="view_last_restocked" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Date Created</label>
                        <p id="view_created_at" class="mb-0"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted">Last Updated</label>
                        <p id="view_updated_at" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>