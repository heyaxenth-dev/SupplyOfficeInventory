<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" method="POST" action="api/add-user-handler.php">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="department" name="department">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required
                                minlength="6">
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required minlength="6">
                        </div>
                    </div>

                    <!-- Combined show/hide checkbox -->
                    <div class="col-12 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showBoth">
                            <label class="form-check-label" for="showBoth">Show Passwords</label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" action="api/edit-user-handler.php">
                <input type="hidden" id="edit_user_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="edit_department" name="department">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password"
                                minlength="6">
                            <small class="form-text text-muted">Leave blank to keep current password</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="edit_confirm_password"
                                name="confirm_password" minlength="6">
                        </div>
                    </div>

                    <!-- Combined checkbox -->
                    <div class="col-12 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showEditPasswords">
                            <label class="form-check-label" for="showEditPasswords">Show Passwords</label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewUserModalLabel">
                    <i class="bi bi-info-circle"></i> User Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h4 class="mb-0" id="view_user_name"></h4>
                        <p class="text-muted mb-0" id="view_user_email"></p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Role</label>
                        <p id="view_user_role" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Department</label>
                        <p id="view_user_department" class="mb-0"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted">Date Created</label>
                        <p id="view_user_created_at" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle add form submission
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();

        // Validate passwords match
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Passwords do not match!'
            });
            return;
        }

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...'
        );

        $.ajax({
            url: 'api/add-user-handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'User added successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();

                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message ||
                            'Failed to add user. Please try again.'
                    });

                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                var errorMessage =
                    'An error occurred while adding the user. Please try again.';
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

    // Handle edit button click
    $(document).on('click', '.edit-user', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var role = $(this).data('role');
        var department = $(this).data('department');

        $('#edit_user_id').val(id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_role').val(role);
        $('#edit_department').val(department);
        $('#edit_password').val('');
        $('#edit_confirm_password').val('');

        $('#editUserModal').modal('show');
    });

    // Handle edit form submission
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();

        var password = $('#edit_password').val();
        var confirmPassword = $('#edit_confirm_password').val();

        if (password && password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Passwords do not match!'
            });
            return;
        }

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
        );

        $.ajax({
            url: 'api/edit-user-handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'User updated successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $('#editUserModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message ||
                            'Failed to update user. Please try again.'
                    });

                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                var errorMessage =
                    'An error occurred while updating the user. Please try again.';
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

    // Handle view button click
    $(document).on('click', '.view-user', function() {
        var name = $(this).data('name');
        var email = $(this).data('email');
        var role = $(this).data('role');
        var department = $(this).data('department');
        var createdAt = $(this).data('created-at');

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

        var roleClass = '';
        var roleIcon = '';
        if (role === 'Admin') {
            roleClass = 'badge bg-danger';
            roleIcon = '<i class="bi bi-shield-check"></i>';
        } else if (role === 'Staff') {
            roleClass = 'badge bg-primary';
            roleIcon = '<i class="bi bi-person"></i>';
        } else {
            roleClass = 'badge bg-secondary';
        }

        $('#view_user_name').text(name);
        $('#view_user_email').text(email);
        $('#view_user_role').html('<span class="' + roleClass + '">' + roleIcon + ' ' + role +
            '</span>');
        $('#view_user_department').text(department || 'N/A');
        $('#view_user_created_at').text(formatDateTime(createdAt));

        $('#viewUserModal').modal('show');
    });

    // Handle delete button click
    $(document).on('click', '.delete-user', function() {
        var id = $(this).data('id');
        var userName = $(this).data('name');
        var currentUserId = '<?php echo $_SESSION['admin_id'] ?? 0; ?>';

        // Prevent deleting own account
        if (id == currentUserId) {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Delete',
                text: 'You cannot delete your own account!'
            });
            return;
        }

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
                    url: 'api/delete-user-handler.php',
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
                                    'User has been deleted.',
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
                                    'Failed to delete user. Please try again.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage =
                            'An error occurred while deleting the user. Please try again.';
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

    // Reset forms when modals are closed
    $('#addUserModal').on('hidden.bs.modal', function() {
        $('#addUserForm')[0].reset();
        $('#addUserForm').find('button[type="submit"]').prop('disabled', false).html('Add User');
        // Reset password visibility
        $('#showBoth').prop('checked', false);
        $('#password').attr('type', 'password');
        $('#confirm_password').attr('type', 'password');
    });

    $('#editUserModal').on('hidden.bs.modal', function() {
        $('#editUserForm')[0].reset();
        $('#editUserForm').find('button[type="submit"]').prop('disabled', false).html('Update User');
        // Reset password visibility
        $('#showEditPasswords').prop('checked', false);
        $('#edit_password').attr('type', 'password');
        $('#edit_confirm_password').attr('type', 'password');
    });

    // Handle show password checkbox for Add User form
    // Use event delegation to handle dynamically loaded modal content
    $(document).on('change', '#showBoth', function() {
        var isChecked = $(this).is(':checked');
        var type = isChecked ? 'text' : 'password';

        $('#password').attr('type', type);
        $('#confirm_password').attr('type', type);
    });

    // Also bind when modal is shown (backup method)
    $('#addUserModal').on('shown.bs.modal', function() {
        $('#showBoth').off('change').on('change', function() {
            var isChecked = $(this).is(':checked');
            var type = isChecked ? 'text' : 'password';
            $('#password').attr('type', type);
            $('#confirm_password').attr('type', type);
        });
    });

    // Handle show password checkbox for Edit User form
    $(document).on('change', '#showEditPasswords', function() {
        var isChecked = $(this).is(':checked');
        var type = isChecked ? 'text' : 'password';

        $('#edit_password').attr('type', type);
        $('#edit_confirm_password').attr('type', type);
    });

    // Also bind when edit modal is shown (backup method)
    $('#editUserModal').on('shown.bs.modal', function() {
        $('#showEditPasswords').off('change').on('change', function() {
            var isChecked = $(this).is(':checked');
            var type = isChecked ? 'text' : 'password';
            $('#edit_password').attr('type', type);
            $('#edit_confirm_password').attr('type', type);
        });
    });

    // Reset password visibility when modals are closed
    $('#addUserModal').on('hidden.bs.modal', function() {
        $('#showBoth').prop('checked', false);
        $('#password').attr('type', 'password');
        $('#confirm_password').attr('type', 'password');
    });

    $('#editUserModal').on('hidden.bs.modal', function() {
        $('#showEditPasswords').prop('checked', false);
        $('#edit_password').attr('type', 'password');
        $('#edit_confirm_password').attr('type', 'password');
    });

});
</script>