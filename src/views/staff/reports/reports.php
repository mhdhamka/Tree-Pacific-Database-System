<?php 
// Dynamic DB Connection Resolver
include(__DIR__ . '../../../../config/dbConnect.php');

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

/* ==========================================================================
   1. Dynamic KPI Metric Queries
   ========================================================================== */
// Total Portfolio Value & Total Sold Blocks
$kpi1Query = "SELECT COUNT(DISTINCT block.BlockID) as TotalBlocks, SUM(BasePrice) as TotalRev FROM block 
              INNER JOIN purchase ON block.BlockID = purchase.BlockID";
$kpi1Res = mysqli_query($conn, $kpi1Query);
$kpi1 = mysqli_fetch_assoc($kpi1Res);

// Total Trees Monitored
$kpi2Query = "SELECT COUNT(TreeID) as TotalTrees FROM tree";
$kpi2Res = mysqli_query($conn, $kpi2Query);
$kpi2 = mysqli_fetch_assoc($kpi2Res);

// Total Partner Companies
$kpi3Query = "SELECT COUNT(CompanyID) as TotalCompanies FROM company";
$kpi3Res = mysqli_query($conn, $kpi3Query);
$kpi3 = mysqli_fetch_assoc($kpi3Res);

/* ==========================================================================
   2. Chart Aggregation Queries
   ========================================================================== */
// Chart 1: Revenue / Tree Count per Client
$clientChartLabels = [];
$clientChartData = [];
$cChartQuery = "SELECT user.RealName, SUM(block.BasePrice) as ClientSpend 
                FROM user 
                INNER JOIN client ON user.UserID = client.UserID
                INNER JOIN sale ON client.UserID = sale.ClientID
                INNER JOIN purchase ON sale.SaleID = purchase.SaleID
                INNER JOIN block ON purchase.BlockID = block.BlockID
                GROUP BY user.UserID, user.RealName LIMIT 6";
$cChartRes = mysqli_query($conn, $cChartQuery);
if ($cChartRes) {
    while ($r = mysqli_fetch_assoc($cChartRes)) {
        $clientChartLabels[] = $r['RealName'];
        $clientChartData[] = (float)$r['ClientSpend'];
    }
}

// Chart 2: Trees Breakdown per Company
$compChartLabels = [];
$compChartData = [];
$compChartQuery = "SELECT company.CompanyName, COUNT(tree.TreeID) as TreeCount
                   FROM company
                   INNER JOIN orchard ON company.OrchardID = orchard.OrchardID
                   INNER JOIN block ON orchard.OrchardID = block.OrchardID
                   INNER JOIN tree ON block.BlockID = tree.BlockID
                   GROUP BY company.CompanyID, company.CompanyName";
