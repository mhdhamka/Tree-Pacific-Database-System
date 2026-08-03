<?php
// 1. Start session and include database configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../../config/dbConnect.php');

// Define BASE_URL fallback if not defined in dbConnect.php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/tree');
}

// 2. Client Authentication Guard
if (!isset($_SESSION['UserID']) && !isset($_SESSION['user_id'])) {
    $UserID     = 1;
    $clientName = "Client";
} else {
    $UserID     = $_SESSION['UserID'] ?? $_SESSION['user_id'];
    $clientName = $_SESSION['RealName'] ?? $_SESSION['username'] ?? 'Valued Client';
}

// 3. Data Structures for Purchase Management
$purchases = [];
$totalSpent = 0;
$totalBlocksPurchased = 0;
$totalTreesOwned = 0;

// 4. Fetch Detailed Purchase History with Blocks & Tree Breakdown
if (isset($conn) && $conn) {
    // Fetch Client Real Name
    $userQuery = "SELECT RealName FROM user WHERE UserID = ?";
    if ($stmt = $conn->prepare($userQuery)) {
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $clientName = $row['RealName'];
        }
        $stmt->close();
    }

    // Main Query: Retrieve Sales, Purchases, Blocks, and Associated Trees
    $purchaseQuery = "SELECT 
                        s.SaleID,
                        s.DateSold,
                        s.TotalPrice,
                        p.SaleID,
                        p.BlockID,
                        p.SellingPrice AS BlockPrice,
                        t.TreeID,
                        t.SpeciesName,
                        t.Lattitude,
                        t.Longitude,
                        t.timber_grade,
                        COALESCE(tu.TreeHeight, t.TreeHeight) AS TreeHeight,
                        COALESCE(tu.TreeDiameter, t.TreeDiameter) AS TreeDiameter,
                        COALESCE(tu.TreeStatus, 1) AS TreeStatus,
                        tu.TreeImage
                      FROM client c
                      INNER JOIN sale s ON c.UserID = s.ClientID
                      INNER JOIN purchase p ON s.SaleID = p.SaleID
                      LEFT JOIN tree t ON p.BlockID = t.BlockID
                      LEFT JOIN (
                          SELECT tu1.* 
                          FROM treeupdate tu1
                          INNER JOIN (
                              SELECT TreeID, MAX(UpdateDate) AS MaxDate 
                              FROM treeupdate 
                              GROUP BY TreeID
                          ) tu2 ON tu1.TreeID = tu2.TreeID AND tu1.UpdateDate = tu2.MaxDate
                      ) tu ON t.TreeID = tu.TreeID
                      WHERE c.UserID = ?
                      ORDER BY s.DateSold DESC, p.BlockID ASC";

    if ($stmt = $conn->prepare($purchaseQuery)) {
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $res = $stmt->get_result();

        $rawPurchases = [];
        while ($row = $res->fetch_assoc()) {
            // Convert BLOB to Base64 image URL
            if (!empty($row['TreeImage'])) {
                $row['TreeImageDataUri'] = 'data:image/jpeg;base64,' . base64_encode($row['TreeImage']);
            } else {
                $row['TreeImageDataUri'] = BASE_URL . '/assets/images/TREE.PNG';
            }
            unset($row['TreeImage']); // Strip raw binary

            $saleID  = $row['SaleID'];
            $blockID = $row['BlockID'];

            if (!isset($rawPurchases[$saleID])) {
                $rawPurchases[$saleID] = [
                    'SaleID'        => $saleID,
                    'DateSold'      => $row['DateSold'] ?? 'N/A',
                    'TotalPrice'    => $row['TotalPrice'] ?? 0,
                    'PaymentStatus' => 'Completed', // Default fallback
                    'Blocks'        => []
                ];
                $totalSpent += floatval($row['TotalPrice'] ?? 0);
            }

            if (!isset($rawPurchases[$saleID]['Blocks'][$blockID])) {
                $rawPurchases[$saleID]['Blocks'][$blockID] = [
                    'BlockID'    => $blockID,
                    'BlockPrice' => $row['BlockPrice'] ?? 0,
                    'Trees'      => []
                ];
                $totalBlocksPurchased++;
            }

            if (!empty($row['TreeID'])) {
                $rawPurchases[$saleID]['Blocks'][$blockID]['Trees'][] = [
                    'TreeID'           => $row['TreeID'],
                    'SpeciesName'      => $row['SpeciesName'] ?? 'Unknown Species',
                    'Lattitude'        => $row['Lattitude'],
                    'Longitude'        => $row['Longitude'],
                    'timber_grade'     => $row['timber_grade'] ?? 'N/A',
                    'TreeHeight'       => $row['TreeHeight'] ?? '0.00',
                    'TreeDiameter'     => $row['TreeDiameter'] ?? '0.00',
                    'TreeStatus'       => $row['TreeStatus'] ?? 1,
                    'TreeImageDataUri' => $row['TreeImageDataUri']
                ];
                $totalTreesOwned++;
            }
        }
        $stmt->close();
        $purchases = array_values($rawPurchases);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree - Purchases & Block Portfolio</title>
    
    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/images/TREE.PNG">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/client.css">

</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="brand">
            <img src="<?php echo BASE_URL; ?>/assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1 class="brand-title">PacificTree</h1>
        </div>
        <span class="page-badge">Client Portal</span>
    </header>

    <!-- Sidebar -->
    <?php include(__DIR__ . '/../../includes/navbarClient.php'); ?>

    <!-- Main Container -->
    <main class="main-container">
        
        <!-- Welcome & Portfolio Stats Summary -->
        <div class="dashboard-hero-grid">
            <div class="hero-card">
                <h2>Purchase & Portfolio History 🛒</h2>
                <p>Manage and review all your past block acquisitions, inspect individual trees assigned to your blocks, and track tree growth metrics.</p>
            </div>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                    <div class="stat-details">
                        <h3><?php echo count($purchases); ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $totalBlocksPurchased; ?></h3>
                        <p>Blocks Acquired</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-tree"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $totalTreesOwned; ?></h3>
                        <p>Profiled Trees</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                    <div class="stat-details">
                        <h3>RM <?php echo number_format($totalSpent, 2); ?></h3>
                        <p>Total Investment</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Interactive Search & Filter Options -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="filterPurchases()" placeholder="Search by Order ID, Block ID, or Species Name...">
            </div>
            <div class="filter-actions">
                <button type="button" class="btn-action btn-expand" onclick="expandAll()">
                    <i class="fa-solid fa-angles-down"></i> Expand All
                </button>
                <button type="button" class="btn-action btn-collapse" onclick="collapseAll()">
                    <i class="fa-solid fa-angles-up"></i> Collapse All
                </button>
            </div>
        </div>

        <!-- Purchase List Accordion Section -->
        <section id="purchaseList">
            <?php if (!empty($purchases)): ?>
                <?php foreach ($purchases as $index => $sale): ?>
                    <div class="purchase-card <?php echo $index === 0 ? 'active' : ''; ?>" id="sale-card-<?php echo $sale['SaleID']; ?>" data-search="<?php echo strtolower($sale['SaleID'] . ' ' . implode(' ', array_keys($sale['Blocks']))); ?>">
                        <div class="purchase-header" onclick="toggleAccordion('sale-card-<?php echo $sale['SaleID']; ?>')">
                            <div>
                                <h3 style="margin:0; font-size:1.05rem; color:#fff;">
                                    Order #<?php echo $sale['SaleID']; ?>
                                    <span style="font-size:0.85rem; color:#94a3b8; font-weight:normal; margin-left:10px;">
                                        <i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($sale['DateSold'])); ?>
                                    </span>
                                </h3>
                                <small style="color:#94a3b8;">Contains <?php echo count($sale['Blocks']); ?> Block(s)</small>
                            </div>
                            <div style="display:flex; align-items:center; gap:1.25rem;">
                                <span class="status-badge <?php echo strtolower($sale['PaymentStatus']) === 'completed' ? 'status-completed' : 'status-pending'; ?>">
                                    <?php echo htmlspecialchars($sale['PaymentStatus']); ?>
                                </span>
                                <strong style="color:#4caf50; font-size:1.1rem;">RM <?php echo number_format($sale['TotalPrice'], 2); ?></strong>
                                <i class="fa-solid fa-chevron-down chevron-icon" style="color:#94a3b8;"></i>
                            </div>
                        </div>

                        <div class="purchase-body" style="<?php echo $index === 0 ? 'display:block;' : 'display:none;'; ?>">
                            <?php foreach ($sale['Blocks'] as $block): ?>
                                <div class="block-section">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                                        <h4 style="margin:0; color:#fff;"><i class="fa-solid fa-cube" style="color:#4caf50;"></i> Block #<?php echo $block['BlockID']; ?></h4>
                                        <small style="color:#94a3b8;">Block Value: RM <?php echo number_format($block['BlockPrice'], 2); ?></small>
                                    </div>

                                    <?php if (!empty($block['Trees'])): ?>
                                        <div class="tree-grid">
                                            <?php foreach ($block['Trees'] as $tree): ?>
                                                <div class="tree-card" onclick='openModal(<?php echo json_encode($tree); ?>)'>
                                                    <img src="<?php echo $tree['TreeImageDataUri']; ?>" alt="Tree Image">
                                                    <strong style="color:#fff; font-size:0.88rem; display:block; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                                                        <?php echo htmlspecialchars($tree['SpeciesName']); ?>
                                                    </strong>
                                                    <div style="display:flex; justify-content:space-between; margin-top:0.35rem; font-size:0.78rem; color:#94a3b8;">
                                                        <?php 
                                                            // Safely cast to float and round to 2 decimal places
                                                            $formattedHeight   = isset($tree['TreeHeight']) ? number_format((float)$tree['TreeHeight'], 2) : '0.00';
                                                            $formattedDiameter = isset($tree['TreeDiameter']) ? number_format((float)$tree['TreeDiameter'], 2) : '0.00';
                                                        ?>

                                                        <div class="tree-stats">
                                                            <div>
                                                                <small class="stat-label">Ht:</small>
                                                                <span class="stat-value"><?php echo $formattedHeight; ?>m</span>
                                                            </div>
                                                            <div>
                                                                <small class="stat-label">Dia:</small>
                                                                <span class="stat-value"><?php echo $formattedDiameter; ?>cm</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p style="font-size:0.85rem; color:#94a3b8; margin:0.5rem 0 0 0;">No active trees currently linked to this block profile.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div style="height: 1rem;"></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="info-card empty-state">
                    <i class="fa-solid fa-box-open" style="font-size:3rem; color:#64748b; margin-bottom:1rem;"></i>
                    <h3>No Purchases Found</h3>
                    <p>You have not made any tree block acquisitions yet.</p>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <!-- Interactive Tree Detail Modal -->
    <div id="treeModal" class="modal-overlay">
        <div class="modal-card">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div style="text-align: center;">
                <img id="modalTreeImg" src="" alt="Tree Image" style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                <h3 id="modalSpecies" style="margin:0 0 0.25rem 0; color:#fff;">Species Name</h3>
                <p style="color: #94a3b8; font-size: 0.85rem;" id="modalMeta">Tree ID: - | Grade: -</p>
            </div>
            <hr style="margin: 1rem 0; border: none; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-around; text-align: center;">
                <div>
                    <h4 id="modalHeight" style="margin:0; color:#4caf50; font-size:1.2rem;">-</h4>
                    <small style="color:#94a3b8;">Height (m)</small>
                </div>
                <div>
                    <h4 id="modalDiameter" style="margin:0; color:#4caf50; font-size:1.2rem;">-</h4>
                    <small style="color:#94a3b8;">Diameter (cm)</small>
                </div>
                <div>
                    <h4 id="modalStatus" style="margin:0; color:#4caf50; font-size:1.2rem;">-</h4>
                    <small style="color:#94a3b8;">Health Status</small>
                </div>
            </div>
            <div style="margin-top:1.25rem; text-align:center;">
                <button onclick="closeModal()" style="background:#2e7d32; color:#fff; border:none; padding:0.5rem 1.5rem; border-radius:6px; cursor:pointer; font-weight:600;">Close View</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include(__DIR__ . '/../../includes/footerClient.php'); ?>

    <!-- Interactive Logic Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/purchases.js" defer></script>

</body>
</html>