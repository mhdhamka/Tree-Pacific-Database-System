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
    // Fallback/Demo values if testing without active login session
    $UserID     = 1;
    $clientName = "Client";
} else {
    $UserID     = $_SESSION['UserID'] ?? $_SESSION['user_id'];
    $clientName = $_SESSION['RealName'] ?? $_SESSION['username'] ?? 'Valued Client';
}

// 3. Initialize metrics with safe defaults
$totalTrees  = 0;
$totalBlocks = 0;
$co2Offset   = 0;
$oxygenGen   = 0;
$clientTrees = [];

// 4. Fetch actual dashboard data if database connection exists
if (isset($conn) && $conn) {
    // Fetch Client Real Name from 'user' table
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

    // Query 1: Fetch Total Blocks & Trees via client.UserID -> sale.ClientID -> purchase.BlockID -> tree
    $statsQuery = "SELECT COUNT(DISTINCT p.BlockID) AS total_blocks, COUNT(t.TreeID) AS total_trees 
                   FROM client c
                   INNER JOIN sale s ON c.UserID = s.ClientID
                   INNER JOIN purchase p ON s.SaleID = p.SaleID
                   LEFT JOIN tree t ON p.BlockID = t.BlockID
                   WHERE c.UserID = ?";
    
    if ($stmt = $conn->prepare($statsQuery)) {
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $totalBlocks = $row['total_blocks'] ?? 0;
            $totalTrees  = $row['total_trees'] ?? 0;
        }
        $stmt->close();
    }

    // Calculate environmental impact based on tree count
    $co2Offset = $totalTrees * 21.77; // Average kg CO2 per tree/year
    $oxygenGen = $totalTrees * 260;   // Average Liters O2 per tree/day

    // Query 2: Fetch Profiled Trees, getting latest status/measurements from 'treeupdate'
    $treeQuery = "SELECT DISTINCT 
                    t.TreeID, 
                    t.BlockID, 
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
                  INNER JOIN tree t ON p.BlockID = t.BlockID
                  LEFT JOIN (
                      SELECT tu1.* 
                      FROM treeupdate tu1
                      INNER JOIN (
                          SELECT TreeID, MAX(UpdateDate) AS MaxDate 
                          FROM treeupdate 
                          GROUP BY TreeID
                      ) tu2 ON tu1.TreeID = tu2.TreeID AND tu1.UpdateDate = tu2.MaxDate
                  ) tu ON t.TreeID = tu.TreeID
                  WHERE c.UserID = ?";
                  
    if ($stmt = $conn->prepare($treeQuery)) {
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            // Convert BLOB image from treeupdate to Data URI format if present
            if (!empty($row['TreeImage'])) {
                $row['TreeImageDataUri'] = 'data:image/jpeg;base64,' . base64_encode($row['TreeImage']);
            } else {
                $row['TreeImageDataUri'] = BASE_URL . '/assets/images/TREE.PNG';
            }
            unset($row['TreeImage']); // Strip raw binary stream before converting to JSON
            $clientTrees[] = $row;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree - Client Dashboard</title>
    
    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
        
        <!-- Top Hero Grid -->
        <div class="dashboard-hero-grid">
            <!-- Welcome Hero -->
            <div class="hero-card">
                <h2>Welcome back, <?php echo htmlspecialchars($clientName); ?>!</h2>
                <p>Track your adopted tree blocks, monitor real-time growth updates, and view your cumulative environmental impact.</p>
            </div>

            <!-- Real-Time Impact Metrics Grid -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-tree"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $totalTrees; ?></h3>
                        <p>Trees Monitored</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $totalBlocks; ?></h3>
                        <p>Blocks Purchased</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-leaf"></i></div>
                    <div class="stat-details">
                        <h3><?php echo number_format($co2Offset, 1); ?> kg</h3>
                        <p>Annual CO₂ Offset</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-wind"></i></div>
                    <div class="stat-details">
                        <h3><?php echo number_format($oxygenGen); ?> L</h3>
                        <p>Daily O₂ Produced</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Latest Purchase Status Tracker -->
        <article class="info-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-truck-fast"></i> Latest Purchase Status</h3>
            </div>
            <div class="timeline">
                <div class="timeline-step completed">
                    <div class="dot"><i class="fa fa-check"></i></div>
                    <small>Payment Received</small>
                </div>
                <div class="timeline-step completed">
                    <div class="dot"><i class="fa fa-check"></i></div>
                    <small>Block Assigned</small>
                </div>
                <div class="timeline-step completed">
                    <div class="dot"><i class="fa fa-check"></i></div>
                    <small>Trees Profiled</small>
                </div>
                <div class="timeline-step completed">
                    <div class="dot"><i class="fa-solid fa-leaf"></i></div>
                    <small>Active Monitoring</small>
                </div>
            </div>
        </article>

        <!-- Interactive Leaflet Map Section -->
        <section class="map-container info-card">
            <h3><i class="fa-solid fa-map-location-dot"></i> Your Profiled Trees Location Map</h3>
            <p style="color: #94a3b8; font-size: 0.88rem; margin-top: 0.25rem;">Click on any marker on the map to inspect live height, trunk diameter, and field update photos.</p>
            <div id="map"></div>
        </section>

        <!-- Environmental Quote -->
        <article class="info-card">
            <div class="card-header">
                <h3><span>PacificTree</span> &mdash; Environmental Note</h3>
            </div>
            <div class="quote-content">
                It costs 38 trillion dollars to produce oxygen for all humans on the planet for six months. This indicates that even if we spent all of the money in the world, we would be unable to provide oxygen for all humans for six months. Trees do it for free.
            </div>
        </article>

    </main>

    <!-- Interactive Detail Modal -->
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
        </div>
    </div>

    <!-- Footer -->
    <?php include(__DIR__ . '/../../includes/footerClient.php'); ?>

    <!-- Leaflet Script -->
    <script>
        const clientTrees = <?php echo json_encode($clientTrees); ?>;

        const defaultLat = (clientTrees && clientTrees.length > 0 && clientTrees[0].Lattitude) 
            ? parseFloat(clientTrees[0].Lattitude) 
            : 1.56557;
            
        const defaultLng = (clientTrees && clientTrees.length > 0 && clientTrees[0].Longitude) 
            ? parseFloat(clientTrees[0].Longitude) 
            : 110.347;

        const map = L.map('map').setView([defaultLat, defaultLng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        if (Array.isArray(clientTrees) && clientTrees.length > 0) {
            clientTrees.forEach(tree => {
                if (tree.Lattitude && tree.Longitude) {
                    const marker = L.marker([parseFloat(tree.Lattitude), parseFloat(tree.Longitude)]).addTo(map);
                    
                    marker.bindPopup(`
                        <div style="font-family: 'Inter', sans-serif;">
                            <b style="color: #1e293b;">${tree.SpeciesName || 'Monitored Tree'}</b><br>
                            <span style="color: #475569; font-size: 0.82rem;">Height: ${tree.TreeHeight || '-'}m | Dia: ${tree.TreeDiameter || '-'}cm</span><br>
                            <a href="javascript:void(0)" onclick='openModal(${JSON.stringify(tree)})' style="color: #2e7d32; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-block; margin-top: 4px;">View Profile Details &rarr;</a>
                        </div>
                    `);
                }
            });
        }

        function openModal(tree) {
            document.getElementById('modalSpecies').innerText = tree.SpeciesName || 'Tree Profile';
            document.getElementById('modalMeta').innerText = `Tree ID: ${tree.TreeID || '-'} | Timber Grade: ${tree.timber_grade || '-'} | Block: ${tree.BlockID || '-'}`;
            document.getElementById('modalHeight').innerText = (tree.TreeHeight || '-') + 'm';
            document.getElementById('modalDiameter').innerText = (tree.TreeDiameter || '-') + 'cm';
            
            // Displays 'Healthy' for status 1, 'Monitored' for status 0
            const statusEl = document.getElementById('modalStatus');
            if (tree.TreeStatus == 1) {
                statusEl.innerText = 'Healthy';
                statusEl.style.color = '#4caf50';
            } else {
                statusEl.innerText = 'Needs Attention';
                statusEl.style.color = '#f59e0b';
            }

            document.getElementById('modalTreeImg').src = tree.TreeImageDataUri || '<?php echo BASE_URL; ?>/assets/images/TREE.PNG';
            
            document.getElementById('treeModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('treeModal').style.display = 'none';
        }
    </script>
</body>
</html>