$compChartRes = mysqli_query($conn, $compChartQuery);
if ($compChartRes) {
    while ($r = mysqli_fetch_assoc($compChartRes)) {
        $compChartLabels[] = $r['CompanyName'];
        $compChartData[] = (int)$r['TreeCount'];
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Reports & Analytics</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../../../assets/images/TREE.PNG">
    
    <!-- DataTables & Extensions CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    
    <link rel="stylesheet" href="../../../../assets/css/reports.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <?php include(__DIR__ . '../../../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Reports & Analytics</h1>
            </header>

            <div class="content-body">

                <!-- 1. KPI Executive Summary Cards -->
                <div class="kpi-grid">
                    
                    <!-- Card 1: Total Portfolio Sales -->
                    <div class="kpi-card kpi-card-success">
                        <div class="kpi-card-inner">
                            <div class="kpi-content">
                                <span class="kpi-label">Total Portfolio Sales</span>
                                <h3 class="kpi-value">
                                    <span class="currency">RM</span> 
                                    <span class="counter" data-target="<?php echo $kpi1['TotalRev'] ?? 0; ?>" data-decimals="2">0.00</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-sack-dollar"></i>
                            </div>
                        </div>
                        <div class="kpi-glow"></div>
                    </div>

                    <!-- Card 2: Total Trees Registered -->
                    <div class="kpi-card kpi-card-primary">
                        <div class="kpi-card-inner">
                            <div class="kpi-content">
                                <span class="kpi-label">Total Trees Registered</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?php echo $kpi2['TotalTrees'] ?? 0; ?>" data-decimals="0">0</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-tree"></i>
                            </div>
                        </div>
                        <div class="kpi-glow"></div>
                    </div>

                    <!-- Card 3: Partner Companies -->
                    <div class="kpi-card kpi-card-purple">
                        <div class="kpi-card-inner">
                            <div class="kpi-content">
                                <span class="kpi-label">Partner Companies</span>
                                <h3 class="kpi-value">
                                    <span class="counter" data-target="<?php echo $kpi3['TotalCompanies'] ?? 0; ?>" data-decimals="0">0</span>
                                </h3>
                            </div>
                            <div class="kpi-icon-wrapper">
                                <i class="fa-solid fa-building"></i>
                            </div>
                        </div>
                        <div class="kpi-glow"></div>
                    </div>

                </div>

                <!-- 2. Visual Charts Row -->
                <div class="charts-row">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa-solid fa-chart-column icon-green"></i> Client Investment Value</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="clientSpendChart" height="140"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa-solid fa-chart-pie icon-blue"></i> Tree Distribution by Company</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="companyTreeChart" height="140"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 3. Navigation Tabs -->
                <div class="analytics-tabs">
                    <button class="tab-btn active" onclick="switchTab(event, 'tab-blocks')">
                        <i class="fa-solid fa-tree"></i> Client Tree Blocks
                    </button>
                    <button class="tab-btn" onclick="switchTab(event, 'tab-orchards')">
                        <i class="fa-solid fa-map-location-dot"></i> Orchards & Companies
                    </button>
                    <button class="tab-btn" onclick="switchTab(event, 'tab-trees')">
                        <i class="fa-solid fa-building"></i> Tree Mappings
                    </button>
                    <button class="tab-btn" onclick="switchTab(event, 'tab-dates')">
                        <i class="fa-solid fa-calendar-days"></i> Growth & Updates Timeline
                    </button>
                </div>

                <!-- TAB 1: Tree Blocks by Clients -->
                <div id="tab-blocks" class="tab-pane active">
                    <section class="card shadow-sm border-0 rounded-3 mt-3">
                        <div class="card-body p-3">
                            <table class="reportTable table table-striped table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Block ID</th>
                                        <th>Tree ID</th>
                                        <th>Species Name</th>
                                        <th>Coordinates</th>
                                        <th>Base Price</th>
                                        <th>Orchard ID</th>
                                        <th>Sale ID</th>
                                        <th>Client Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql1 = "SELECT * FROM tree
                                        INNER JOIN block ON tree.BlockID = block.BlockID
                                        INNER JOIN purchase ON block.BlockID = purchase.BlockID
                                        INNER JOIN sale ON purchase.SaleID = sale.SaleID
                                        INNER JOIN client ON sale.ClientID = client.UserID
                                        INNER JOIN user ON client.UserID = user.UserID";
                                $res1 = mysqli_query($conn, $sql1);
                                if ($res1 && mysqli_num_rows($res1) > 0) {
                                    while($row = mysqli_fetch_assoc($res1)) {
                                        $lat = htmlspecialchars($row["Lattitude"]);
                                        $lng = htmlspecialchars($row["Longitude"]);
                                        $title = "Tree #" . htmlspecialchars($row["TreeID"]) . " (" . htmlspecialchars($row["SpeciesName"]) . ")";
                                        
                                        echo "<tr>";
                                        echo "<td><strong>#" . htmlspecialchars($row["BlockID"]) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row["TreeID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["SpeciesName"]) . "</td>";
                                        echo "<td>
                                                <button type='button' class='btn btn-sm btn-outline-success open-map-btn rounded-pill px-3' 
                                                        data-lat='{$lat}' data-lng='{$lng}' data-title='{$title}'>
                                                    <i class='fa-solid fa-location-dot me-1'></i> {$lat}, {$lng}
                                                </button>
                                            </td>";
                                        echo "<td class='price-text fw-bold text-success'>RM " . number_format($row["BasePrice"], 2) . "</td>";
                                        echo "<td>Orchard #" . htmlspecialchars($row["OrchardID"]) . "</td>";
                                        echo "<td>Sale #" . htmlspecialchars($row["SaleID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["RealName"]) . "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- TAB 2: Orchards by Companies -->
                <div id="tab-orchards" class="tab-pane">
                    <section class="card shadow-sm border-0 rounded-3 mt-3">
                        <div class="card-body p-3">
                            <table class="reportTable table table-striped table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Orchard ID</th>
                                        <th>Address</th>
                                        <th>Coordinates</th>
                                        <th>Tree ID</th>
                                        <th>Species Name</th>
                                        <th>Company Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql2 = "SELECT * FROM orchard
                                        INNER JOIN block ON orchard.OrchardID = block.OrchardID
                                        INNER JOIN tree ON block.BlockID = tree.BlockID
                                        INNER JOIN company ON orchard.OrchardID = company.OrchardID";
                                $res2 = mysqli_query($conn, $sql2);
                                if ($res2 && mysqli_num_rows($res2) > 0) {
                                    while($row = mysqli_fetch_assoc($res2)) {
                                        $lat = htmlspecialchars($row["Lattitude"]);
                                        $lng = htmlspecialchars($row["Longitude"]);
                                        $title = "Orchard #" . htmlspecialchars($row["OrchardID"]) . " - " . htmlspecialchars($row["CompanyName"]);
                                        $addr = htmlspecialchars($row["Address"]);
                                        
                                        echo "<tr>";
                                        echo "<td><strong>Orchard #" . htmlspecialchars($row["OrchardID"]) . "</strong></td>";
                                        echo "<td>
                                                <button type='button' class='btn btn-sm btn-link text-start text-decoration-none open-map-btn p-0' 
                                                        data-lat='{$lat}' data-lng='{$lng}' data-title='{$title}' data-address='{$addr}'>
                                                    <i class='fa-solid fa-map-pin text-danger me-1'></i> {$addr}
                                                </button>
                                            </td>";
                                        echo "<td>
                                                <button type='button' class='btn btn-sm btn-outline-success open-map-btn rounded-pill px-3' 
                                                        data-lat='{$lat}' data-lng='{$lng}' data-title='{$title}' data-address='{$addr}'>
                                                    <i class='fa-solid fa-location-dot me-1'></i> {$lat}, {$lng}
                                                </button>
                                            </td>";
                                        echo "<td>" . htmlspecialchars($row["TreeID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["SpeciesName"]) . "</td>";
                                        echo "<td><strong class='company-name text-primary'>" . htmlspecialchars($row["CompanyName"]) . "</strong></td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- TAB 3: Trees by Companies -->
                <div id="tab-trees" class="tab-pane">
                    <section class="card shadow-sm border-0 rounded-3 mt-3">
                        <div class="card-body p-3">
                            <table class="reportTable table table-striped table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tree ID</th>
                                        <th>Species Name</th>
                                        <th>Coordinates</th>
                                        <th>Block ID</th>
                                        <th>Orchard ID</th>
                                        <th>Company Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql3 = "SELECT * FROM tree
                                        INNER JOIN block ON tree.BlockID = block.BlockID
                                        INNER JOIN orchard ON block.OrchardID = orchard.OrchardID
                                        INNER JOIN company ON orchard.OrchardID = company.OrchardID";
                                $res3 = mysqli_query($conn, $sql3);
                                if ($res3 && mysqli_num_rows($res3) > 0) {
                                    while($row = mysqli_fetch_assoc($res3)) {
                                        $lat = htmlspecialchars($row["Lattitude"]);
                                        $lng = htmlspecialchars($row["Longitude"]);
                                        $title = "Tree #" . htmlspecialchars($row["TreeID"]) . " (" . htmlspecialchars($row["SpeciesName"]) . ")";

                                        echo "<tr>";
                                        echo "<td><strong>#" . htmlspecialchars($row["TreeID"]) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row["SpeciesName"]) . "</td>";
                                        echo "<td>
                                                <button type='button' class='btn btn-sm btn-outline-success open-map-btn rounded-pill px-3' 
                                                        data-lat='{$lat}' data-lng='{$lng}' data-title='{$title}'>
                                                    <i class='fa-solid fa-location-dot me-1'></i> {$lat}, {$lng}
                                                </button>
                                            </td>";
                                        echo "<td>Block #" . htmlspecialchars($row["BlockID"]) . "</td>";
                                        echo "<td>Orchard #" . htmlspecialchars($row["OrchardID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["CompanyName"]) . "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- TAB 4: Trees by Planting / Growth Dates -->
                <div id="tab-dates" class="tab-pane">
                    <section class="card shadow-sm border-0 rounded-3 mt-3">
                        <div class="card-body p-3">
                            <table class="reportTable table table-striped table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Update ID</th>
                                        <th>Image</th>
                                        <th>Staff ID</th>
                                        <th>Tree ID</th>
                                        <th>Height (m)</th>
                                        <th>Diameter (m)</th>
                                        <th>Status</th>
                                        <th>Update Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql4 = "SELECT * FROM treeupdate ORDER BY UpdateDate DESC";
                                $res4 = mysqli_query($conn, $sql4);
                                if ($res4 && mysqli_num_rows($res4) > 0) {
                                    while($row = mysqli_fetch_assoc($res4)) {
                                        $imgSrc = !empty($row['TreeImage']) ? 'data:image/jpeg;base64,' . base64_encode($row['TreeImage']) : '../../../../assets/images/TREE.PNG';
                                        echo "<tr>";
                                        echo "<td>#" . htmlspecialchars($row["UpdateID"]) . "</td>";
                                        echo "<td><img src='" . $imgSrc . "' class='rounded-2 shadow-sm' style='width: 45px; height: 45px; object-fit: cover;' alt='Tree'></td>";
                                        echo "<td>Staff #" . htmlspecialchars($row["StaffID"]) . "</td>";
                                        echo "<td>Tree #" . htmlspecialchars($row["TreeID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["TreeHeight"]) . " m</td>";
                                        echo "<td>" . htmlspecialchars($row["TreeDiameter"]) . " m</td>";
                                        echo "<td><span class='badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1'>" . htmlspecialchars($row["TreeStatus"]) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($row["UpdateDate"]) . "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '/../../../includes/footer.php'); ?>
        </main>
    </div>

    <!-- Custom Location Map Modal -->
    <div id="mapModal" class="modal-overlay">
        <div class="modal-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0" id="mapModalLabel">
                    <i class="fa-solid fa-map-location-dot me-2 text-success"></i> Location Map
                </h5>
                <button type="button" class="btn-close" onclick="closeCustomModal()"></button>
            </div>
            
            <div class="p-2 bg-light rounded mb-3 border">
                <p id="mapModalAddress" class="text-muted mb-0 small fw-semibold">
                    <i class="fa-solid fa-location-dot text-danger me-1"></i> Loading coordinates...
                </p>
            </div>

            <div id="interactiveMap" style="height: 380px; width: 100%; border-radius: 8px;"></div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a id="googleMapsDirectBtn" href="#" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open in Google Maps
                </a>
                <button type="button" class="btn btn-sm btn-secondary fw-semibold rounded-3" onclick="closeCustomModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Required JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap 5 JS Bundle (Includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables PDF / Excel Export Extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        // =========================================================================
        // Global Map Variables
        // =========================================================================
        var leafletMap = null;
        var currentMarker = null;

        // Custom Modal Toggle Functions (Exposed Globally)
        function openCustomMapModal() {
            $('#mapModal').css('display', 'flex');
            
            // Force Leaflet to recalculate container dimensions when rendered in modal
            setTimeout(function() {
                if (leafletMap) {
                    leafletMap.invalidateSize();
                }
            }, 150);
        }

        function closeCustomModal() {
            $('#mapModal').css('display', 'none');
        }

        $(document).ready(function() {

            // =========================================================================
            // 1. Click Handler for Map Buttons (Custom Modal Trigger)
            // =========================================================================
            $(document).on('click', '.open-map-btn', function(e) {
                e.preventDefault();

                var lat = parseFloat($(this).data('lat'));
                var lng = parseFloat($(this).data('lng'));
                var title = $(this).data('title') || 'Location Marker';
                var address = $(this).data('address') || '';

                if (isNaN(lat) || isNaN(lng)) {
                    alert('Invalid coordinates provided.');
                    return;
                }

                // Update UI elements in Custom Modal
                $('#mapModalLabel').html('<i class="fa-solid fa-map-location-dot me-2 text-success"></i> ' + title);
                $('#mapModalAddress').html('<i class="fa-solid fa-location-dot text-danger me-1"></i> ' + (address ? 'Address: ' + address : 'Coords: ' + lat + ', ' + lng));
                $('#googleMapsDirectBtn').attr('href', 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng);

                // Open the custom overlay modal
                openCustomMapModal();

                // Render/Update Leaflet Map
                if (!leafletMap) {
                    leafletMap = L.map('interactiveMap').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(leafletMap);
                } else {
                    leafletMap.setView([lat, lng], 15);
                }

                // Remove existing pin/marker if present
                if (currentMarker) {
                    leafletMap.removeLayer(currentMarker);
                }

                // Pin new marker and open popup
                var popupContent = '<b>' + title + '</b>';
                if (address) popupContent += '<br><small>' + address + '</small>';

                currentMarker = L.marker([lat, lng]).addTo(leafletMap)
                    .bindPopup(popupContent)
                    .openPopup();
            });

            // =========================================================================
            // 2. Event Listener: Close Custom Modal when clicking overlay backdrop
            // =========================================================================
            $('#mapModal').on('click', function(e) {
                if ($(e.target).is('#mapModal')) {
                    closeCustomModal();
                }
            });

            // =========================================================================
            // Dynamic Count-Up Animation for KPI Values
            // =========================================================================
            $('.counter').each(function() {
                var $this = $(this);
                var target = parseFloat($this.data('target')) || 0;
                var decimals = parseInt($this.data('decimals')) || 0;

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
            
            // =========================================================================
            // DataTables Initialization
            // =========================================================================
            $('.reportTable').DataTable({
                dom: '<"dt-custom-header d-flex justify-content-between align-items-center mb-3"fB>rt<"dt-custom-footer d-flex justify-content-between align-items-center mt-3"ip>',
                language: {
                    search: "", 
                    searchPlaceholder: "Search by client name, ID, or company ..." 
                },
                buttons: [
                    { 
                        extend: 'excelHtml5', 
                        className: 'btn btn-export btn-excel', 
                        text: '<i class="fa-solid fa-file-excel"></i> Excel' 
                    },
                    { 
                        extend: 'pdfHtml5', 
                        className: 'btn btn-export btn-pdf', 
                        text: '<i class="fa-solid fa-file-pdf"></i> PDF' 
                    }
                ],
                pageLength: 10,
                responsive: true
            });

            // =========================================================================
            // Chart 1: Client Spend
            // =========================================================================
            const canvas1 = document.getElementById('clientSpendChart');
            if (canvas1) {
                const ctx1 = canvas1.getContext('2d');
                const greenGradient = ctx1.createLinearGradient(0, 0, 0, 400);
                greenGradient.addColorStop(0, '#16a34a');
                greenGradient.addColorStop(1, '#15803d');

                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($clientChartLabels ?? []); ?>,
                        datasets: [{
                            label: 'Total Spend',
                            data: <?php echo json_encode($clientChartData ?? []); ?>,
                            backgroundColor: greenGradient,
                            hoverBackgroundColor: '#111827',
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
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        let val = context.raw || 0;
                                        return ' Spend: RM ' + val.toLocaleString('en-MY', { minimumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#6b7280' }
                            },
                            y: {
                                grid: { color: '#f3f4f6', drawBorder: false },
                                ticks: {
                                    font: { size: 11 },
                                    color: '#6b7280',
                                    callback: function(value) {
                                        return 'RM ' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // =========================================================================
            // Chart 2: Company Distribution
            // =========================================================================
            const canvas2 = document.getElementById('companyTreeChart');
            if (canvas2) {
                const ctx2 = canvas2.getContext('2d');

                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($compChartLabels ?? []); ?>,
                        datasets: [{
                            data: <?php echo json_encode($compChartData ?? []); ?>,
                            backgroundColor: ['#16a34a', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#06b6d4'],
                            hoverBackgroundColor: ['#15803d', '#2563eb', '#7c3aed', '#d97706', '#db2777', '#0891b2'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        animation: {
                            animateScale: true,
                            animateRotate: true,
                            duration: 1000
                        },
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 18,
                                    font: { size: 12, weight: '500' },
                                    color: '#374151'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let value = context.raw || 0;
                                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return ` ${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        // =========================================================================
        // Tab Switching Function
        // =========================================================================
        function switchTab(evt, tabId) {
            $('.tab-pane').removeClass('active');
            $('.tab-btn').removeClass('active');
            
            $('#' + tabId).addClass('active');
            $(evt.currentTarget).addClass('active');

            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        }
    </script>

</body>
</html>