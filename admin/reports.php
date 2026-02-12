<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';
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

                    <form action="generated_report.php" method="GET"
                        class="d-flex flex-column align-items-center gap-3" id="reportForm">
                        <!-- From Month & Year -->
                        <div class="w-75">
                            <label for="reportMonthFrom" class="form-label">From (Month & Year)</label>
                            <input type="month" id="reportMonthFrom" name="reportMonthFrom" class="form-control" required>
                        </div>

                        <!-- To Month & Year -->
                        <div class="w-75">
                            <label for="reportMonthTo" class="form-label">To (Month & Year)</label>
                            <input type="month" id="reportMonthTo" name="reportMonthTo" class="form-control" required>
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