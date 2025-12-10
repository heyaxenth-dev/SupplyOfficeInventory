<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get filter parameters
$reportMonth = isset($_GET['reportMonth']) ? $_GET['reportMonth'] : date('Y-m');

// Validate and parse month
if (!preg_match('/^\d{4}-\d{2}$/', $reportMonth)) {
    $reportMonth = date('Y-m');
}

// Parse month and year
$year = substr($reportMonth, 0, 4);
$month = substr($reportMonth, 5, 2);
$monthName = date('F', mktime(0, 0, 0, $month, 1, $year));

// Build SQL query to filter by month (based on created_at or updated_at)
$startDate = $year . '-' . $month . '-01';
$endDate = date('Y-m-t', strtotime($startDate)); // Last day of the month
$reportDate = date('F d, Y', strtotime($endDate)); // Format: "January 31, 2025"

// Fetch all inventory items (as of the report date)
// The report shows current inventory status as of the selected month
$sql = "SELECT * FROM inventory 
        ORDER BY item_name ASC";
$result = $conn->query($sql);
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Report for <?php echo $monthName . ' ' . $year; ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="reports.php">Reports</a></li>
                <li class="breadcrumb-item active">Report for <?php echo $monthName . ' ' . $year; ?></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end mb-3 mt-3 gap-2 no-print">
                            <a href="reports.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button onclick="window.print()" class="btn btn-success">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <button onclick="exportToPDF()" class="btn btn-danger">
                                <i class="bi bi-file-pdf"></i> Export PDF
                            </button>
                        </div>

                        <!-- Official Report Header -->
                        <div class="report-header text-center mb-4">
                            <h3 class="fw-bold mb-2">REPORT ON THE PHYSICAL COUNT OF INVENTORIES</h3>
                            <h4 class="fw-bold mb-3">OFFICE SUPPLIES (Adjusted)</h4>
                            <p class="mb-1"><strong>As at <?php echo $reportDate; ?></strong></p>
                            <p class="mb-4"><strong>Fund Cluster: GENERAL FUND (101)</strong></p>

                            <div class="row text-start mb-3">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>For which</strong>
                                        <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : '________________'; ?>,
                                    </p>
                                    <p class="text-muted small">(Name of accountable officer)</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Supply Officer I</strong></p>
                                    <p class="text-muted small">(Official Designation)</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>University of Antique-Hamtic Campus,</strong></p>
                                    <p class="text-muted small">(Bureau or Office)</p>
                                </div>
                            </div>
                            <p class="mb-4"><strong>is accountable having assumed such accountability on
                                    <?php echo $reportDate; ?>.</strong></p>
                        </div>

                        <div class="mb-3 no-print">
                            <p class="text-muted">Showing results for: <strong
                                    class="text-primary"><?php echo $monthName . ' ' . $year; ?></strong></p>
                        </div>

                        <!-- Table with inventory data - Official Format -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="reportTable">
                                <thead>
                                    <tr>
                                        <th rowspan="2">ARTICLE</th>
                                        <th rowspan="2">DESCRIPTION</th>
                                        <th rowspan="2">Stock Number</th>
                                        <th rowspan="2">Unit of Measure</th>
                                        <th rowspan="2">Unit Value</th>
                                        <th colspan="2">BALANCE PER CARD</th>
                                        <th colspan="2">ON HAND PER COUNT</th>
                                        <th colspan="2">SHORTAGE/OVERAGE</th>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <th>Value</th>
                                        <th>Quantity</th>
                                        <th>Value</th>
                                        <th>Quantity</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalBalanceValue = 0;
                                    $totalOnHandValue = 0;
                                    $totalShortageValue = 0;
                                    $totalItems = 0;
                                    
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $unitValue = $row['unit_value'] ? floatval($row['unit_value']) : 0;
                                            $quantity = intval($row['quantity']); // This is ON HAND PER COUNT
                                            
                                            // For this report, BALANCE PER CARD = ON HAND PER COUNT (since we don't track separately)
                                            $balancePerCardQty = $quantity;
                                            $onHandPerCountQty = $quantity;
                                            
                                            // Calculate values
                                            $balancePerCardValue = $unitValue * $balancePerCardQty;
                                            $onHandPerCountValue = $unitValue * $onHandPerCountQty;
                                            
                                            // SHORTAGE/OVERAGE (difference between balance and on hand)
                                            $shortageOverageQty = $onHandPerCountQty - $balancePerCardQty;
                                            $shortageOverageValue = $unitValue * $shortageOverageQty;
                                            
                                            $totalBalanceValue += $balancePerCardValue;
                                            $totalOnHandValue += $onHandPerCountValue;
                                            $totalShortageValue += $shortageOverageValue;
                                            $totalItems++;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['stock_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['unit_of_measure'] ?? ''); ?></td>
                                        <td><?php echo $unitValue > 0 ? '₱' . number_format($unitValue, 2) : ''; ?></td>
                                        <td><?php echo number_format($balancePerCardQty); ?></td>
                                        <td><?php echo $balancePerCardValue > 0 ? '₱' . number_format($balancePerCardValue, 2) : ''; ?>
                                        </td>
                                        <td><?php echo number_format($onHandPerCountQty); ?></td>
                                        <td><?php echo $onHandPerCountValue > 0 ? '₱' . number_format($onHandPerCountValue, 2) : ''; ?>
                                        </td>
                                        <td><?php echo $shortageOverageQty != 0 ? number_format($shortageOverageQty) : ''; ?>
                                        </td>
                                        <td><?php echo $shortageOverageValue != 0 ? '₱' . number_format(abs($shortageOverageValue), 2) : ''; ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            No records found for <?php echo $monthName . ' ' . $year; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <?php if ($result && $result->num_rows > 0) { ?>
                                <tfoot>
                                    <tr class="table-info fw-bold">
                                        <th colspan="5" class="text-end">TOTAL:</th>
                                        <th><?php echo number_format($totalItems); ?></th>
                                        <th>₱<?php echo number_format($totalBalanceValue, 2); ?></th>
                                        <th><?php echo number_format($totalItems); ?></th>
                                        <th>₱<?php echo number_format($totalOnHandValue, 2); ?></th>
                                        <th><?php echo $totalShortageValue != 0 ? number_format($totalShortageValue) : ''; ?>
                                        </th>
                                        <th><?php echo $totalShortageValue != 0 ? '₱' . number_format(abs($totalShortageValue), 2) : ''; ?>
                                        </th>
                                    </tr>
                                </tfoot>
                                <?php } ?>
                            </table>
                        </div>

                        <!-- Summary Cards (Screen Only) -->
                        <?php if ($result && $result->num_rows > 0) { ?>
                        <div class="row mt-4 no-print">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Items</h6>
                                        <h3><?php echo number_format($totalItems); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Inventory Value</h6>
                                        <h3>₱<?php echo number_format($totalOnHandValue, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Report Period</h6>
                                        <h5><?php echo $monthName . ' ' . $year; ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<style>
/* Print Styles - Official Report Format */
@media print {
    @page {
        size: A4;
        margin: 1.5cm 1cm;
    }

    body * {
        visibility: hidden;
    }

    #main,
    #main * {
        visibility: visible;
    }

    #main {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .no-print,
    .btn,
    .pagetitle,
    .pagetitle nav,
    .sidebar,
    .header,
    .footer {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }

    .card-body {
        padding: 0 !important;
    }

    /* Official Report Header */
    .report-header {
        margin-bottom: 20px !important;
        page-break-after: avoid;
    }

    .report-header h3 {
        font-size: 14px !important;
        font-weight: bold !important;
        text-transform: uppercase;
        margin-bottom: 5px !important;
    }

    .report-header h4 {
        font-size: 13px !important;
        font-weight: bold !important;
        margin-bottom: 10px !important;
    }

    .report-header p {
        font-size: 11px !important;
        margin-bottom: 3px !important;
    }

    .report-header .row {
        font-size: 10px !important;
        margin-bottom: 10px !important;
    }

    .report-header .text-muted {
        font-size: 9px !important;
    }

    /* Table Styles */
    table {
        border-collapse: collapse !important;
        width: 100% !important;
        font-size: 9px !important;
        margin-top: 10px !important;
    }

    table th,
    table td {
        border: 1px solid #000 !important;
        padding: 4px 3px !important;
        text-align: left;
        vertical-align: middle;
    }

    table th {
        background-color: #f0f0f0 !important;
        font-weight: bold !important;
        text-align: center !important;
        font-size: 8px !important;
    }

    table thead th {
        background-color: #e0e0e0 !important;
    }

    table tfoot th {
        background-color: #d0d0d0 !important;
        font-weight: bold !important;
    }

    /* Column widths for better fit */
    table td:nth-child(1),
    table th:nth-child(1) {
        width: 12%;
    }

    table td:nth-child(2),
    table th:nth-child(2) {
        width: 15%;
    }

    table td:nth-child(3),
    table th:nth-child(3) {
        width: 8%;
    }

    table td:nth-child(4),
    table th:nth-child(4) {
        width: 8%;
    }

    table td:nth-child(5),
    table th:nth-child(5) {
        width: 8%;
        text-align: right;
    }

    table td:nth-child(6),
    table th:nth-child(6),
    table td:nth-child(7),
    table th:nth-child(7),
    table td:nth-child(8),
    table th:nth-child(8),
    table td:nth-child(9),
    table th:nth-child(9),
    table td:nth-child(10),
    table th:nth-child(10),
    table td:nth-child(11),
    table th:nth-child(11) {
        width: 7%;
        text-align: right;
    }
}

/* Screen Styles */
.no-print {
    display: block;
}

.report-header {
    border-bottom: 2px solid #333;
    padding-bottom: 15px;
    margin-bottom: 20px;
}
</style>

<?php 
include 'includes/footer.php';
?>

<!-- SweetAlert2 -->
<script src="assets/js/sweetalert2.all.min.js"></script>

<script>
// Export to PDF function - uses browser print dialog
function exportToPDF() {
    // Use browser print dialog (user can save as PDF)
    Swal.fire({
        icon: 'info',
        title: 'Export to PDF',
        html: 'Click OK to open the print dialog.<br><br>In the print dialog, select "Save as PDF" as the destination.',
        confirmButtonText: 'OK',
        showCancelButton: true,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.print();
        }
    });
}
</script>