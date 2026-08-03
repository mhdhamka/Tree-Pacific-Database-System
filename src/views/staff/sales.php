<?php 

include(__DIR__ . '/../../config/dbConnect.php');

/* ==========================================================================
   1. Dynamic Sales KPI Metrics
   ========================================================================== */
$totalSalesVal       = 0;
$totalAssignedBlocks = 0;
$totalCompaniesCount = 0;
$availableBlocks     = 0;
$avgDealValue        = 0;
$totalSalesCount     = 0;

// Total Sales Revenue, Assigned Blocks, and Average Deal Value
$kpiSql = "SELECT COUNT(DISTINCT purchase.BlockID) AS AssignedBlocks, 
                  COUNT(DISTINCT purchase.SaleID) AS TotalSalesCount,
                  SUM(block.BasePrice) AS Revenue 
           FROM purchase 
           JOIN block ON purchase.BlockID = block.BlockID";
$kpiRes = mysqli_query($conn, $kpiSql);

if ($kpiRes && $kpiData = mysqli_fetch_assoc($kpiRes)) {
    $totalAssignedBlocks = $kpiData['AssignedBlocks'] ?? 0;
    $totalSalesVal       = $kpiData['Revenue'] ?? 0;
    $totalSalesCount     = $kpiData['TotalSalesCount'] ?? 0;
    $avgDealValue        = $totalSalesCount > 0 ? ($totalSalesVal / $totalSalesCount) : 0;
}

// Available (Unassigned) Blocks Count
$availSql = "SELECT COUNT(*) AS AvailCount FROM block WHERE BlockID NOT IN (SELECT BlockID FROM purchase)";
$availRes = mysqli_query($conn, $availSql);
if ($availRes && $availData = mysqli_fetch_assoc($availRes)) {
    $availableBlocks = $availData['AvailCount'] ?? 0;
}

// Registered Companies Count
$compCountSql = "SELECT COUNT(*) AS CompCount FROM company";
$compCountRes = mysqli_query($conn, $compCountSql);
if ($compCountRes && $compData = mysqli_fetch_assoc($compCountRes)) {
    $totalCompaniesCount = $compData['CompCount'] ?? 0;
}


/* ==========================================================================
   2. CHART DATA PREPARATION
   ========================================================================== */

// Orchard Revenue Breakdown
$orchardChartData = ['labels' => [], 'data' => []];
$orchSql = "SELECT b.OrchardID, SUM(b.BasePrice) AS TotalRev 
            FROM purchase p 
            JOIN block b ON p.BlockID = b.BlockID 
            GROUP BY b.OrchardID";
$orchRes = mysqli_query($conn, $orchSql);
if ($orchRes) {
    while ($row = mysqli_fetch_assoc($orchRes)) {
        $orchardChartData['labels'][] = "Orchard " . $row['OrchardID'];
        $orchardChartData['data'][]   = (float)$row['TotalRev'];
    }
}

