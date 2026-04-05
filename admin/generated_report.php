<?php 
include 'authentication.php';
include 'config/conn.php';
include 'includes/login-credentials.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Get filter parameters - support date range (reportMonthFrom, reportMonthTo) or single month (reportMonth)
$reportMonthFrom = isset($_GET['reportMonthFrom']) ? $_GET['reportMonthFrom'] : null;
$reportMonthTo = isset($_GET['reportMonthTo']) ? $_GET['reportMonthTo'] : null;

// Fallback to single month for backward compatibility
if (!$reportMonthFrom && isset($_GET['reportMonth'])) {
    $reportMonthFrom = $_GET['reportMonth'];
}
if (!$reportMonthTo && isset($_GET['reportMonth'])) {
    $reportMonthTo = $_GET['reportMonth'];
}

// Default to current month if not provided
$today = date('Y-m');
if (!$reportMonthFrom) $reportMonthFrom = $today;
if (!$reportMonthTo) $reportMonthTo = $today;

// Validate format (YYYY-MM)
if (!preg_match('/^\d{4}-\d{2}$/', $reportMonthFrom)) $reportMonthFrom = $today;
if (!preg_match('/^\d{4}-\d{2}$/', $reportMonthTo)) $reportMonthTo = $today;

// Ensure To is not before From
if ($reportMonthTo < $reportMonthFrom) {
    $reportMonthTo = $reportMonthFrom;
}

// Build display strings for the date range
$fromYear = substr($reportMonthFrom, 0, 4);
$fromMonth = substr($reportMonthFrom, 5, 2);
$toYear = substr($reportMonthTo, 0, 4);
$toMonth = substr($reportMonthTo, 5, 2);

$fromMonthName = date('F', mktime(0, 0, 0, $fromMonth, 1, $fromYear));
$toMonthName = date('F', mktime(0, 0, 0, $toMonth, 1, $toYear));

$reportPeriodDisplay = ($reportMonthFrom === $reportMonthTo)
    ? ($fromMonthName . ' ' . $fromYear)
    : ($fromMonthName . ' ' . $fromYear . ' to ' . $toMonthName . ' ' . $toYear);

$startDate = $reportMonthFrom . '-01';
$endDate = date('Y-m-t', strtotime($reportMonthTo . '-01'));
$reportDate = date('F d, Y', strtotime($endDate));

// Match inventory list badges (admin/inventory.php)
$lowStockThreshold = 10;

// Items appear in the report if created OR updated in the range (created_at-only hid most rows)
$inventoryDateRangeSql = "(
    (DATE(i.created_at) >= ? AND DATE(i.created_at) <= ?)
    OR (DATE(i.updated_at) >= ? AND DATE(i.updated_at) <= ?)
)";

// Filters
$selectedStatus = isset($_GET['status']) ? trim($_GET['status']) : '';
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$selectedDepartment = isset($_GET['department']) ? trim($_GET['department']) : '';

// Validate status against known inventory enum values
$validStatuses = ['In Stock', 'Low Stock', 'Out of Stock'];
if ($selectedStatus !== '' && !in_array($selectedStatus, $validStatuses, true)) {
    $selectedStatus = '';
}

// Check if inventory_distributions table exists (department filter depends on it)
$hasDistributions = false;
$tableCheck = $conn->query("SHOW TABLES LIKE 'inventory_distributions'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $hasDistributions = true;
}
if (!$hasDistributions) {
    $selectedDepartment = '';
}

// Build dropdown options based on the selected month/year range
$categoryOptions = [];
$categorySql = "SELECT DISTINCT i.category
        FROM inventory i
        WHERE $inventoryDateRangeSql
          AND i.category IS NOT NULL AND i.category <> ''
        ORDER BY i.category ASC";
$categoryStmt = $conn->prepare($categorySql);
if ($categoryStmt) {
    $categoryStmt->bind_param("ssss", $startDate, $endDate, $startDate, $endDate);
    $categoryStmt->execute();
    $categoryRes = $categoryStmt->get_result();
    if ($categoryRes) {
        while ($r = $categoryRes->fetch_assoc()) {
            $categoryOptions[] = $r['category'];
        }
    }
    $categoryStmt->close();
}

// Ensure the currently selected category remains visible in the dropdown
if ($selectedCategory !== '' && !in_array($selectedCategory, $categoryOptions, true)) {
    $categoryOptions[] = $selectedCategory;
}

