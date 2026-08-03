<?php
// Dynamic root path resolver
$base_path = '';
if (preg_match('#^(/[^/]+)#', $_SERVER['SCRIPT_NAME'], $matches)) {
    $base_path = ($matches[1] !== '/src' && $matches[1] !== '/assets') ? $matches[1] : '';
}

// Fetch session variables initialized during login
$activeUser = $_SESSION['real_name'] ?? $_SESSION['username'] ?? 'Staff User';
$username   = $_SESSION['username'] ?? '';
$userRole   = $_SESSION['role'] ?? 'Administrator';

// Get current script path for active link checking
$current_page = $_SERVER['SCRIPT_NAME'];
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo $base_path; ?>/assets/images/TREE.PNG" alt="PacificTree Logo">
        <span>PacificTree</span>
    </div>

    <!-- Profile Info Banner -->
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
            <?php if (!empty($username)): ?>
                <div class="sidebar-user-handle" style="font-size: 0.75rem; opacity: 0.75; margin-top: 2px;">
                    @<?php echo htmlspecialchars($username); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navigation Links -->
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a class="sidebar-link <?php echo (strpos($current_page, 'dashboard.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/staff/dashboard.php">
                <i class="fa fa-tachometer"></i>
                <span>User Management</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a class="sidebar-link <?php echo (strpos($current_page, 'inventory.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/staff/inventory.php">
                <i class="fa fa-leaf"></i>
                <span>Tree Block Inventory</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link <?php echo (strpos($current_page, 'sales.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/staff/sales.php">
                <i class="fa fa-line-chart"></i>
                <span>Sales & Clients</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link <?php echo (strpos($current_page, 'reports.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/staff/reports/reports.php">
                <i class="fa fa-file-text"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link logout-link" href="<?php echo $base_path; ?>/src/views/public/logout.php">
                <i class="fa fa-sign-out"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</aside>