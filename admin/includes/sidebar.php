<?php 
    $currentPage = basename($_SERVER['PHP_SELF'], ".php");
    function setActive($page) {
        global $currentPage;
        return $currentPage === $page ? 'class="nav-link active"' : 'class="nav-link collapsed"';
    }
?>


<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <div class="logo-text d-flex align-items-center justify-content-center mb-4">
        <a href="dashboard.php" class="text-center">
            <img src="assets/img/apple-touch-icon.png" height="90px" alt="">
            <h5 class="fw-bold text-dark mt-2">Supply Office Inventory</h5>
            <span class="undertext text-dark">University of Antique Hamtic Campus</span>
        </a>

    </div>

    <!-- Sidebar Navigation -->
    <ul class="sidebar-nav" id="sidebar-nav">


        <li class="nav-item">
            <a <?= setActive('verification_dashboard') ?> href="verification_dashboard.php">
                <i class="bi bi-shield-check"></i>
                <span>Verification Dashboard</span>
            </a>
        </li>

        <!-- <li class="nav-item">
            <a <?= setActive('dashboard') ?> href="dashboard.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li> -->

        <li class="nav-item">
            <a <?= setActive('inventory') ?> href="inventory.php">
                <i class="bi bi-box-seam"></i>
                <span>Inventory</span>
            </a>
        </li>

        <li class="nav-item">
            <a <?= setActive('reports') ?> href="reports.php">
                <i class="bi bi-bar-chart-line"></i>
                <span>Reports</span>
            </a>
        </li>


        <li class="nav-item">
            <a <?= setActive('user-manangement') ?> href="user-manangement.php">
                <i class="bi bi-people"></i>
                <span>User Management</span>
            </a>
        </li>

    </ul>


</aside><!-- End Sidebar-->