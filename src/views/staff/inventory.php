<?php 
include(__DIR__ . '/../../config/dbConnect.php');

// Optional Block Filter from GET parameter
$selectedBlock = isset($_GET['blockID']) ? intval($_GET['blockID']) : null;

// Initialize metrics for dashboard summary (if needed)
$totalInventoryValue = 0;
$totalTreeCount = 0;

// Initialize array to hold tree data for spatial mapping
$mapTreeData = [];

// Updated SQL query: Pulls height, diameter, and status from the LATEST treeupdate record for each tree
$treeSql = "SELECT 
                t.TreeID, 
                t.SpeciesName, 
                t.Lattitude, 
                t.Longitude, 
                t.BlockID, 
                t.timber_grade,
                b.BasePrice, 
                COALESCE(tu.TreeHeight, t.TreeHeight) AS TreeHeight,
                COALESCE(tu.TreeDiameter, t.TreeDiameter) AS TreeDiameter,
                COALESCE(tu.TreeStatus, 1) AS TreeStatus,
                tu.UpdateDate
            FROM tree t 
            LEFT JOIN block b ON t.BlockID = b.BlockID
            LEFT JOIN (
                SELECT u1.TreeID, u1.TreeHeight, u1.TreeDiameter, u1.TreeStatus, u1.UpdateDate
                FROM treeupdate u1
                INNER JOIN (
                    SELECT TreeID, MAX(UpdateID) as MaxUpdateID 
                    FROM treeupdate 
                    GROUP BY TreeID
                ) u2 ON u1.UpdateID = u2.MaxUpdateID
            ) tu ON t.TreeID = tu.TreeID";

if ($selectedBlock) {
    $treeSql .= " WHERE t.BlockID = " . intval($selectedBlock);
}

$treeSql .= " ORDER BY t.TreeID ASC";

$treeRes = mysqli_query($conn, $treeSql);

