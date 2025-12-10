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

<?php 
include 'includes/footer.php';
?>

<!-- Verification CSS -->
<link href="assets/css/verification.css" rel="stylesheet">
<!-- SweetAlert2 -->
<script src="assets/js/sweetalert2.all.min.js"></script>
<!-- Verification JavaScript -->
<script src="assets/js/verification.js"></script>

