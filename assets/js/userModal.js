/**
 * User Modal Controls & Leaflet Map Integration
 */

// Global Map State Variables
let clientMap, mapMarker;
let viewMap, viewMarker;

// Open Modal Handler
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }

    // Trigger map initialization when specific modals open
    if (modalId === 'addClientModal') {
        initClientMap();
    }
}

// Close Modal Handler
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking on the dark overlay background
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('user-modal')) {
        event.target.style.display = 'none';
    }
});


/* ==========================================================================
   1. Add Client Map (Interactive Location Selection)
   ========================================================================== */

function initClientMap() {
    // Default location: Kuching, Sarawak [lat, lng]
    const defaultLat = 1.5535;
    const defaultLng = 110.3593;

    if (!clientMap) {
        clientMap = L.map('clientMap').setView([defaultLat, defaultLng], 13);

        // OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(clientMap);

        // Draggable Marker
        mapMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(clientMap);

        // Trigger address lookup when marker drag ends or map is clicked
        mapMarker.on('dragend', function () {
            const position = mapMarker.getLatLng();
            fetchAddress(position.lat, position.lng);
        });

        clientMap.on('click', function (e) {
            mapMarker.setLatLng(e.latlng);
            fetchAddress(e.latlng.lat, e.latlng.lng);
        });

        // Fetch initial location
        fetchAddress(defaultLat, defaultLng);
    }

    // Fix map rendering bug when inside modals
    setTimeout(() => {
        clientMap.invalidateSize();
    }, 300);
}

// Reverse Geocoding using Nominatim (Free OpenStreetMap API)
function fetchAddress(lat, lng) {
    const addressInput = document.getElementById('clientAddress');
    const countryInput = document.getElementById('clientCountry');

    if (addressInput) addressInput.value = "Locating address...";
    if (countryInput) countryInput.value = "Locating country...";

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.address) {
                const addressParts = [
                    data.address.road || data.address.pedestrian || '',
                    data.address.suburb || data.address.neighbourhood || '',
                    data.address.city || data.address.town || data.address.county || ''
                ].filter(Boolean).join(', ');

                if (addressInput) addressInput.value = addressParts || data.display_name;
                if (countryInput) countryInput.value = data.address.country || '';
            } else {
                if (addressInput) addressInput.value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                if (countryInput) countryInput.value = 'Unknown';
            }
        })
        .catch(err => {
            console.error("Geocoding error:", err);
            if (addressInput) addressInput.value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            if (countryInput) countryInput.value = 'Error fetching country';
        });
}

/* ==========================================================================
   2. View Client Location Modal (Table Preview & Interactive Update)
   ========================================================================== */

let activeClientId = null; 

