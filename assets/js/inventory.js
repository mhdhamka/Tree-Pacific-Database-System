document.addEventListener("DOMContentLoaded", function () {
    const mapElement = document.getElementById('treeMap');
    if (!mapElement) return;

    // 1. Read and parse the PHP JSON data from the data attribute
    const rawData = mapElement.getAttribute('data-trees');
    const treeData = rawData ? JSON.parse(rawData) : [];

    // 2. Default center fallback (Sarawak, Malaysia)
    const defaultLat = 1.56557;
    const defaultLng = 110.347;

    const map = L.map('treeMap').setView([defaultLat, defaultLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // 3. Status icon builder
    function createStatusIcon(status) {
        let color = '#2e7d32'; // Green (Healthy)
        const statusLower = (status || '').toLowerCase();

        if (statusLower.includes('critical') || statusLower.includes('diseased') || statusLower.includes('dead')) {
            color = '#d32f2f'; // Red
        } else if (statusLower.includes('attention') || statusLower.includes('warning') || statusLower.includes('damaged')) {
            color = '#f57c00'; // Orange
        } else if (statusLower.includes('fair') || statusLower.includes('moderate')) {
            color = '#fbc02d'; // Yellow
        }

        const svgPin = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="30" height="42">
                <path fill="${color}" stroke="#ffffff" stroke-width="1.5" 
                      d="M12 0C5.37 0 0 5.37 0 12c0 9 12 24 12 24s12-15 12-24c0-6.63-5.37-12-12-12z"/>
                <circle cx="12" cy="12" r="5" fill="#ffffff"/>
            </svg>`;

        return L.divIcon({
            className: 'custom-tree-pin',
            html: svgPin,
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor: [0, -38]
        });
    }

    // 4. Plot tree markers
    const bounds = [];

    if (treeData && treeData.length > 0) {
        treeData.forEach(tree => {
            const lat = parseFloat(tree.lat);
            const lng = parseFloat(tree.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
                bounds.push([lat, lng]);

                const markerIcon = createStatusIcon(tree.status);
                const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);

                marker.bindPopup(`
                    <div style="font-family: sans-serif; padding: 4px; min-width: 160px;">
                        <strong style="color: #2e7d32; font-size: 14px;">Tree #${tree.id}</strong><br>
                        景色 <em>${tree.species}</em><br><br>
                        <strong>Block:</strong> Block ${tree.block}<br>
                        <strong>Status:</strong> <span>${tree.status}</span><br>
                        <strong>Price:</strong> ${tree.price}<br>
                        <strong>GPS:</strong> ${lat}, ${lng}<br><br>
                        <a href="../../staff/processes/updateTree.php?updateID=${tree.id}" 
                           style="display: inline-block; padding: 4px 10px; background: #2e7d32; color: #fff; text-decoration: none; border-radius: 4px; font-size: 12px;">
                           Edit Tree
                        </a>
                    </div>
                `);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    }
});