<?php
// Dynamic root path resolver
$base_path = '';
if (preg_match('#^(/[^/]+)#', $_SERVER['SCRIPT_NAME'], $matches)) {
    $base_path = ($matches[1] !== '/src' && $matches[1] !== '/assets') ? $matches[1] : '';
}

// Fetch session variables initialized during login
$activeUser = $_SESSION['username'] ?? 'Staff User';
$userRole   = $_SESSION['role'] ?? 'Administrator';
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo $base_path; ?>/assets/images/TREE.PNG" alt="PacificTree Logo">
        <span>PacificTree</span>
    </div>

    <!-- Profile Info Banner (Cleaned - No Inline DB Query) -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <i class="fa fa-user"></i>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">
                <?php echo htmlspecialchars($activeUser); ?>
            </div>
            <div class="sidebar-user-role">
                <?php echo htmlspecialchars($userRole); ?>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a class="sidebar-link active" href="<?php echo $base_path; ?>/src/views/staff/dashboard.php">
                <i class="fa fa-tachometer"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/views/staff/inventory.php">
                <i class="fa fa-leaf"></i>
                <span>Tree Block Inventory</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/views/staff/sales.php">
                <i class="fa fa-line-chart"></i>
                <span>Sales & Clients</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/views/staff/reports/index.php">
                <i class="fa fa-file-text"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link logout-link" href="<?php echo $base_path; ?>/src/views/staff/logout.php">
                <i class="fa fa-sign-out"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</aside>