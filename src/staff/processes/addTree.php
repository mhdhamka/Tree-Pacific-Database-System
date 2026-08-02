<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Add Tree</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        
        <!-- Sidebar -->
        <?php include(__DIR__ . '../../../includes/sidebar.php'); ?>

        <!-- Main Content Wrapper -->
        <main class="main-content">
            
            <header class="topbar">
                <h1 class="topbar-title">Tree Block inventory & Spatial Mapping</h1>
            </header>

            <div class="content-body">
                
                <!-- Breadcrumbs -->
                <nav>
                    <ul class="breadcrumb">
                        <li><a href="../../views/staff/inventory.php">Inventory</a></li>
                        <li class="separator"><i class="fa-solid fa-angle-right"></i></li>
                        <li class="active">Add Tree</li>
                    </ul>
                </nav>

                <!-- Form Card Component -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Register New Tree</h2>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="../../controllers/staff/inventoryController.php?action=addTree">
                            
                            <!-- Species Name -->
                            <div class="form-group">
                                <label for="speciesname">Species Name</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-leaf"></i>
                                    <input type="text" class="form-control-input" name="speciesname" id="speciesname" placeholder="e.g. Durian Musang King" required>
                                </div>
                            </div>

                            <!-- Timber Grade Dropdown -->
                            <div class="form-group">
                                <label for="timber_grade">Timber Grade</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-award"></i>
                                    <select class="form-control-input" name="timber_grade" id="timber_grade" required>
                                        <option value="" disabled selected>Select Grade</option>
                                        <option value="A">Grade A (Premium)</option>
                                        <option value="B">Grade B (Standard)</option>
                                        <option value="C">Grade C (Utility)</option>
                                        <option value="D">Grade D (Low)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Initial Tree Height -->
                            <div class="form-group">
                                <label for="treeheight">Tree Height (m)</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-ruler-vertical"></i>
                                    <input type="number" step="0.01" class="form-control-input" name="treeheight" id="treeheight" placeholder="e.g. 12.50" required>
                                </div>
                            </div>

                            <!-- Initial Tree Diameter -->
                            <div class="form-group">
                                <label for="treediameter">Tree Diameter (cm)</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-ruler-horizontal"></i>
                                    <input type="number" step="0.01" class="form-control-input" name="treediameter" id="treediameter" placeholder="e.g. 45.00" required>
                                </div>
                            </div>

                            <!-- Leaflet Map Selector -->
                            <div class="form-group">
                                <label>Pick Location on Map</label>
                                <div id="treeMap" style="height: 300px; width: 100%; border-radius: 8px; margin-bottom: 12px; border: 1px solid #ccc;"></div>
                                <small style="color: #666;">Click anywhere on the map or drag the marker to automatically capture coordinates.</small>
                            </div>

                            <!-- Latitude -->
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <input type="text" class="form-control-input" name="latitude" id="latitude" placeholder="e.g. 1.5533" readonly required>
                                </div>
                            </div>

                            <!-- Longitude -->
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <input type="text" class="form-control-input" name="longitude" id="longitude" placeholder="e.g. 110.3593" readonly required>
                                </div>
                            </div>

                            <!-- Block ID (Foreign Key Dropdown) -->
                            <div class="form-group">
                                <label for="blockID">Block ID</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-cubes"></i>
                                    <select class="form-control-input" name="blockID" id="blockID" required>
                                        <option value="">Select Block</option>
                                        <?php
                                        // Query available blocks from the block table
                                        $blockQuery = "SELECT BlockID, OrchardID FROM block";
                                        $blockResult = mysqli_query($conn, $blockQuery);
                                        if ($blockResult && mysqli_num_rows($blockResult) > 0) {
                                            while ($block = mysqli_fetch_assoc($blockResult)) {
                                                echo '<option value="' . htmlspecialchars($block['BlockID']) . '">Block #' . htmlspecialchars($block['BlockID']) . ' (Orchard: ' . htmlspecialchars($block['OrchardID']) . ')</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="submit" name="addTree" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Add Tree
                                </button>
                                <a href="../../views/staff/inventory.php" class="btn btn-outline-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '../../../includes/footer.php'); ?>

        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Default coordinates (e.g., Sarawak center / Kuching: 1.5533, 110.3593)
            const defaultLat = 1.5533;
            const defaultLng = 110.3593;

            // 1. Initialize Map
            const map = L.map('treeMap').setView([defaultLat, defaultLng], 13);

            // 2. Add OpenStreetMap Tile Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // 3. Place a Draggable Marker
            let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            // Helper to update field values
            function updateCoords(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
            }

            // Set initial values on load
            updateCoords(defaultLat, defaultLng);

            // Event 1: Dragging Marker
            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updateCoords(position.lat, position.lng);
            });

            // Event 2: Clicking Map
            map.on('click', function (e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
            });

            // If modal triggers the map view, trigger resize to prevent broken rendering
            setTimeout(() => { map.invalidateSize(); }, 400);
        });
    </script>
</body>
</html>