<?php
include(__DIR__ . '/../../config/dbConnect.php');
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Add User</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <!-- Leaflet CSS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <link rel="stylesheet" href="../../../assets/css/staff.css">

    <style>
        /* Custom Helper Styling for Map & Phone */
        #map {
            height: 250px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            margin-top: 8px;
            z-index: 1;
        }
        .map-search-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }
        .phone-feedback {
            font-size: 0.82em;
            margin-top: 4px;
            display: block;
        }
        .phone-valid { color: #16a34a; }
        .phone-invalid { color: #dc2626; }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Users & System Administration</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumbs -->
                <ul class="breadcrumb">
                    <li><a href="../../views/staff/dashboard.php">Users</a></li>
                    <li class="separator"><i class="fa fa-angle-right"></i></li>
                    <li class="active">Add User</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Register New Account</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                            <div class="alert alert-success">User added successfully!</div>
                        <?php elseif (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">Error processing request.</div>
                        <?php endif; ?>

                        <form method="POST" action="../../controllers/staff/userController.php?action=add">
                            
                            <!-- Real Name -->
                            <div class="form-group">
                                <label for="realname">Real Name</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-id-card"></i>
                                    <input type="text" class="form-control-input" name="realname" id="realname" placeholder="Enter full name" required>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-user"></i>
                                    <input type="text" class="form-control-input" name="username" id="username" placeholder="Enter username" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" class="form-control-input" name="email" id="email" placeholder="name@example.com" required>
                                </div>
                            </div>

                            <!-- Interactive Phone Field -->
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-phone"></i>
                                    <input type="tel" class="form-control-input" name="phone" id="phone" placeholder="+60 1X-XXX XXXX" autocomplete="off">
                                </div>
                                <span id="phone-status" class="phone-feedback"></span>
                            </div>

                            <!-- Interactive Address Field with Leaflet Map -->
                            <div class="form-group">
                                <label for="address">Address & Map Location</label>
                                
                                <!-- Map Search Controls -->
                                <div class="map-search-bar">
                                    <div class="input-wrapper" style="flex: 1;">
                                        <i class="fa fa-search"></i>
                                        <input type="text" id="mapSearchInput" class="form-control-input" placeholder="Search address or location...">
                                    </div>
                                    <button type="button" class="btn btn-light" id="btnSearchMap" title="Search Map">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button type="button" class="btn btn-light" id="btnLocateMe" title="Use My Current Location">
                                        <i class="fa fa-location-crosshairs"></i>
                                    </button>
                                </div>

                                <!-- Leaflet Map Container -->
                                <div id="map"></div>

                                <!-- Textarea storing resolved address -->
                                <div class="input-wrapper" style="margin-top: 8px;">
                                    <textarea class="form-control-input" name="address" id="address" rows="3" placeholder="Selected full address will appear here..."></textarea>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-lock"></i>
                                    <input type="password" class="form-control-input" name="password" id="password" placeholder="Enter password" required>
                                </div>
                            </div>

                            <!-- User Role -->
                            <div class="form-group">
                                <label>User Role</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="C" required checked> Client
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="S" required> Staff
                                    </label>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="submit" name="addUser" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i> Create User
                                </button>
                                <a href="../../views/staff/dashboard.php" class="btn btn-outline-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '/../../includes/footer.php'); ?>

        </main>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // ==========================================
            // 1. INTERACTIVE PHONE NUMBER FORMATTING
            // ==========================================
            const phoneInput = document.getElementById('phone');
            const phoneStatus = document.getElementById('phone-status');

            phoneInput.addEventListener('input', function (e) {
                let x = e.target.value.replace(/\D/g, ''); // Remove non-digits
                
                // Auto prefix +60 if starting with digits
                if (x.startsWith('60')) {
                    x = x.substring(2);
                } else if (x.startsWith('0')) {
                    x = x.substring(1);
                }

                let formatted = '';
                if (x.length > 0) {
                    formatted = '+60 ';
                    if (x.length <= 2) {
                        formatted += x;
                    } else if (x.length <= 5) {
                        formatted += x.substring(0, 2) + '-' + x.substring(2);
                    } else if (x.length <= 9) {
                        formatted += x.substring(0, 2) + '-' + x.substring(2, 5) + ' ' + x.substring(5);
                    } else {
                        formatted += x.substring(0, 2) + '-' + x.substring(2, 6) + ' ' + x.substring(6, 10);
                    }
                }
                
                e.target.value = formatted;

                // Validation status
                const rawDigits = formatted.replace(/\D/g, '');
                if (rawDigits.length >= 11 && rawDigits.length <= 12) {
                    phoneStatus.textContent = '✓ Valid Malaysian phone structure';
                    phoneStatus.className = 'phone-feedback phone-valid';
                } else if (rawDigits.length > 0) {
                    phoneStatus.textContent = '⚠ Incomplete mobile number';
                    phoneStatus.className = 'phone-feedback phone-invalid';
                } else {
                    phoneStatus.textContent = '';
                }
            });


            // ==========================================
            // 2. LEAFLET MAP & REVERSE GEOCODING
            // ==========================================
            // Default center: Kuching, Sarawak (1.5535, 110.3593)
            const defaultLat = 1.5535;
            const defaultLng = 110.3593;

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            // Add OpenStreetMap Tile Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Draggable Marker
            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            const addressTextarea = document.getElementById('address');

            // Reverse Geocode function (Convert Lat/Lng -> Address string)
            function updateAddressFromLatLng(lat, lng) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            addressTextarea.value = data.display_name;
                        }
                    })
                    .catch(err => console.error('Geocoding error:', err));
            }

            // Trigger when marker drag ends
            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updateAddressFromLatLng(position.lat, position.lng);
            });

            // Trigger when clicking on the map
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateAddressFromLatLng(e.lat, e.lng);
            });

            // Map Search Functionality
            const searchInput = document.getElementById('mapSearchInput');
            const searchBtn = document.getElementById('btnSearchMap');

            function performSearch() {
                const query = searchInput.value.trim();
                if (!query) return;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            map.setView([lat, lon], 16);
                            marker.setLatLng([lat, lon]);
                            addressTextarea.value = data[0].display_name;
                        } else {
                            alert('Location not found. Try refining your query.');
                        }
                    })
                    .catch(err => console.error('Search error:', err));
            }

            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });

            // Geolocation Button ("Locate Me")
            document.getElementById('btnLocateMe').addEventListener('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateAddressFromLatLng(lat, lng);
                    }, function () {
                        alert('Could not detect location. Check browser permissions.');
                    });
                }
            });

        });
    </script>
</body>
</html>