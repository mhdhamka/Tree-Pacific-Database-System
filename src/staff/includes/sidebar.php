<?php
// Finds the path to the root 'tree' directory dynamically
$app_root = '/';
if (preg_match('#^(/[^/]+)#', $_SERVER['SCRIPT_NAME'], $matches)) {
    // If running in a subfolder like /tree, $base_path becomes "/tree"
    $base_path = ($matches[1] !== '/src' && $matches[1] !== '/assets') ? $matches[1] : '';
} else {
    $base_path = '';
}
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo $base_path; ?>/assets/images/TREE.png" alt="PacificTree Logo">
        <span>PacificTree</span>
    </div>

    <!-- Profile Info Banner -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <i class="fa fa-user"></i>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">
                <?php
                    global $conn;
                    $sql = "SELECT Username FROM user WHERE logStatus = 1 AND UserType = 'S' LIMIT 1;";
                    $result = mysqli_query($conn, $sql);
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo htmlspecialchars($row["Username"]);
                    } else {
                        echo "Staff User";
                    }
                ?>
            </div>
            <div class="sidebar-user-role">Administrator</div>
        </div>
    </div>

    <!-- Navigation Links -->
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a class="sidebar-link active" href="<?php echo $base_path; ?>/src/staff/dashboard.php">
                <i class="fa fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/staff/companies.php">
                <i class="fa fa-building"></i>
                <span>Companies</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/staff/trees.php">
                <i class="fa fa-leaf"></i>
                <span>Trees</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/staff/blocks.php">
                <i class="fa fa-cubes"></i>
                <span>Blocks</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/staff/orchards.php">
                <i class="fa fa-map-marker"></i>
                <span>Orchards</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/staff/sales.php">
                <i class="fa fa-line-chart"></i>
                <span>Sales</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $base_path; ?>/src/staff/reports.php">
                <i class="fa fa-file-text"></i>
                <span>Report</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link logout-link" href="<?php echo $base_path; ?>/src/staff/logoutStaff.php">
                <i class="fa fa-sign-out"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</aside>