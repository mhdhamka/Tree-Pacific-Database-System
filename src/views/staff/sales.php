<?php 

include(__DIR__ . '/../../config/dbConnect.php');

// Fetch KPI Metrics for Commercial Dashboard
$totalSalesVal = 0;
$totalAssignedBlocks = 0;
$totalCompaniesCount = 0;

// Calculate Total Revenue and Assigned Blocks
$kpiSql = "SELECT COUNT(DISTINCT purchase.BlockID) AS AssignedBlocks, SUM(block.BasePrice) AS Revenue 
           FROM purchase 
           JOIN block ON purchase.BlockID = block.BlockID";
$kpiRes = mysqli_query($conn, $kpiSql);
if ($kpiRes && $kpiData = mysqli_fetch_assoc($kpiRes)) {
    $totalAssignedBlocks = $kpiData['AssignedBlocks'] ?? 0;
    $totalSalesVal = $kpiData['Revenue'] ?? 0;
}

// Calculate Total Companies
$compCountSql = "SELECT COUNT(*) AS CompCount FROM company";
$compCountRes = mysqli_query($conn, $compCountSql);
if ($compCountRes && $compData = mysqli_fetch_assoc($compCountRes)) {
    $totalCompaniesCount = $compData['CompCount'] ?? 0;
}

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Sales & Clients</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

        <!-- Main Dashboard View -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Client & Sales Commercial Operations</h1>
            </header>

            <div class="content-body">

                <!-- 1. Commercial KPI Cards -->
                <div class="kpi-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                        <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 10px; font-size: 1.5rem;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <small style="color: #666; font-weight: 500;">Total Commercial Sales</small>
                            <h3 style="margin: 0; font-size: 1.4rem;">RM <?php echo number_format($totalSalesVal, 2); ?></h3>
                        </div>
                    </div>

                    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                        <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 1rem; border-radius: 10px; font-size: 1.5rem;">
                            <i class="fa-solid fa-cubes"></i>
                        </div>
                        <div>
                            <small style="color: #666; font-weight: 500;">Assigned Blocks</small>
                            <h3 style="margin: 0; font-size: 1.4rem;"><?php echo $totalAssignedBlocks; ?> Blocks</h3>
                        </div>
                    </div>

                    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                        <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 1rem; border-radius: 10px; font-size: 1.5rem;">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <div>
                            <small style="color: #666; font-weight: 500;">Registered Companies</small>
                            <h3 style="margin: 0; font-size: 1.4rem;"><?php echo $totalCompaniesCount; ?> Entities</h3>
                        </div>
                    </div>
                </div>

                <!-- 2. Sales & Block Assignments Card -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                        <h2 class="card-title"><i class="fa-solid fa-chart-line"></i> Sales & Block Assignments</h2>
                        <a href="../../staff/processes/assignBlock.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Assign Block
                        </a>
                    </div>

                    <!-- Search Filter and Export Bar -->
                    <div class="table-controls">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="salesSearchInput" placeholder="Search sales, clients, blocks..." onkeyup="filterSalesTable()">
                        </div>
                        <div class="control-buttons">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportSalesToCSV()">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="salesTable">
                            <thead>
                                <tr>
                                    <th>Block ID</th>
                                    <th>Base Price</th>
                                    <th>Orchard ID</th>
                                    <th>Sale ID</th>
                                    <th>Client ID</th>
                                    <th>Client Name</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $salesSql = "SELECT 
                                                    b.BlockID, b.BasePrice, b.OrchardID, 
                                                    s.SaleID, 
                                                    u.UserID, u.RealName 
                                                 FROM block b
                                                 INNER JOIN purchase p ON b.BlockID = p.BlockID
                                                 INNER JOIN sale s ON p.SaleID = s.SaleID
                                                 INNER JOIN client c ON s.ClientID = c.UserID
                                                 INNER JOIN user u ON c.UserID = u.UserID
                                                 ORDER BY s.SaleID DESC";
                                    
                                    $salesResult = mysqli_query($conn, $salesSql);

                                    if ($salesResult && mysqli_num_rows($salesResult) > 0) {
                                        while ($sRow = mysqli_fetch_assoc($salesResult)) {
                                            $blockID   = htmlspecialchars($sRow['BlockID']);
                                            $baseprice = is_numeric($sRow['BasePrice']) ? 'RM ' . number_format($sRow['BasePrice'], 2) : htmlspecialchars($sRow['BasePrice']);
                                            $orchardID = htmlspecialchars($sRow['OrchardID']);
                                            $saleID    = htmlspecialchars($sRow['SaleID']);
                                            $userID    = htmlspecialchars($sRow['UserID']);
                                            $realname  = htmlspecialchars($sRow['RealName']);

                                            echo "<tr>
                                                    <td><span class='badge-chip'>Block {$blockID}</span></td>
                                                    <td><span class='price-text'>{$baseprice}</span></td>
                                                    <td>{$orchardID}</td>
                                                    <td><span class='badge-chip'>#{$saleID}</span></td>
                                                    <td>{$userID}</td>
                                                    <td><strong>{$realname}</strong></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='../../staff/processes/reassignBlock.php?updateID={$userID}&saleID={$saleID}&blockID={$blockID}' class='btn btn-outline-primary btn-sm'>
                                                                <i class='fa-solid fa-arrows-rotate'></i> Reassign
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr id='noDataRow'><td colspan='7' style='text-align:center;'>No sales or block assignments recorded yet.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Companies Management Card -->
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 class="card-title"><i class="fa-solid fa-building"></i> Client Companies</h2>
                        <a href="../../staff/processes/addCompany.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Company
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>Assigned Orchard ID</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $compSql = "SELECT * FROM company ORDER BY CompanyID ASC";
                                    $compResult = mysqli_query($conn, $compSql);

                                    if ($compResult && mysqli_num_rows($compResult) > 0) {
                                        while ($cRow = mysqli_fetch_assoc($compResult)) {
                                            $companyID   = htmlspecialchars($cRow['CompanyID']);
                                            $companyName = htmlspecialchars($cRow['CompanyName']);
                                            $orchardID   = htmlspecialchars($cRow['OrchardID']);

                                            echo "<tr>
                                                    <td><span class='badge-chip'>#{$companyID}</span></td>
                                                    <td><span class='company-name'><strong>{$companyName}</strong></span></td>
                                                    <td><span class='badge-chip'>Orchard {$orchardID}</span></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='../../staff/processes/updateCompany.php?updateID={$companyID}' class='btn btn-outline-primary btn-sm'>
                                                                <i class='fa-solid fa-pen-to-square'></i> Update
                                                            </a>
                                                            <a href='../../controllers/staff/companyController.php?action=delete&deleteID={$companyID}' 
                                                               class='btn btn-outline-danger btn-sm' 
                                                               onclick=\"return confirm('Are you sure you want to delete this company?');\">
                                                                <i class='fa-solid fa-trash'></i> Delete
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' style='text-align:center;'>No companies found.</td></tr>";
                                    }

                                    if (isset($conn)) {
                                        mysqli_close($conn);
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '/../../includes/footer.php'); ?>

        </main>
    </div>

    <!-- JavaScript for Live Filtering and CSV Export -->
    <script src="../../../assets/js/salesHandler.js"></script>
</body>
</html>