$departmentOptions = [];
if ($hasDistributions) {
    $departmentStmt = $conn->prepare("SELECT DISTINCT department
        FROM inventory_distributions
        WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?
          AND department IS NOT NULL AND department <> ''
        ORDER BY department ASC");
    if ($departmentStmt) {
        $departmentStmt->bind_param("ss", $startDate, $endDate);
        $departmentStmt->execute();
        $departmentRes = $departmentStmt->get_result();
        if ($departmentRes) {
            while ($r = $departmentRes->fetch_assoc()) {
                $departmentOptions[] = $r['department'];
            }
        }
        $departmentStmt->close();
    }
}

// Ensure the currently selected department remains visible in the dropdown
if ($selectedDepartment !== '' && !in_array($selectedDepartment, $departmentOptions, true)) {
    $departmentOptions[] = $selectedDepartment;
}

// Fetch inventory rows in date range (+ optional filters)
$sql = "SELECT i.*
        FROM inventory i
        WHERE $inventoryDateRangeSql";
$params = [$startDate, $endDate, $startDate, $endDate];
$types = 'ssss';

// Status from quantity (matches on-screen badges; DB status column can be stale)
if ($selectedStatus === 'Out of Stock') {
    $sql .= ' AND i.quantity = 0';
} elseif ($selectedStatus === 'Low Stock') {
    $sql .= ' AND i.quantity > 0 AND i.quantity <= ?';
    $params[] = (int) $lowStockThreshold;
    $types .= 'i';
} elseif ($selectedStatus === 'In Stock') {
    $sql .= ' AND i.quantity > ?';
    $params[] = (int) $lowStockThreshold;
    $types .= 'i';
}

if ($selectedCategory !== '') {
    $sql .= ' AND i.category = ?';
    $params[] = $selectedCategory;
    $types .= 's';
}

if ($hasDistributions && $selectedDepartment !== '') {
    $sql .= ' AND EXISTS (
        SELECT 1 FROM inventory_distributions d
        WHERE d.inventory_id = i.id
          AND d.department = ?
          AND DATE(d.created_at) >= ? AND DATE(d.created_at) <= ?
    )';
    $params[] = $selectedDepartment;
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= 'sss';
}

$sql .= ' ORDER BY i.item_name ASC';

