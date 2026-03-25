<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Filter dropdown options for the report generator
$validStatuses = ['In Stock', 'Low Stock', 'Out of Stock'];

$categoryOptions = [];
$categoryRes = $conn->query("SELECT DISTINCT category
    FROM inventory
    WHERE category IS NOT NULL AND category <> ''
    ORDER BY category ASC");
if ($categoryRes) {
    while ($r = $categoryRes->fetch_assoc()) {
        $categoryOptions[] = $r['category'];
    }
}

$departmentOptions = [];
$hasDistributions = false;
$tableCheck = $conn->query("SHOW TABLES LIKE 'inventory_distributions'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $hasDistributions = true;
}
if ($hasDistributions) {
    $departmentRes = $conn->query("SELECT DISTINCT department
        FROM inventory_distributions
        WHERE department IS NOT NULL AND department <> ''
        ORDER BY department ASC");
    if ($departmentRes) {
        while ($r = $departmentRes->fetch_assoc()) {
            $departmentOptions[] = $r['department'];
        }
    }
}
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Generate Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="verification_dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Generate Report</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->


    <section class="section d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg">
                <div class="card-body text-center p-4">
                    <h5 class="card-title mb-4">Generate Report</h5>

                    <form action="generated_report.php" method="GET" class="d-flex flex-column align-items-center gap-3"
                        id="reportForm">
                        <!-- From Month & Year -->
                        <div class="w-75">
                            <label for="reportMonthFrom" class="form-label">From (Month & Year)</label>
                            <input type="month" id="reportMonthFrom" name="reportMonthFrom" class="form-control"
                                required>
                        </div>

                        <!-- To Month & Year -->
                        <div class="w-75">
                            <label for="reportMonthTo" class="form-label">To (Month & Year)</label>
                            <input type="month" id="reportMonthTo" name="reportMonthTo" class="form-control" required>
                        </div>

                        <!-- Status -->
                        <div class="w-75">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <?php foreach ($validStatuses as $s) { ?>
                                <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="w-75">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categoryOptions as $cat) { ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>">
                                    <?php echo htmlspecialchars($cat); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="w-75">
                            <label for="department" class="form-label">Department</label>
                            <select id="department" name="department" class="form-select">
                                <option value="">All Departments</option>
                                <?php foreach ($departmentOptions as $dept) { ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>">
                                    <?php echo htmlspecialchars($dept); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <small class="text-muted">e.g., January 2026 to February 2026</small>

                        <!-- Generate Button -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-danger px-4">Generate Report</button>
                        </div>
                    </form>

                    <script>
                    // Validate that To is not before From
                    document.getElementById('reportForm').addEventListener('submit', function(e) {
                        var from = document.getElementById('reportMonthFrom').value;
                        var to = document.getElementById('reportMonthTo').value;
                        if (from && to && to < from) {
                            e.preventDefault();
                            alert('The "To" date must be on or after the "From" date.');
                            return false;
                        }
                    });
                    </script>

                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<?php 
include 'includes/footer.php';
?>