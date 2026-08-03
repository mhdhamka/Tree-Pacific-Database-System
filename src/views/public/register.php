<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Register</title>
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <link rel="stylesheet" href="../../../assets/css/loginClient.css">

    <style>
        /* Map Container Styling */
        #map {
            width: 100%;
            height: 220px;
            border-radius: 8px;
            border: 1px solid var(--border, #E2E8F0);
            margin-top: 0.5rem;
            z-index: 1;
        }

        /* Phone Validation Helper Styling */
        .phone-feedback {
            font-size: 0.75rem;
            display: block;
            margin-top: 0.35rem;
            font-weight: 500;
        }
        .phone-valid {
            color: #10B981;
        }
        .phone-invalid {
            color: #EF4444;
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <header class="navbar">
        <div class="brand">
            <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1>Pacific<span>Tree</span></h1>
        </div>
        <div class="nav-badge">
            Client Registration
        </div>
    </header>

    <!-- Main Content Center Layout -->
    <main class="main-wrapper">
        
        <div class="login-card">
            
            <!-- Left Side Hero Banner -->
            <section class="card-hero">
                <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
                <h2>PacificTree</h2>
                <p>Enterprise GIS & Forestry Operations Platform</p>
            </section>

            <!-- Right Side Form Card -->
            <section class="card-form">
                <div class="form-header">
                    <h3><span>Client</span> | Sign Up</h3>
                    <p>Create an account to access tree management services.</p>
                </div>

                <form method="POST" action="../../controllers/auth/authController.php" onsubmit="return validateForm()">
                    <!-- Action Discriminator -->
                    <input type="hidden" name="action" value="register_client">

                    <!-- 1. Personal Information -->
                    <div class="form-group">
                        <label for="realName">Real Name</label>
                        <input type="text" placeholder="Full Name" id="realName" name="realName" required>
                    </div>

                    <!-- 2. Account Credentials -->
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" placeholder="Enter Username" id="username" name="username" required>
                    </div>

					<div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" placeholder="+60 1X-XXX XXXX" id="phone" name="phone" required>
                        <span id="phone-status" class="phone-feedback"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" placeholder="name@example.com" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" placeholder="Enter Password" id="password" name="password" required>
                    </div>

                    <!-- 3. Location Details -->
                    <div class="form-group">
                        <label><i class="fa-solid fa-location-dot"></i> Select Location on Map</label>
                        <div id="map"></div>
                        <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.35rem;">
                            Click on the map or drag the pin to select your address location.
                        </small>
                    </div>

                    <!-- Hidden Lat/Lng fields for database storage -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" placeholder="Street Address" id="address" name="address" required>
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" placeholder="Country" id="country" name="country" required>
                    </div>

                    <!-- 4. Form Actions -->
                    <button type="submit" name="submit" class="btn-submit">
                        <i class="fa-solid fa-user-plus"></i> REGISTER
                    </button>

                    <a href="OptionLogin.php" class="btn-switch">
                        <i class="fa-solid fa-arrows-rotate"></i> Change User Type
                    </a>

                </form>

                <div class="form-footer">
                    <p>Already have an account? 
                        <a href="LoginClient.php">Login</a>
                    </p>
                </div>
            </section>

        </div>

    </main>

    <!-- Footer -->
    <?php include(__DIR__ . '/../../includes/footerClient.php'); ?>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // ==========================================
            // 1. INTERACTIVE PHONE NUMBER FORMATTING
            // ==========================================
            const phoneInput = document.getElementById('phone');
            const phoneStatus = document.getElementById('phone-status');

            if (phoneInput) {
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
            }

            // ==========================================
            // 2. LEAFLET MAP INITIALIZATION
            // ==========================================
            const defaultLat = 1.5535;
            const defaultLng = 110.3593;

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function updatePosition(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);

                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.address) {
                            if (data.address.country) {
                                document.getElementById('country').value = data.address.country;
                            }
                            const fullAddr = data.display_name || '';
                            if (fullAddr) {
                                document.getElementById('address').value = fullAddr;
                            }
                        }
                    })
                    .catch(err => console.log('Geocoding error:', err));
            }

            map.on('click', function (e) {
                const { lat, lng } = e.latlng;
                marker.setLatLng([lat, lng]);
                updatePosition(lat, lng);
            });

            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updatePosition(position.lat, position.lng);
            });

            updatePosition(defaultLat, defaultLng);
        });

        function validateForm() {
            return true;
        }
    </script>
</body>
</html>