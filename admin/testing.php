<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container py-4">
    <h2 class="mb-4">Inventory Verification Dashboard</h2>

    <!-- Run Verification Button -->
    <button id="runVerificationBtn" class="btn btn-primary mb-3">
        <i class="bi bi-shield-check"></i> Run Verification
    </button>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center my-3 d-none">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2">Running verification, please wait...</p>
    </div>

    <!-- Status Summary -->
    <div id="verificationSummary" class="row g-3 d-none">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Status</h5>
                    <span id="statusBadge" class="badge bg-secondary">—</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Errors</h5>
                    <span id="errorCount" class="badge bg-danger">0</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Warnings</h5>
                    <span id="warningCount" class="badge bg-warning text-dark">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div id="verificationResults" class="mt-4 d-none">

        <!-- Errors Section -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-danger text-white">
                <strong>Errors Detected</strong>
            </div>
            <div class="card-body">
                <ul id="errorList" class="list-group"></ul>
            </div>
        </div>

        <!-- Warnings Section -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <strong>Warnings</strong>
            </div>
            <div class="card-body">
                <ul id="warningList" class="list-group"></ul>
            </div>
        </div>
    </div>
</div>

<script src="./api/verify.js"></script>
<?php 
include 'includes/footer.php';
?>