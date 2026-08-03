<?php
include(__DIR__ . '/../../config/dbConnect.php');

global $conn;
$updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : 0;

// Fetch Target User Data to Pre-fill Form
$userQuery = "SELECT * FROM user WHERE UserID = $updateID LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$userData = ($userResult && mysqli_num_rows($userResult) > 0) ? mysqli_fetch_assoc($userResult) : [];

$realNameVal = $userData['RealName'] ?? '';
$usernameVal = $userData['Username'] ?? '';
$emailVal    = $userData['Email'] ?? '';
$phoneVal    = $userData['Phone'] ?? '';
$addressVal  = $userData['Address'] ?? '';
$passVal     = $userData['PasswordHash'] ?? '';
$userTypeVal = $userData['UserType'] ?? 'C';
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Update User</title>
    
    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <!-- Leaflet CSS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <link rel="stylesheet" href="../../../assets/css/staff.css">

    <style>
        /* Interactive Map & Helper Styling */
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
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Users & System Administration</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <ul class="breadcrumb">
                    <li><a href="../../views/staff/dashboard.php">Users</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></li>
                    <li class="active">Update User</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update User Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../../controllers/staff/userController.php?action=update">
                            <!-- Hidden input field to pass UserID to controller -->
                            <input type="hidden" name="userID" value="<?php echo $updateID; ?>">

                            <div class="form-group">
                                <label>User ID</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-id-badge"></i>
                                    <input type="text" class="form-control-input" value="#<?php echo htmlspecialchars($updateID); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="realname">Real Name</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-id-card"></i>
                                    <input type="text" class="form-control-input" name="realname" id="realname" value="<?php echo htmlspecialchars($realNameVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" class="form-control-input" name="username" id="username" value="<?php echo htmlspecialchars($usernameVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" class="form-control-input" name="email" id="email" value="<?php echo htmlspecialchars($emailVal); ?>" required>
                                </div>
                            </div>

                            <!-- Interactive Phone Field -->
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="tel" class="form-control-input" name="phone" id="phone" value="<?php echo htmlspecialchars($phoneVal); ?>" placeholder="+60 1X-XXX XXXX" autocomplete="off">
                                </div>
                                <span id="phone-status" class="phone-feedback"></span>
                            </div>

                            <!-- Interactive Address Field with Leaflet Map -->
                            <div class="form-group">
                                <label for="address">Address & Map Location</label>
                                
                                <!-- Map Search Controls -->
                                <div class="map-search-bar">
                                    <div class="input-wrapper" style="flex: 1;">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <input type="text" id="mapSearchInput" class="form-control-input" placeholder="Search address or location...">
                                    </div>
                                    <button type="button" class="btn btn-light" id="btnSearchMap" title="Search Map">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                    <button type="button" class="btn btn-light" id="btnLocateMe" title="Use My Current Location">
                                        <i class="fa-solid fa-crosshairs"></i>
                                    </button>
                                </div>

                                <!-- Leaflet Map Container -->
                                <div id="map"></div>

                                <!-- Textarea displaying/editing full address -->
                                <div class="input-wrapper" style="margin-top: 8px;">
                                    <textarea class="form-control-input" name="address" id="address" rows="3" placeholder="Selected full address will appear here..."><?php echo htmlspecialchars($addressVal); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password Hash</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" class="form-control-input" name="password" id="password" value="<?php echo htmlspecialchars($passVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>User Type</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="C" <?php echo ($userTypeVal === 'C') ? 'checked' : ''; ?>>
                                        Client
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="S" <?php echo ($userTypeVal === 'S') ? 'checked' : ''; ?>>
                                        Staff
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="updateUser" class="btn btn-primary">
                                    <i class="fa-solid fa-check"></i> Save Changes
                                </button>
                                <a href="../../views/staff/dashboard.php" class="btn btn-outline-danger">
                                    <i class="fa-solid fa-xmark"></i> Cancel
                                </a>
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

            function formatPhoneNumber(value) {
                let x = value.replace(/\D/g, ''); // Remove non-digits
                
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
                return formatted;
            }

            function validatePhone() {
                const rawDigits = phoneInput.value.replace(/\D/g, '');
                if (rawDigits.length >= 11 && rawDigits.length <= 12) {
                    phoneStatus.textContent = '✓ Valid Malaysian phone structure';
                    phoneStatus.className = 'phone-feedback phone-valid';
                } else if (rawDigits.length > 0) {
                    phoneStatus.textContent = '⚠ Incomplete mobile number';
                    phoneStatus.className = 'phone-feedback phone-invalid';
                } else {
                    phoneStatus.textContent = '';
                }
            }

            // Run validation once on page load for existing value
            validatePhone();

            phoneInput.addEventListener('input', function (e) {
                e.target.value = formatPhoneNumber(e.target.value);
                validatePhone();
            });


            // ==========================================
            // 2. LEAFLET MAP & REVERSE GEOCODING
            // ==========================================
            const addressTextarea = document.getElementById('address');

            // Default fallback center: Kuching, Sarawak
            let defaultLat = 1.5535;
            let defaultLng = 110.3593;

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            // Reverse Geocode (Lat/Lng -> Address string)
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

            // Auto-locate existing address on load if present
            if (addressTextarea.value.trim().length > 0) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(addressTextarea.value.trim())}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            map.setView([lat, lon], 16);
                            marker.setLatLng([lat, lon]);
                        }
                    })
                    .catch(err => console.error('Initial address lookup failed:', err));
            }

            // Marker Drag event
            marker.on('dragend', function () {
                const position = marker.getLatLng();
                updateAddressFromLatLng(position.lat, position.lng);
            });

            // Map Click event
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateAddressFromLatLng(e.lat, e.lng);
            });

            // Manual Map Search
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