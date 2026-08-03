<?php
// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Dynamic root path resolver (Works regardless of subdirectory depth)
$base_path = '';
if (preg_match('#^(/[^/]+)#', $_SERVER['SCRIPT_NAME'], $matches)) {
    $base_path = ($matches[1] !== '/src' && $matches[1] !== '/assets') ? $matches[1] : '';
}

// 2. Fetch session details (with safe fallbacks)
$activeUser = $_SESSION['RealName'] ?? $_SESSION['username'] ?? 'Client User';

// 3. Get current script name for active link highlighting
$current_page = $_SERVER['SCRIPT_NAME'];
?>

<!-- Dynamic Navigation Bar -->
<nav class="navbar">
    <ul>
        <li>
            <a class="<?php echo (strpos($current_page, 'dashboard.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/client/dashboard.php">
                <i class="fa fa-home"></i> Overview
            </a>
        </li>
        <li>
            <a class="<?php echo (strpos($current_page, 'ProfileClient.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/client/ProfileClient.php">
                <i class="fa fa-user"></i> Profile
            </a>
        </li>
        <li>
            <a class="<?php echo (strpos($current_page, 'PurchaseClient.php') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_path; ?>/src/views/client/PurchaseClient.php">
                <i class="fa fa-tree"></i> Purchases
            </a>
        </li>
        <li class="logout-item">
            <a href="<?php echo $base_path; ?>/src/views/public/logout.php">
                <i class="fa fa-sign-out"></i> Log Out
            </a>
        </li>
    </ul>
</nav>