<?php
// 1. Dependencies and Controller Initialization
include(__DIR__ . '/../../config/dbConnect.php');
require_once __DIR__ . '/../../controllers/client/profileController.php';

// Define BASE_URL fallback
if (!defined('BASE_URL')) {
    define('BASE_URL', '/tree');
}

// 2. Instantiate Controller & Process Requests
$controller = new ProfileController($conn ?? null);
$controller->handleRequest();

// 3. Extract Data for View Presentation
$userData     = $controller->getUserData();
$flashMessage = $controller->getFlashMessage();
$flashType    = $controller->getFlashType();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree - Client Profile</title>
    
    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet JS Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/images/TREE.PNG">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/client.css">

    <style>
        #addressMap {
            height: 250px;
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            z-index: 1;
        }
        .map-instruction {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
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

    <!-- Sidebar / Navbar -->
    <?php include(__DIR__ . '/../../includes/navbarClient.php'); ?>

    <!-- Main Container -->
    <main class="main-container">
        
        <!-- Toast Feedback -->
        <?php if (!empty($flashMessage)): ?>
            <div class="toast-notification toast-<?php echo htmlspecialchars($flashType); ?>">
                <i class="fa-solid <?php echo $flashType === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>"></i>
                <span><?php echo htmlspecialchars($flashMessage); ?></span>
            </div>
        <?php endif; ?>

        <div class="profile-wrapper">
            
            <!-- Left Sidebar Profile Card -->
            <aside class="profile-card">
                <div class="avatar-container">
                    <img id="avatarPreview" src="https://ui-avatars.com/api/?name=<?php echo urlencode($userData['RealName']); ?>&background=10b981&color=fff&size=128" alt="Avatar" class="avatar-img">
                    <label for="avatarUpload" class="avatar-edit-btn" title="Change Avatar">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="avatarUpload" style="display:none;" accept="image/*" onchange="previewImage(event)">
                </div>

                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($userData['RealName']); ?></h3>
                    <p>@<?php echo htmlspecialchars($userData['Username']); ?></p>
                    
                    <div class="client-badge-tag">
                        <i class="fa-solid fa-shield-halved"></i> Verified Sponsor
                    </div>

                    <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid rgba(255,255,255,0.08);">

                    <div style="text-align: left; font-size: 0.85rem; color: #94a3b8; display: flex; flex-direction: column; gap: 0.6rem;">
                        <div><i class="fa-regular fa-envelope" style="width: 20px; color: #10b981;"></i> <?php echo htmlspecialchars($userData['Email']); ?></div>
                        <div><i class="fa-regular fa-calendar" style="width: 20px; color: #10b981;"></i> Member since <?php echo htmlspecialchars($userData['MemberSince']); ?></div>
                    </div>
                </div>
            </aside>

            <!-- Right Main Interactive Section -->
            <section class="profile-main-card">
                
                <!-- Navigation Tabs -->
                <div class="profile-tabs">
                    <button type="button" class="tab-btn active" onclick="switchTab(event, 'detailsTab')">
                        <i class="fa-solid fa-user-gear"></i> Details
                    </button>
                    <button type="button" class="tab-btn" onclick="switchTab(event, 'securityTab')">
                        <i class="fa-solid fa-lock"></i> Security
                    </button>
                    <button type="button" class="tab-btn" onclick="switchTab(event, 'activityTab')">
                        <i class="fa-solid fa-clock-rotate-left"></i> Activity
                    </button>
                </div>

                <!-- Tab 1: Profile Details -->
                <div id="detailsTab" class="tab-content active">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="latitude" id="latitudeInput" value="<?php echo htmlspecialchars($userData['Latitude']); ?>">
                        <input type="hidden" name="longitude" id="longitudeInput" value="<?php echo htmlspecialchars($userData['Longitude']); ?>">

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="real_name" class="form-control" value="<?php echo htmlspecialchars($userData['RealName']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Username (System ID)</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['Username']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($userData['Email']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($userData['Phone']); ?>" placeholder="+60 1X-XXXXXXX">
                            </div>
                            <div class="form-group full-width">
                                <label>Billing / Delivery Address Pin</label>
                                <textarea name="address" id="addressInput" class="form-control" rows="2" placeholder="Click on the map or type address..."><?php echo htmlspecialchars($userData['Address']); ?></textarea>
                                
                                <div class="map-instruction">
                                    <i class="fa-solid fa-location-dot" style="color: #10b981;"></i> 
                                    Click or drag the pin on the map to automatically set your exact location address.
                                </div>
                                <div id="addressMap"></div>
                            </div>
                        </div>
                        <button type="submit" class="btn-save" style="margin-top: 1rem;">
                            <i class="fa-solid fa-floppy-disk"></i> Save Changes
                        </button>
                    </form>
                </div>

                <!-- Tab 2: Security & Passwords -->
                <div id="securityTab" class="tab-content">
                    <form method="POST" action="" onsubmit="return validatePasswordMatch()">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" id="newPwd" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" id="confirmPwd" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <small id="pwdMatchMsg" style="display:block; margin-top:0.5rem; font-size:0.82rem;"></small>
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-key"></i> Update Password
                        </button>
                    </form>
                </div>

                <!-- Tab 3: Recent Activity Log -->
                <div id="activityTab" class="tab-content">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fa-solid fa-right-to-bracket"></i></div>
                            <div>
                                <strong style="color:#f8fafc; font-size:0.88rem;">Logged into Client Portal</strong>
                                <p style="margin:2px 0 0 0; color:#94a3b8; font-size:0.78rem;">Today at <?php echo date('H:i A'); ?></p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fa-solid fa-tree"></i></div>
                            <div>
                                <strong style="color:#f8fafc; font-size:0.88rem;">Inspected Profiled Trees</strong>
                                <p style="margin:2px 0 0 0; color:#94a3b8; font-size:0.78rem;">Interactive Leaflet Map session active</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fa-solid fa-shield-check"></i></div>
                            <div>
                                <strong style="color:#f8fafc; font-size:0.88rem;">Account Status Verified</strong>
                                <p style="margin:2px 0 0 0; color:#94a3b8; font-size:0.78rem;">Sponsorship profile synchronized</p>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </main>

    <!-- Footer -->
    <?php include(__DIR__ . '/../../includes/footerClient.php'); ?>

    <!-- Leaflet JS Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Interactive Logic Script -->
    <script>
        let map, marker;
        const initialLat = <?php echo json_encode((float)$userData['Latitude']); ?>;
        const initialLng = <?php echo json_encode((float)$userData['Longitude']); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Leaflet Map
            map = L.map('addressMap').setView([initialLat, initialLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Draggable pin marker
            marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
        });

        // Update hidden inputs
        function updateCoordinates(lat, lng) {
            document.getElementById('latitudeInput').value = lat.toFixed(6);
            document.getElementById('longitudeInput').value = lng.toFixed(6);
        }

        // Reverse Geocoding using OpenStreetMap Nominatim
        function reverseGeocode(lat, lng) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('addressInput').value = data.display_name;
                    }
                })
                .catch(err => console.error("Geocoding failed:", err));
        }

        // Tab Switcher with Leaflet Map Relayout Fix
        function switchTab(evt, tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            evt.currentTarget.classList.add('active');

            if (tabId === 'detailsTab' && map) {
                setTimeout(() => { map.invalidateSize(); }, 200);
            }
        }

        // Live Avatar Image Preview
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('avatarPreview');
                output.src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // Password matching JS check
        const newPwd = document.getElementById('newPwd');
        const confirmPwd = document.getElementById('confirmPwd');
        const pwdMatchMsg = document.getElementById('pwdMatchMsg');

        function checkPwdMatch() {
            if (!confirmPwd.value) {
                pwdMatchMsg.innerText = '';
                return;
            }
            if (newPwd.value === confirmPwd.value) {
                pwdMatchMsg.style.color = '#34d399';
                pwdMatchMsg.innerText = '✓ Passwords match';
            } else {
                pwdMatchMsg.style.color = '#f87171';
                pwdMatchMsg.innerText = '✕ Passwords do not match';
            }
        }

        if(newPwd && confirmPwd) {
            newPwd.addEventListener('keyup', checkPwdMatch);
            confirmPwd.addEventListener('keyup', checkPwdMatch);
        }

        function validatePasswordMatch() {
            return newPwd.value === confirmPwd.value;
        }
    </script>
</body>
</html>