// Store rows in an array for both Map JSON and Table rendering
$treeRows = [];
if ($treeRes && mysqli_num_rows($treeRes) > 0) {
    while ($tRow = mysqli_fetch_assoc($treeRes)) {
        
        // Extract biometrics from query with fallbacks
        $rawHeight   = isset($tRow['TreeHeight']) ? floatval($tRow['TreeHeight']) : 0.0;
        $rawDiameter = isset($tRow['TreeDiameter']) ? floatval($tRow['TreeDiameter']) : 0.0;

        // -----------------------------------------------------------------
        // 1. FORMAT BIOMETRICS (Prevents long floating-point decimals)
        // -----------------------------------------------------------------
        $formattedHeight = number_format($rawHeight, 2) . ' m';

        // Auto-detect if diameter is in meters (< 5.0) and convert to cm, otherwise keep as cm
        $diameterCmVal     = ($rawDiameter > 0 && $rawDiameter < 5.0) ? $rawDiameter * 100 : $rawDiameter;
        $formattedDiameter = number_format($diameterCmVal, 2) . ' cm';

        // Store cleaned values back in $tRow
        $tRow['TreeHeight']        = $rawHeight;
        $tRow['TreeDiameter']      = $rawDiameter;
        $tRow['formattedHeight']   = $formattedHeight;
        $tRow['formattedDiameter'] = $formattedDiameter;
        $tRow['timber_grade']      = htmlspecialchars($tRow['timber_grade'] ?? 'N/A');

        $treeRows[] = $tRow;

        $lat = floatval($tRow['Lattitude']);
        $lng = floatval($tRow['Longitude']);

        // Format status code into human-readable label
        $statusText = 'Healthy';
        if (isset($tRow['TreeStatus'])) {
            $statusText = ($tRow['TreeStatus'] == 1) ? 'Healthy' : 'Requires Attention';
        }

        // Collect coordinates and attributes for Leaflet JS
        if ($lat != 0 && $lng != 0) {
            $mapTreeData[] = [
                'id'                => htmlspecialchars($tRow['TreeID']),
                'species'           => htmlspecialchars($tRow['SpeciesName']),
                'lat'               => $lat,
                'lng'               => $lng,
                'block'             => htmlspecialchars($tRow['BlockID']),
                'height'            => $rawHeight,
                'diameter'          => $rawDiameter,
                'formattedHeight'   => $formattedHeight,
                'formattedDiameter' => $formattedDiameter,
                'timberGrade'       => $tRow['timber_grade'],
                'price'             => is_numeric($tRow['BasePrice']) ? 'RM ' . number_format($tRow['BasePrice'], 2) : 'N/A',
                'status'            => $statusText
            ];
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Tree Block Inventory & Spatial Mapping</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js CSS (External Mapping Library) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

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
                <h1 class="topbar-title">Tree Block Inventory & Spatial Mapping</h1>
            </header>

            <div class="content-body">

                <!-- 1. Block Overview Summary Cards -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-cubes"></i> Land Blocks Summary</h2>
                        <a href="../../staff/processes/addBlock.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add New Block
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Block ID</th>
                                    <th>Base Price</th>
                                    <th>Parent Orchard ID</th>
                                    <th>Filter Action</th>
                                    <th style="text-align: right;">Management</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $blockSql = "SELECT * FROM block";
                                $blockRes = mysqli_query($conn, $blockSql);

                                if ($blockRes && mysqli_num_rows($blockRes) > 0) {
                                    while ($bRow = mysqli_fetch_assoc($blockRes)) {
                                        $bID = htmlspecialchars($bRow['BlockID']);
                                        $price = is_numeric($bRow['BasePrice']) ? 'RM ' . number_format($bRow['BasePrice'], 2) : htmlspecialchars($bRow['BasePrice']);
                                        $oID = htmlspecialchars($bRow['OrchardID']);
                                        $isFiltered = isset($selectedBlock) && ($selectedBlock == $bID);

                                        echo "<tr class='" . ($isFiltered ? 'table-active' : '') . "'>
                                                <td><span class='badge-chip'>Block {$bID}</span></td>
                                                <td><span class='price-text'>{$price}</span></td>
                                                <td><span class='badge-chip'>Orchard {$oID}</span></td>
                                                <td>
                                                    <a href='inventory.php?blockID={$bID}' class='btn btn-outline-secondary btn-sm'>
                                                        <i class='fa-solid fa-filter'></i> " . ($isFiltered ? "Showing Trees" : "View Trees") . "
                                                    </a>
                                                </td>
                                                <td style='text-align: right;'>
                                                    <div class='actions-cell' style='justify-content: flex-end;'>
                                                        <a href='../../staff/processes/updateBlock.php?updateID={$bID}' class='btn btn-outline-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i> Edit</a>
                                                        <a href='../../controllers/staff/inventoryController.php?action=deleteBlock&deleteID={$bID}' 
                                                        class='btn btn-outline-danger btn-sm' 
                                                        onclick=\"return confirm('Are you sure you want to delete Block #{$bID}? This action cannot be undone.');\">
                                                            <i class='fa-solid fa-trash'></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center;'>No blocks found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Interactive Leaflet Spatial Map Container -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-map-location-dot"></i> Interactive Spatial Map</h2>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <!-- Store PHP JSON data inside data-trees attribute -->
                        <div id="treeMap" 
                             data-trees='<?php echo htmlspecialchars(json_encode($mapTreeData), ENT_QUOTES, "UTF-8"); ?>' 
                             style="height: 420px; width: 100%; border-radius: 0 0 8px 8px; z-index: 1;">
                        </div>
                    </div>
                </div>

                <!-- 3. Trees Inventory Card with Spatial Data -->
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-leaf"></i> Tree Records</h2>
                            <?php if ($selectedBlock): ?>
                                <small style="color: #666;">Filtering by Block #<?php echo $selectedBlock; ?> 
                                    (<a href="inventory.php">Clear Filter</a>)
                                </small>
                            <?php endif; ?>
                        </div>
                        <a href="../../staff/processes/addTree.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Tree
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tree ID</th>
                                    <th>Species & Grade</th>
                                    <th>Height & Diameter</th>
                                    <th>GPS Coordinates (Lat, Long)</th>
                                    <th>Assigned Block</th>
                                    <th>Block Price</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($treeRows)) {
                                    foreach ($treeRows as $tRow) {
                                        $tID       = htmlspecialchars($tRow['TreeID']);
                                        $species   = htmlspecialchars($tRow['SpeciesName']);
                                        $lat       = floatval($tRow['Lattitude']);
                                        $lng       = floatval($tRow['Longitude']);
                                        $bID       = htmlspecialchars($tRow['BlockID']);
                                        
                                        // Use the pre-formatted height and diameter calculated in the setup script
                                        $heightStr = $tRow['formattedHeight'] ?? '0.00 m';
                                        $dbhStr    = $tRow['formattedDiameter'] ?? '0.00 cm';
                                        
                                        $grade     = htmlspecialchars($tRow['timber_grade'] ?? 'N/A');
                                        $bPrice    = is_numeric($tRow['BasePrice']) ? 'RM ' . number_format($tRow['BasePrice'], 2) : 'N/A';

                                        echo "<tr>
                                                <td><strong>#{$tID}</strong></td>
                                                <td>{$species}<br><small style='color: #666;'>Grade: {$grade}</small></td>
                                                <td>{$heightStr} &times; {$dbhStr}</td>
                                                <td>
                                                    <a href='https://maps.google.com/?q={$lat},{$lng}' target='_blank' class='map-link'>
                                                        <i class='fa-solid fa-location-dot'></i> {$lat}, {$lng}
                                                    </a>
                                                </td>
                                                <td><span class='badge-block'>Block {$bID}</span></td>
                                                <td><span class='price-text'>{$bPrice}</span></td>
                                                <td style='text-align: right;'>
                                                    <div class='actions-cell' style='justify-content: flex-end;'>
                                                        <a href='../../staff/processes/updateTree.php?updateID={$tID}' class='btn btn-outline-primary btn-sm'>
                                                            <i class='fa-solid fa-pen'></i> Edit
                                                        </a>
                                                        <a href='../../controllers/staff/inventoryController.php?action=delete&deleteID={$tID}' 
                                                        class='btn btn-outline-danger btn-sm' 
                                                        onclick=\"return confirm('Are you sure you want to delete this tree?');\">
                                                            <i class='fa-solid fa-trash'></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>No tree records found for this view.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Audit Log / Field Updates -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-clipboard-list"></i> Field Inspection Log</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Log ID</th>
                                    <th>Tree ID</th>
                                    <th>Inspector Staff</th>
                                    <th>Timber Grade</th>
                                    <th>Height</th>
                                    <th>Diameter</th>
                                    <th>Status</th>
                                    <th>Logged Date</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // JOIN tree to get timber_grade and users to get staff name
                                $logSql = "SELECT 
                                            tu.*, 
                                            t.timber_grade,
                                            u.Username AS StaffName
                                        FROM treeupdate tu
                                        LEFT JOIN tree t ON tu.TreeID = t.TreeID
                                        LEFT JOIN user u ON tu.StaffID = u.UserID
                                        ORDER BY tu.UpdateDate ASC, tu.UpdateID ASC";
                                        
                                $logRes = mysqli_query($conn, $logSql);

                                if ($logRes && mysqli_num_rows($logRes) > 0) {
                                    while ($lRow = mysqli_fetch_assoc($logRes)) {
                                        
                                        // Human-readable status mapping
                                        $statusRaw = $lRow['TreeStatus'];
                                        if ($statusRaw == 1 || $statusRaw === '1') {
                                            $statusBadge = "<span class='badge badge-success' style='background-color:#d1e7dd; color:#0f5132; padding:4px 8px; border-radius:4px; font-weight:600;'>Healthy</span>";
                                        } else {
                                            $statusBadge = "<span class='badge badge-warning' style='background-color:#fff3cd; color:#664d03; padding:4px 8px; border-radius:4px; font-weight:600;'>Requires Attention</span>";
                                        }

                                        // Staff Name fallback
                                        $staffDisplayName = !empty($lRow['StaffName']) 
                                            ? htmlspecialchars($lRow['StaffName']) . " (#" . htmlspecialchars($lRow['StaffID']) . ")"
                                            : "Staff #" . htmlspecialchars($lRow['StaffID']);

                                        // Timber Grade display
                                        $gradeDisplay = !empty($lRow['timber_grade']) 
                                            ? "Grade " . htmlspecialchars($lRow['timber_grade']) 
                                            : 'N/A';

                                        // Formatted Date
                                        $formattedDate = !empty($lRow['UpdateDate']) 
                                            ? date("d M Y", strtotime($lRow['UpdateDate'])) 
                                            : 'N/A';

                                        echo "<tr>
                                                <td>#" . htmlspecialchars($lRow['UpdateID']) . "</td>
                                                <td><strong>#" . htmlspecialchars($lRow['TreeID']) . "</strong></td>
                                                <td>" . $staffDisplayName . "</td>
                                                <td><span class='badge-grade'>" . $gradeDisplay . "</span></td>
                                                <td>" . htmlspecialchars(number_format((float)$lRow['TreeHeight'], 2)) . " m</td>
                                                <td>" . htmlspecialchars(number_format((float)$lRow['TreeDiameter'], 2)) . " cm</td>
                                                <td>" . $statusBadge . "</td>
                                                <td>" . $formattedDate . "</td>
                                                <td style='text-align: right;'>
                                                    <a href='../../controllers/staff/inventoryController.php?action=deleteTreeUpdate&deleteID=" . htmlspecialchars($lRow['UpdateID']) . "' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this inspection log?');\"><i class='fa-solid fa-trash'></i> Delete</a>
                                                </td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' style='text-align:center;'>No inspection logs found.</td></tr>";
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

    <!-- Leaflet JS Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- External JavaScript File -->
    <script src="../../../assets/js/inventory.js"></script>
</body>
</html>