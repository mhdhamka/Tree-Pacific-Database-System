<?php 

include(__DIR__ . '/../../config/dbConnect.php');

/* ==========================================================================
   Dynamic KPI Metric Queries (Aligned with reports.php structure)
   ========================================================================== */

// 1. Total Revenue (Calculated from block JOIN purchase on BasePrice)
$revenueQuery = "SELECT SUM(BasePrice) AS TotalRev 
                 FROM block 
                 INNER JOIN purchase ON block.BlockID = purchase.BlockID";
$revenueRes   = mysqli_query($conn, $revenueQuery);
$revenueData  = $revenueRes ? mysqli_fetch_assoc($revenueRes) : [];

$totalSales = $revenueData['TotalRev'] ?? 0;

// 2. Accounts Breakdown (Total Users, Clients, Staff)
$userRes   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user");
$clientRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM client");
$staffRes  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM staff");

$totalUsers   = ($userRes && $row = mysqli_fetch_assoc($userRes)) ? $row['total'] : 0;
$totalClients = ($clientRes && $row = mysqli_fetch_assoc($clientRes)) ? $row['total'] : 0;
$totalStaff   = ($staffRes && $row = mysqli_fetch_assoc($staffRes)) ? $row['total'] : 0;
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Dashboard & User Management</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <link rel="stylesheet" href="../../../assets/css/staff.css">

    <style>
        /* Dashboard Modern Additions */
        .dashboard-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .quick-action-btns {
            display: flex;
            gap: 0.5rem;
        }

        .tab-container {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1rem;
        }

        .tab-btn {
            padding: 0.6rem 1.2rem;
            border: none;
            background: none;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
        }

        .tab-btn.active {
            color: #10b981;
            border-bottom-color: #10b981;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

        <!-- Main Dashboard View -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Users & System Administration</h1>
            </header>

            <div class="content-body">

                <!-- Page Header with Quick Actions -->
                <div class="dashboard-header-actions">
                    <div>
                        <h2 style="margin: 0; font-size: 1.5rem;">System Overview</h2>
                        <small style="color: #64748b;">Real-time system stats, client directory, and staff management</small>
                    </div>
                    <div class="quick-action-btns">
                        <a href="../../staff/processes/addUser.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-user-plus"></i> Add User
                        </a>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openModal('addClientModal')">
                            <i class="fa-solid fa-address-book"></i> Add Client Details
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openModal('addStaffModal')">
                            <i class="fa-solid fa-id-badge"></i> Add Staff Details
                        </button>
                    </div>
                </div>

                <!-- STATS OVERVIEW ROW -->
                <div class="kpi-grid">
                    
                    <!-- Total Accounts -->
                    <div class="kpi-card kpi-card-primary">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">TOTAL ACCOUNTS</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?= $totalUsers ?>" data-decimals="0">0</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Clients -->
                    <div class="kpi-card kpi-card-success">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">CLIENTS</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?= $totalClients ?>" data-decimals="0">0</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Staff Members -->
                    <div class="kpi-card kpi-card-warning">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">STAFF MEMBERS</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?= $totalStaff ?>" data-decimals="0">0</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-id-badge"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="kpi-card kpi-card-success">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">TOTAL REVENUE</span>
                                <h3 class="kpi-value">
                                    <span class="currency">RM</span><span class="counter" data-target="<?= number_format((float)$totalSales, 2, '.', '') ?>" data-decimals="2">0.00</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tabbed User Directory -->
                <div class="card">
                    <div class="tab-container">
                        <button class="tab-btn active" onclick="switchTab('all-users', this)">
                            <i class="fa-solid fa-users"></i> All System Users
                        </button>
                        <button class="tab-btn" onclick="switchTab('clients-tab', this)">
                            <i class="fa-solid fa-user-tie"></i> Client Profiles
                        </button>
                        <button class="tab-btn" onclick="switchTab('staff-tab', this)">
                            <i class="fa-solid fa-id-badge"></i> Staff Directory
                        </button>
                    </div>

                    <!-- TAB 1: Unified All Users -->
                    <div id="all-users" class="tab-content active">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Username / Email</th>
                                        <th>Real Name & Contact</th>
                                        <th>Role / Type</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT UserID, Username, Email, RealName, Phone, Address, UserType FROM user ORDER BY UserID ASC";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                            $uID     = htmlspecialchars($row['UserID']);
                                            $uName   = htmlspecialchars($row['Username']);
                                            $email   = htmlspecialchars($row['Email']);
                                            $rName   = htmlspecialchars($row['RealName']);
                                            $phone   = htmlspecialchars($row['Phone'] ?? 'N/A');
                                            $address = htmlspecialchars($row['Address'] ?? 'N/A');
                                            $type    = htmlspecialchars($row['UserType']);
                                    ?>
                                            <tr>
                                                <td>#<?= $uID ?></td>
                                                <td>
                                                    <strong><?= $uName ?></strong><br>
                                                    <small style="color: #64748b;"><?= $email ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= $rName ?></strong><br>
                                                    <small style="color: #64748b;"><i class="fa fa-phone"></i> <?= $phone ?></small><br>
                                                    <small style="color: #64748b; display: inline-block; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= $address ?>">
                                                        <i class="fa fa-map-marker"></i> <?= $address ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge-usertype <?= strtolower($type) == 'staff' || strtolower($type) == 's' ? 'badge-staff' : 'badge-client' ?>">
                                                        <?= $type ?>
                                                    </span>
                                                </td>
                                                <td style="text-align: right;">
                                                    <div class="actions-cell" style="justify-content: flex-end;">
                                                        <a href="../../staff/processes/updateUser.php?updateID=<?= $uID ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fa-solid fa-pen"></i> Edit
                                                        </a>
                                                        <a href="../../controllers/staff/userController.php?action=delete&deleteID=<?= $uID ?>" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                                            <i class="fa-solid fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php 
                                        endwhile;
                                    else: 
                                    ?>
                                        <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: Clients Only -->
                    <div id="clients-tab" class="tab-content">
                        <div class="table-responsive">
                            <table class="clients-table">
                                <thead>
                                    <tr>
                                        <th>Client ID</th>
                                        <th>Address</th>
                                        <th>Country</th>
                                        <th>Map View</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM client";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                            $cID     = htmlspecialchars($row['UserID']);
                                            $address = htmlspecialchars($row['Address']);
                                            $country = htmlspecialchars($row['Country']);
                                            
                                            // Full query string for search mapping
                                            $fullAddress = urlencode($address . ', ' . $country);
                                    ?>
                                            <tr>
                                                <td>#<?= $cID ?></td>
                                                <td>
                                                    <div class="address-wrapper">
                                                        <span class="address-pin-badge">
                                                            <i class="fa-solid fa-location-dot"></i>
                                                        </span>
                                                        <span class="address-text"><?= $address ?></span>
                                                    </div>
                                                </td>
                                                <td><?= $country ?></td>
                                                <td>
                                                    <!-- Leaflet Modal Trigger Button -->
                                                    <button type="button" 
                                                            class="btn-leaflet-modal" 
                                                            onclick="openClientMapModal('<?= $cID ?>', '<?= addslashes($address) ?>', '<?= addslashes($country) ?>')">
                                                        <i class="fa-solid fa-map-location-dot"></i>
                                                        <span>Edit Map</span>
                                                    </button>

                                                    <!-- Direct Google Maps Link (Swapped Icon) -->
                                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $fullAddress ?>" 
                                                    target="_blank" 
                                                    class="btn-gmaps-badge" 
                                                    title="Open in Google Maps">
                                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                    </a>
                                                </td>
                                                <td class="text-right">
                                                    <div class="actions-cell">
                                                        <a href="/tree/src/controllers/staff/userController.php?action=deleteClient&deleteID=<?= $cID ?>" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this client record?');">
                                                            <i class="fa-solid fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php 
                                        endwhile;
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="5" class="empty-table-msg">No client profiles found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: Staff Only -->
                    <div id="staff-tab" class="tab-content">
                        <div class="table-responsive">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Role</th>
                                        <th>Employment Date</th>
                                        <th>Salary</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM staff";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                            $sID       = htmlspecialchars($row['UserID']);
                                            $eDate     = htmlspecialchars($row['EmployDate']);
                                            $salary    = htmlspecialchars($row['Salary']);
                                            $isManager = (bool)$row['IsManager']; // Assumes boolean or 1/0 in database
                                    ?>
                                            <tr>
                                                <td>#<?= $sID ?></td>
                                                <td>
                                                    <!-- Interactive Toggle Switch -->
                                                    <label class="role-toggle" title="Click to toggle Manager status">
                                                        <input type="checkbox" 
                                                            onchange="toggleManagerStatus(this, '<?= $sID ?>')" 
                                                            <?= $isManager ? 'checked' : '' ?>>
                                                        <span class="role-badge <?= $isManager ? 'badge-manager' : 'badge-staff' ?>">
                                                            <i class="fa-solid <?= $isManager ? 'fa-user-tie' : 'fa-user' ?>"></i>
                                                            <span class="role-text"><?= $isManager ? 'Manager' : 'Staff' ?></span>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td class="editable-cell relative-cell" 
                                                data-staff-id="<?= $sID ?>" 
                                                data-field="EmployDate" 
                                                title="Click to edit date">
                                                <div class="cell-wrapper">
                                                    <span class="cell-value">
                                                        <i class="fa-solid fa-calendar-days text-muted me-1"></i><?= $eDate ?>
                                                    </span>
                                                    <input type="date" class="cell-input" value="<?= $eDate ?>" style="display: none;">
                                                    
                                                    <!-- Tenure Popover -->
                                                    <div class="cell-popover">
                                                        <small class="text-muted d-block">Tenure:</small>
                                                        <strong class="popover-tenure" data-date="<?= $eDate ?>">Calculating...</strong>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="editable-cell relative-cell" 
                                                data-staff-id="<?= $sID ?>" 
                                                data-field="Salary" 
                                                title="Click to edit salary">
                                                <div class="cell-wrapper">
                                                    <span class="cell-value salary-text">RM <?= number_format((float)$salary, 2) ?></span>
                                                    <input type="number" step="0.01" class="cell-input" value="<?= $salary ?>" style="display: none;">
                                                    
                                                    <!-- Salary Popover -->
                                                    <div class="cell-popover">
                                                        <small class="text-muted d-block">Annual Total:</small>
                                                        <strong class="popover-annual">RM <?= number_format((float)$salary * 12, 2) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                                <td class="text-right">
                                                    <div class="actions-cell">
                                                        <a href="../../controllers/staff/userController.php?action=deleteStaff&deleteID=<?= $sID ?>" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this staff record?');">
                                                            <i class="fa-solid fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php 
                                        endwhile;
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="5" class="empty-table-msg">No staff members found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '/../../includes/footer.php'); ?>

        </main>
    </div>

    <!-- View Client Location Modal -->
    <div id="viewMapModal" class="user-modal">
        <div class="modal-content" style="max-width: 650px;">
            <div class="modal-header">
                <h2><i class="fa-solid fa-map-location-dot"></i> Client Location & Address Manager</h2>
                <span class="close-btn" onclick="closeModal('viewMapModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p id="viewMapAddressText" style="font-weight: 600; margin-bottom: 10px; color: #333;"></p>
                
                <!-- Leaflet Map Container -->
                <div id="viewClientMap" style="height: 350px; width: 100%; border-radius: 8px; border: 1px solid #ccc;"></div>
            </div>
        </div>
    </div>

   <!-- Add Client Modal -->
    <div id="addClientModal" class="user-modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2><i class="fa fa-briefcase"></i> Register New Client Details</h2>
                <span class="close-btn" onclick="closeModal('addClientModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="/tree/src/controllers/staff/userController.php?action=addClient">
                    
                    <div class="form-group">
                        <label for="clientUserID">Select User</label>
                        <div class="input-wrapper">
                            <i class="fa fa-id-badge"></i>
                            <select name="userID" id="clientUserID" class="form-control-input" required>
                                <option value="">Select Existing User</option>
                                <?php
                                $userQuery = "SELECT u.UserID, u.RealName, u.Username 
                                            FROM user u 
                                            LEFT JOIN client c ON u.UserID = c.UserID 
                                            WHERE c.UserID IS NULL";
                                
                                $userResult = mysqli_query($conn, $userQuery);
                                
                                if ($userResult && mysqli_num_rows($userResult) > 0):
                                    while ($u = mysqli_fetch_assoc($userResult)):
                                        $displayName = !empty($u['RealName']) ? $u['RealName'] : $u['Username'];
                                ?>
                                        <option value="<?= $u['UserID'] ?>">
                                            [ID: <?= $u['UserID'] ?>] <?= htmlspecialchars($displayName) ?>
                                        </option>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <option value="" disabled>No available users found</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Leaflet Location Picker Section -->
                    <div class="form-group">
                        <label><i class="fa fa-map-marker"></i> Pick Location on Map</label>
                        <div id="clientMap" style="height: 220px; width: 100%; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 10px;"></div>
                        <small style="color: #666;">Click anywhere on the map or drag the pin to set the address.</small>
                    </div>

                    <div class="form-group">
                        <label for="clientAddress">Address</label>
                        <div class="input-wrapper">
                            <i class="fa fa-map-pin"></i>
                            <input type="text" name="address" id="clientAddress" class="form-control-input" placeholder="Address will auto-fill from map" required readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clientCountry">Country</label>
                        <div class="input-wrapper">
                            <i class="fa fa-globe"></i>
                            <input type="text" name="country" id="clientCountry" class="form-control-input" placeholder="Country will auto-fill from map" required readonly>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="addClient" class="btn btn-primary">
                            <i class="fa fa-check"></i> Submit Client Profile
                        </button>
                        <button type="button" class="btn btn-light" onclick="closeModal('addClientModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div id="addStaffModal" class="user-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa fa-id-badge"></i> Register New Staff Details</h2>
                <span class="close-btn" onclick="closeModal('addStaffModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="../../controllers/staff/userController.php?action=addStaff">
                    <div class="form-group">
                        <label for="staffUserID">Select User</label>
                        <div class="input-wrapper">
                            <i class="fa fa-id-badge"></i>
                            <select name="userID" id="staffUserID" class="form-control-input" required>
                                <option value="">Select Existing User</option>
                                <?php
                                // Fetch users who are NOT yet registered in the staff table
                                $userQuery = "SELECT u.UserID, u.RealName, u.Username 
                                            FROM user u 
                                            LEFT JOIN staff s ON u.UserID = s.UserID 
                                            WHERE s.UserID IS NULL";
                                $userResult = mysqli_query($conn, $userQuery);
                                while ($u = mysqli_fetch_assoc($userResult)):
                                ?>
                                    <option value="<?= $u['UserID'] ?>">
                                        [ID: <?= $u['UserID'] ?>] <?= htmlspecialchars($u['RealName'] ?: $u['Username']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="employdate">Employment Date</label>
                        <div class="input-wrapper">
                            <i class="fa fa-calendar"></i>
                            <input type="date" name="employdate" id="employdate" class="form-control-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="salary">Salary ($)</label>
                        <div class="input-wrapper">
                            <i class="fa fa-dollar"></i>
                            <input type="text" name="salary" id="salary" class="form-control-input" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Manager Status</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="IsManager" value="1" required>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="IsManager" value="0" required>
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="addStaff" class="btn btn-primary">
                            <i class="fa fa-check"></i> Submit Staff Profile
                        </button>
                        <button type="button" class="btn btn-light" onclick="closeModal('addStaffModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
    $(document).ready(function() {

        // =========================================================================
        // Dynamic Count-Up Animation for KPI Values
        // =========================================================================
        function animateCounters() {
            $('.counter').each(function() {
                var $this = $(this);

                // Prevent double-animating if already processed
                if ($this.hasClass('animated')) return;
                $this.addClass('animated');

                // Read target from HTML data-target attribute
                var rawTarget = $this.attr('data-target') || $this.data('target') || 0;
                var cleanedTarget = String(rawTarget).replace(/[^0-9.-]/g, '');
                var target = parseFloat(cleanedTarget) || 0;

                // Read exact decimal precision (e.g., 0 for integers, 2 for Total Revenue RM)
                var decimals = parseInt($this.attr('data-decimals'), 10);
                if (isNaN(decimals)) {
                    decimals = parseInt($this.data('decimals'), 10);
                }
                if (isNaN(decimals)) {
                    decimals = 0; // Default to integer if unspecified
                }

                // Animate from 0 to target value
                $({ countNum: 0 }).animate({ countNum: target }, {
                    duration: 1000,
                    easing: 'swing',
                    step: function() {
                        $this.text(this.countNum.toLocaleString('en-US', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals
                        }));
                    },
                    complete: function() {
                        $this.text(target.toLocaleString('en-US', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals
                        }));
                    }
                });
            });
        }

        // =========================================================================
        // Smooth Staggered Reveal Effect for Cards
        // =========================================================================
        function revealCards() {
            $('.kpi-card').each(function(index) {
                var $card = $(this);

                // Stagger addition of visibility class across the 4 cards
                setTimeout(function() {
                    $card.addClass('kpi-card-visible');
                }, index * 80);
            });
        }

        // Execute card entry and number counting animations on load
        revealCards();
        animateCounters();

    });
    </script>

    <!-- Tab Switcher Script -->
    <script src="../../../assets/js/dashboard.js"></script>
    <script src="../../../assets/js/userModal.js"></script>
    <script src="../../../assets/js/staff.js"></script>

</body>
</html>