// Fetch list of Orchards for Filter Dropdown
$orchardsList = [];
$orchListSql = "SELECT DISTINCT OrchardID FROM block ORDER BY OrchardID ASC";
$orchListRes = mysqli_query($conn, $orchListSql);
if ($orchListRes) {
    while ($oRow = mysqli_fetch_assoc($orchListRes)) {
        $orchardsList[] = $oRow['OrchardID'];
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Commercial Operations Dashboard</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <!-- CSS -->
    <link rel="stylesheet" href="../../../assets/css/staff.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

                <!-- ENHANCED KPI CARDS -->
                <div class="kpi-grid">
                    
                    <!-- Total Sales -->
                    <div class="kpi-card kpi-card-success">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">TOTAL SALES</span>
                                <h3 class="kpi-value">
                                    <span class="currency">RM</span><span class="counter" data-target="<?= number_format((float)$totalSalesVal, 2, '.', '') ?>" data-decimals="2">0.00</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Blocks -->
                    <div class="kpi-card kpi-card-primary">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">ASSIGNED BLOCKS</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?= $totalAssignedBlocks ?>" data-decimals="0">0</span> Blocks
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-cubes"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Available Blocks -->
                    <div class="kpi-card kpi-card-danger">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">AVAILABLE BLOCKS</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?= $availableBlocks ?>" data-decimals="0">0</span> Unassigned
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Avg. Deal Value -->
                    <div class="kpi-card kpi-card-purple">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">AVG. DEAL VALUE</span>
                                <h3 class="kpi-value">
                                    <span class="currency">RM</span><span class="counter" data-target="<?= number_format((float)$avgDealValue, 2, '.', '') ?>" data-decimals="2">0.00</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Companies -->
                    <div class="kpi-card kpi-card-warning">
                        <div class="kpi-glow"></div>
                        <div class="kpi-card-inner">
                            <div>
                                <span class="kpi-label">COMPANIES</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?= $totalCompaniesCount ?>" data-decimals="0">0</span> Entities
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-building"></i>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 2. VISUAL ANALYTICS SECTION -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    
                    <div class="card" style="padding: 1.25rem;">
                        <h3 class="card-title" style="margin-bottom: 1rem;"><i class="fa-solid fa-chart-line"></i> Revenue Distribution by Orchard</h3>
                        <div style="height: 240px; position: relative;">
                            <canvas id="orchardRevenueChart"></canvas>
                        </div>
                    </div>

                    <div class="card" style="padding: 1.25rem;">
                        <h3 class="card-title" style="margin-bottom: 1rem;"><i class="fa-solid fa-pie-chart"></i> Inventory Status</h3>
                        <div style="height: 240px; position: relative;">
                            <canvas id="inventoryGaugeChart"></canvas>
                        </div>
                    </div>

                </div>

                <!-- 3. ADVANCED SALES & BLOCK ASSIGNMENTS TABLE -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                        <h2 class="card-title"><i class="fa-solid fa-receipt"></i> Sales & Block Assignments</h2>
                        <a href="../../staff/processes/assignBlock.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Assign Block
                        </a>
                    </div>

                    <!-- Multi-Faceted Filters -->
                    <div class="filter-grid">
                        <div class="filter-item">
                            <label><i class="fa-solid fa-magnifying-glass"></i> Keyword Search</label>
                            <input type="text" id="salesSearchInput" placeholder="Client, Block, Sale ID..." onkeyup="filterSalesTable()">
                        </div>
                        <div class="filter-item">
                            <label><i class="fa-solid fa-tree"></i> Orchard Filter</label>
                            <select id="orchardFilter" onchange="filterSalesTable()">
                                <option value="">All Orchards</option>
                                <?php foreach ($orchardsList as $oId): ?>
                                    <option value="Orchard <?php echo $oId; ?>">Orchard <?php echo $oId; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-item" style="display: flex; align-items: flex-end;">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportSalesToCSV()" style="width: 100%;">
                                <i class="fa-solid fa-file-csv"></i> Export CSV
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="salesTable">
                            <thead>
                                <tr>
                                    <th class="th-sortable" onclick="sortTable(0)">Block ID <i class="fa-solid fa-sort"></i></th>
                                    <th class="th-sortable" onclick="sortTable(1)">Base Price <i class="fa-solid fa-sort"></i></th>
                                    <th>Orchard</th>
                                    <th class="th-sortable" onclick="sortTable(3)">Sale ID <i class="fa-solid fa-sort"></i></th>
                                    <th>Client ID</th>
                                    <th>Client Name</th>
                                    <th>Status</th>
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
                                            $rawPrice  = $sRow['BasePrice'];
                                            $baseprice = is_numeric($rawPrice) ? 'RM ' . number_format($rawPrice, 2) : htmlspecialchars($rawPrice);
                                            $orchardID = htmlspecialchars($sRow['OrchardID']);
                                            $saleID    = htmlspecialchars($sRow['SaleID']);
                                            $userID    = htmlspecialchars($sRow['UserID']);
                                            $realname  = htmlspecialchars($sRow['RealName']);

                                            echo "<tr data-orchard='Orchard {$orchardID}'>
                                                    <td><span class='badge-chip'>Block {$blockID}</span></td>
                                                    <td><span class='price-text'>{$baseprice}</span></td>
                                                    <td>Orchard {$orchardID}</td>
                                                    <td><span class='badge-chip'>#{$saleID}</span></td>
                                                    <td>{$userID}</td>
                                                    <td><strong>{$realname}</strong></td>
                                                    <td><span class='badge-paid'><i class='fa-solid fa-circle-check'></i> Paid</span></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end; gap: 0.3rem;'>
                                                            <button class='btn btn-outline-secondary btn-sm' 
                                                                    onclick=\"openSaleModal('{$saleID}', '{$blockID}', '{$realname}', '{$baseprice}', 'Orchard {$orchardID}')\" 
                                                                    title='View Summary'>
                                                                <i class='fa-solid fa-eye'></i>
                                                            </button>
                                                            <a href='../../staff/processes/generateInvoice.php?saleID={$saleID}' class='btn btn-invoice btn-sm' title='Download Receipt' target='_blank'>
                                                                <i class='fa-solid fa-file-pdf'></i>
                                                            </a>
                                                            <a href='../../staff/processes/reassignBlock.php?updateID={$userID}&saleID={$saleID}&blockID={$blockID}' class='btn btn-outline-primary btn-sm' title='Reassign'>
                                                                <i class='fa-solid fa-arrows-rotate'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr id='noDataRow'><td colspan='8' style='text-align:center;'>No sales or block assignments recorded yet.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. COMPANIES MANAGEMENT CARD -->
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
                                                            <a href='../../controllers/staff/salesController.php?action=delete&deleteID={$companyID}' 
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

    <!-- 5. SALE DETAIL MODAL -->
    <div id="saleDetailModal" class="modal-overlay">
        <div class="modal-card">
            
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 id="modalSaleID">Sale Details</h3>
                <button class="modal-close-btn" onclick="closeSaleModal()">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div><strong>Client Name:</strong> <span id="modalClientName"></span></div>
                <div><strong>Assigned Block:</strong> <span id="modalBlockID"></span></div>
                <div><strong>Orchard Location:</strong> <span id="modalOrchard"></span></div>
                <div><strong>Total Base Price:</strong> <span id="modalPrice" class="modal-price-text"></span></div>
                <div><strong>Payment Status:</strong> <span class="badge-paid">Completed & Verified</span></div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" onclick="closeSaleModal()">Close</button>
            </div>

        </div>
    </div>

    <!-- JavaScript Handlers -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/salesHandler.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            // =========================================================================
            // 1. KPI CARD REVEAL & COUNTER ANIMATIONS
            // =========================================================================
            
            function animateCounters() {
                $('.counter').each(function() {
                    var $this = $(this);

                    if ($this.hasClass('animated')) return;
                    $this.addClass('animated');

                    var rawTarget = $this.attr('data-target') || $this.data('target') || 0;
                    var cleanedTarget = String(rawTarget).replace(/[^0-9.-]/g, '');
                    var target = parseFloat(cleanedTarget) || 0;

                    var decimals = parseInt($this.attr('data-decimals'), 10);
                    if (isNaN(decimals)) {
                        decimals = parseInt($this.data('decimals'), 10);
                    }
                    if (isNaN(decimals)) {
                        decimals = 0;
                    }

                    $({ countNum: 0 }).animate({ countNum: target }, {
                        duration: 1200,
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

            function revealCards() {
                $('.kpi-card').each(function(index) {
                    var $card = $(this);

                    setTimeout(function() {
                        $card.addClass('kpi-card-visible');
                    }, index * 80);
                });
            }

            revealCards();
            animateCounters();


            // =========================================================================
            // 2. MODERNIZED & INTERACTIVE CHARTS
            // =========================================================================
            
            // --- A. Modernized Orchard Revenue Bar Chart ---
            const orchardData = <?php echo json_encode($orchardChartData); ?>;
            const ctxOrchard = document.getElementById('orchardRevenueChart').getContext('2d');

            // Create Modern Gradient Fill for Bar Chart
            const barGradient = ctxOrchard.createLinearGradient(0, 0, 0, 300);
            barGradient.addColorStop(0, 'rgba(59, 130, 246, 0.9)');  // Bright Blue Top
            barGradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');  // Soft Indigo Base

            const barGradientHover = ctxOrchard.createLinearGradient(0, 0, 0, 300);
            barGradientHover.addColorStop(0, 'rgba(37, 99, 235, 1)');
            barGradientHover.addColorStop(1, 'rgba(79, 70, 229, 0.5)');

            new Chart(ctxOrchard, {
                type: 'bar',
                data: {
                    labels: orchardData.labels.length ? orchardData.labels : ['No Data'],
                    datasets: [{
                        label: 'Revenue',
                        data: orchardData.data.length ? orchardData.data : [0],
                        backgroundColor: barGradient,
                        hoverBackgroundColor: barGradientHover,
                        borderColor: '#3b82f6',
                        hoverBorderColor: '#1d4ed8',
                        borderWidth: 1.5,
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.85)',
                            titleFont: { size: 13, weight: '600' },
                            bodyFont: { size: 14, weight: '500' },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let val = context.raw || 0;
                                    return ' Revenue: RM ' + val.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Inter', sans-serif", size: 12 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                            ticks: {
                                font: { family: "'Inter', sans-serif", size: 11 },
                                callback: function(value) {
                                    return 'RM ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // --- B. Modernized Gauge/Doughnut Chart (Inventory) ---
            const assignedVal = <?php echo (int)$totalAssignedBlocks; ?>;
            const availVal    = <?php echo (int)$availableBlocks; ?>;
            const ctxGauge    = document.getElementById('inventoryGaugeChart').getContext('2d');

            new Chart(ctxGauge, {
                type: 'doughnut',
                data: {
                    labels: ['Assigned Blocks', 'Available Blocks'],
                    datasets: [{
                        data: [assignedVal, availVal],
                        backgroundColor: ['#10b981', '#cbd5e1'],
                        hoverBackgroundColor: ['#059669', '#94a3b8'],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        borderRadius: 6,
                        hoverOffset: 12
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',       // Modern thin ring style
                    rotation: -90,       // Makes it a half-gauge arch
                    circumference: 180,  // Semi-circle gauge view
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1400
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 20,
                                font: { family: "'Inter', sans-serif", size: 12, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.85)',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let total = assignedVal + availVal;
                                    let val = context.raw || 0;
                                    let percentage = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${val} Blocks (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });


        // =========================================================================
        // 3. MULTI-FILTERING FOR SALES TABLE
        // =========================================================================
        function filterSalesTable() {
            const keyword = document.getElementById('salesSearchInput').value.toLowerCase();
            const orchardVal = document.getElementById('orchardFilter').value;
            const table = document.getElementById('salesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                if (rows[i].id === 'noDataRow') continue;

                const textContent = rows[i].textContent.toLowerCase();
                const orchardAttr = rows[i].getAttribute('data-orchard');

                const matchesKeyword = textContent.includes(keyword);
                const matchesOrchard = !orchardVal || orchardAttr === orchardVal;

                if (matchesKeyword && matchesOrchard) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }


        // =========================================================================
        // 4. COLUMN SORTING
        // =========================================================================
        let sortDirection = false;
        function sortTable(columnIndex) {
            const table = document.getElementById("salesTable");
            const tbody = table.querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));

            sortDirection = !sortDirection;

            rows.sort((a, b) => {
                const cellA = a.children[columnIndex].innerText.trim();
                const cellB = b.children[columnIndex].innerText.trim();

                return sortDirection 
                    ? cellA.localeCompare(cellB, undefined, {numeric: true}) 
                    : cellB.localeCompare(cellA, undefined, {numeric: true});
            });

            rows.forEach(row => tbody.appendChild(row));
        }


        // =========================================================================
        // 5. VIEW DETAILS MODAL
        // =========================================================================
        function openSaleModal(saleID, blockID, clientName, price, orchard) {
            document.getElementById('modalSaleID').innerText = 'Sale #' + saleID;
            document.getElementById('modalClientName').innerText = clientName;
            document.getElementById('modalBlockID').innerText = 'Block ' + blockID;
            document.getElementById('modalOrchard').innerText = orchard;
            document.getElementById('modalPrice').innerText = price;

            document.getElementById('saleDetailModal').style.display = 'flex';
        }

        function closeSaleModal() {
            document.getElementById('saleDetailModal').style.display = 'none';
        }
    </script>

</body>
</html>