$stmt = $conn->prepare($sql);
$result = false;
if ($stmt) {
    $typeStr = $types;
    $bindArgs = [&$typeStr];
    for ($i = 0; $i < count($params); $i++) {
        $bindArgs[] = &$params[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bindArgs);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Extra columns when filters are applied (matches filter summary)
$showFilterStatusCol = ($selectedStatus !== '');
$showFilterCategoryCol = ($selectedCategory !== '');
$showFilterDepartmentCol = ($selectedDepartment !== '');
$filterExtraCols = (int) $showFilterStatusCol + (int) $showFilterCategoryCol + (int) $showFilterDepartmentCol;
$reportTableColCount = 11 + $filterExtraCols;
$tfootLabelColspan = 5 + $filterExtraCols;
$printNumericColStart = 6 + $filterExtraCols;
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Report for <?php echo htmlspecialchars($reportPeriodDisplay); ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="reports.php">Reports</a></li>
                <li class="breadcrumb-item active">Report for <?php echo htmlspecialchars($reportPeriodDisplay); ?></li>
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
                            <button onclick="printReport()" class="btn btn-success">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <button onclick="exportToPDF()" class="btn btn-danger">
                                <i class="bi bi-file-pdf"></i> Export PDF
                            </button>
                        </div>

                        <!-- UA Export Header (printed/exported): logo + text side-by-side, centered as a group -->
                        <div class="ua-export-header mb-4">
                            <div class="ua-export-header-inner">
                                <img class="ua-export-logo" src="assets/img/ua-logo.png"
                                    alt="University of Antique - Hamtic Campus Logo" />
                                <div class="ua-export-text">
                                    <div class="ua-line-1">Republic of the Philippines</div>
                                    <div class="ua-line-2">UNIVERSITY OF ANTIQUE–HAMTIC CAMPUS</div>
                                    <div class="ua-line-3">Guintas, Hamtic, Antique</div>
                                </div>
                            </div>
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
                            <form method="GET" action="" class="row g-2 align-items-end" id="reportFilterForm">
                                <div class="col-lg-3 col-md-4">
                                    <label for="filterStatus" class="form-label">Status</label>
                                    <select id="filterStatus" name="status" class="form-select">
                                        <option value="" <?php echo $selectedStatus === '' ? 'selected' : ''; ?>>All
                                            Statuses</option>
                                        <?php foreach ($validStatuses as $s) { ?>
                                        <option value="<?php echo htmlspecialchars($s); ?>"
                                            <?php echo $selectedStatus === $s ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($s); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-4">
                                    <label for="filterCategory" class="form-label">Category</label>
                                    <select id="filterCategory" name="category" class="form-select">
                                        <option value="" <?php echo $selectedCategory === '' ? 'selected' : ''; ?>>All
                                            Categories</option>
                                        <?php foreach ($categoryOptions as $cat) { ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>"
                                            <?php echo $selectedCategory === $cat ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-4">
                                    <label for="filterDepartment" class="form-label">Department</label>
                                    <select id="filterDepartment" name="department" class="form-select">
                                        <option value="" <?php echo $selectedDepartment === '' ? 'selected' : ''; ?>>All
                                            Departments</option>
                                        <?php foreach ($departmentOptions as $dept) { ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>"
                                            <?php echo $selectedDepartment === $dept ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label for="reportMonthFrom" class="form-label">From</label>
                                    <input type="month" id="reportMonthFrom" name="reportMonthFrom" class="form-control"
                                        value="<?php echo htmlspecialchars($reportMonthFrom); ?>" required>
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label for="reportMonthTo" class="form-label">To</label>
                                    <input type="month" id="reportMonthTo" name="reportMonthTo" class="form-control"
                                        value="<?php echo htmlspecialchars($reportMonthTo); ?>" required>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel"></i> Apply Filters
                                    </button>
                                </div>
                            </form>

                            <p class="text-muted mt-2 mb-0">
                                Showing results for:
                                <strong
                                    class="text-primary"><?php echo htmlspecialchars($reportPeriodDisplay); ?></strong>
                                <?php if ($selectedStatus !== '') { ?>
                                &bull; Status: <span><?php echo htmlspecialchars($selectedStatus); ?></span>
                                <?php } ?>
                                <?php if ($selectedCategory !== '') { ?>
                                &bull; Category: <span><?php echo htmlspecialchars($selectedCategory); ?></span>
                                <?php } ?>
                                <?php if ($selectedDepartment !== '') { ?>
                                &bull; Department: <span><?php echo htmlspecialchars($selectedDepartment); ?></span>
                                <?php } ?>
                            </p>
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
                                        <?php if ($showFilterStatusCol) { ?>
                                        <th rowspan="2">STATUS</th>
                                        <?php } ?>
                                        <?php if ($showFilterCategoryCol) { ?>
                                        <th rowspan="2">CATEGORY</th>
                                        <?php } ?>
                                        <?php if ($showFilterDepartmentCol) { ?>
                                        <th rowspan="2">DEPARTMENT</th>
                                        <?php } ?>
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

                                            $rowStatusLabel = $quantity === 0
                                                ? 'Out of Stock'
                                                : ($quantity <= $lowStockThreshold ? 'Low Stock' : 'In Stock');
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['stock_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['unit_of_measure'] ?? ''); ?></td>
                                        <td><?php echo $unitValue > 0 ? '₱' . number_format($unitValue, 2) : ''; ?></td>
                                        <?php if ($showFilterStatusCol) { ?>
                                        <td><?php echo htmlspecialchars($rowStatusLabel); ?></td>
                                        <?php } ?>
                                        <?php if ($showFilterCategoryCol) { ?>
                                        <td><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
                                        <?php } ?>
                                        <?php if ($showFilterDepartmentCol) { ?>
                                        <td><?php echo htmlspecialchars($selectedDepartment); ?></td>
                                        <?php } ?>
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
                                        <td colspan="<?php echo (int) $reportTableColCount; ?>"
                                            class="text-center py-4">
                                            No records found for <?php echo htmlspecialchars($reportPeriodDisplay); ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <?php if ($result && $result->num_rows > 0) { ?>
                                <tfoot>
                                    <tr class="table-info fw-bold">
                                        <th colspan="<?php echo (int) $tfootLabelColspan; ?>" class="text-end">TOTAL:
                                        </th>
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
                                        <h5><?php echo htmlspecialchars($reportPeriodDisplay); ?></h5>
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

    html,
    body {
        background: #fff !important;
        height: auto !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .no-print,
    .btn,
    .pagetitle,
    .pagetitle nav,
    .sidebar,
    .header,
    .footer,
    .back-to-top,
    .swal2-container,
    .swal2-backdrop-show {
        display: none !important;
    }

    #main.main,
    #main {
        position: static !important;
        left: auto !important;
        top: auto !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }

    .card-body {
        padding: 0 !important;
    }

    /* UA Export Header (logo tight to text, whole block centered) */
    .ua-export-header {
        display: block !important;
        page-break-after: avoid;
        margin-bottom: 12px !important;
        text-align: center;
    }

    .ua-export-header-inner {
        display: inline-flex !important;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        text-align: left;
    }

    .ua-export-logo {
        height: 64px !important;
        width: auto !important;
        flex-shrink: 0;
        display: block;
    }

    .ua-export-text {
        flex: 0 1 auto;
        text-align: left !important;
        line-height: 1.2;
    }

    .ua-export-text .ua-line-1,
    .ua-export-text .ua-line-3 {
        font-size: 11px !important;
    }

    .ua-export-text .ua-line-2 {
        font-size: 13px !important;
        font-weight: 700 !important;
        letter-spacing: 0.02em;
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

    #reportTable tbody td:nth-child(n+<?php echo (int) $printNumericColStart; ?>),
    #reportTable tbody th:nth-child(n+<?php echo (int) $printNumericColStart; ?>) {
        width: 7%;
        text-align: right;
    }
}

/* Screen Styles */
.no-print {
    display: block;
}

.ua-export-header {
    display: none;
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
function printReport() {
    // Chrome "Save as PDF" may use the document title as the print header.
    const originalTitle = document.title;
    document.title = '';
    try {
        window.print();
    } finally {
        document.title = originalTitle;
    }
}

// Export to PDF — same as Print; defer until SweetAlert is gone (modal open = blank PDF in some browsers)
function exportToPDF() {
    Swal.fire({
        icon: 'info',
        title: 'Export to PDF',
        html: 'Click OK to open the print dialog.<br><br>In the print dialog, choose <strong>Save as PDF</strong>.',
        confirmButtonText: 'OK',
        showCancelButton: true,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.setTimeout(function() {
                printReport();
            }, 400);
        }
    });
}
</script>