function openClientMapModal(cID, address, country) {
    activeClientId = cID;
    const fullQuery = `${address}, ${country}`;
    
    openModal('viewMapModal');
    
    // Update header text with an edit hint
    const addressTextElem = document.getElementById('viewMapAddressText');
    if (addressTextElem) {
        addressTextElem.innerHTML = `📍 <strong>Current:</strong> ${address}, ${country} <small style="color:#666; font-weight:normal;">(Click anywhere on the map to choose a new location)</small>`;
    }

    // Initialize map if not already created
    if (!viewMap) {
        viewMap = L.map('viewClientMap').setView([1.5535, 110.3593], 13); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(viewMap);

        // Map Click Event
        viewMap.on('click', function(e) {
            handleMapClick(e.latlng.lat, e.latlng.lng);
        });
    }

    setTimeout(() => {
        viewMap.invalidateSize();
    }, 300);

    // Geocode initial address
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullQuery)}&email=admin@example.com`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);

                viewMap.setView([lat, lon], 15);

                if (viewMarker) {
                    viewMarker.setLatLng([lat, lon]);
                } else {
                    viewMarker = L.marker([lat, lon]).addTo(viewMap);
                }
                
                // --- Initial Informational Popup ---
                const initialPopup = `
                    <div style="text-align: center; padding: 4px;">
                        <strong style="color: #007bff;"><i class="fa-solid fa-location-dot"></i> Current Registered Location</strong><br>
                        <span style="font-size:0.9em; display:block; margin: 4px 0;">${address}, ${country}</span>
                        <hr style="margin: 6px 0; border: 0; border-top: 1px solid #eee;">
                        <span style="font-size:0.8em; color: #666;">
                            💡 <i>Want to edit? Click anywhere else on the map to select a new address.</i>
                        </span>
                    </div>
                `;
                
                viewMarker.bindPopup(initialPopup).openPopup();
            } else {
                console.warn("Could not geocode address onto map.");
            }
        })
        .catch(err => console.error("Error geocoding location:", err));
}

/**
 * Handle user clicking a new spot on the map
 */
function handleMapClick(lat, lng) {
    // Temporary loading indicator in popup
    if (viewMarker) {
        viewMarker.setLatLng([lat, lng]);
    } else {
        viewMarker = L.marker([lat, lng]).addTo(viewMap);
    }
    viewMarker.bindPopup("<i>Fetching location address...</i>").openPopup();

    // Primary Reverse Geocoder (Nominatim with identification to prevent blocking)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&email=admin@example.com`, {
        headers: {
            'Accept-Language': 'en'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error("Nominatim request failed");
        return res.json();
    })
    .then(data => {
        if (!data || !data.address) throw new Error("No address returned from primary API");

        const newAddress = data.display_name.split(',').slice(0, 3).join(',').trim();
        const newCountry = data.address.country || '';

        showConfirmationPopup(newAddress, newCountry);
    })
    .catch(err => {
        console.warn("Primary geocoder failed, switching to backup...", err);
        
        // Backup API: BigDataCloud (Free, highly reliable, bypasses CORS/Rate limits)
        fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
            .then(res => res.json())
            .then(data => {
                const locality = data.locality || data.city || data.principalSubdivision || '';
                const country = data.countryName || '';
                const fullAddress = locality ? `${locality}, ${data.principalSubdivision || ''}`.trim() : country;

                if (!fullAddress) {
                    viewMarker.bindPopup("Unable to determine address for this spot.").openPopup();
                    return;
                }

                showConfirmationPopup(fullAddress, country);
            })
            .catch(fallbackErr => {
                console.error("All geocoding attempts failed:", fallbackErr);
                viewMarker.bindPopup("Failed to load address details. Please try another spot.").openPopup();
            });
    });
}

/**
 * Render the confirmation popup on the marker
 */
function showConfirmationPopup(newAddress, newCountry) {
    const popupContent = `
        <div style="text-align: center; padding: 4px;">
            <strong style="color:#28a745;"><i class="fa-solid fa-map-pin"></i> New Location Selected</strong><br>
            <span style="font-size:0.9em; display:block; margin: 6px 0;">${newAddress}, ${newCountry}</span>
            <button onclick="updateClientAddress('${activeClientId}', '${escapeQuotes(newAddress)}', '${escapeQuotes(newCountry)}')" 
                    style="background:#28a745; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:600; width: 100%;">
                <i class="fa-solid fa-check"></i> Set as New Address
            </button>
        </div>
    `;

    viewMarker.bindPopup(popupContent).openPopup();
}

/**
 * Send AJAX request to update client address in backend DB
 */
function updateClientAddress(clientId, newAddress, newCountry) {
    if (!confirm(`Confirm changing address to:\n"${newAddress}, ${newCountry}"?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'updateClientAddress');
    formData.append('cID', clientId);
    formData.append('address', newAddress);
    formData.append('country', newCountry);

    fetch('/tree/src/controllers/staff/userController.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Client address updated successfully!');
            location.reload(); 
        } else {
            alert('Error updating address: ' + (data.message || 'Server error.'));
        }
    })
    .catch(err => {
        console.error('Update request failed:', err);
        alert('An unexpected error occurred while updating.');
    });
}

// Utility function to escape quotes inside JS inline strings
function escapeQuotes(str) {
    return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}