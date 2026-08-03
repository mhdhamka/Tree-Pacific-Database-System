// Toggle individual purchase card accordion
function toggleAccordion(cardId) {
    const card = document.getElementById(cardId);
    if (!card) return;
    const body = card.querySelector('.purchase-body');
    
    if (card.classList.contains('active')) {
        card.classList.remove('active');
        if (body) body.style.display = 'none';
    } else {
        card.classList.add('active');
        if (body) body.style.display = 'block';
    }
}

// Expand all accordions
function expandAll() {
    document.querySelectorAll('.purchase-card').forEach(card => {
        card.classList.add('active');
        const body = card.querySelector('.purchase-body');
        if (body) body.style.display = 'block';
    });
}

// Collapse all accordions
function collapseAll() {
    document.querySelectorAll('.purchase-card').forEach(card => {
        card.classList.remove('active');
        const body = card.querySelector('.purchase-body');
        if (body) body.style.display = 'none';
    });
}

// Real-time client-side search filtering
function filterPurchases() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const query = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll('.purchase-card');

    cards.forEach(card => {
        const searchData = card.getAttribute('data-search') || '';
        const text = (searchData + ' ' + card.innerText).toLowerCase();
        
        if (text.includes(query)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Open Tree Modal
function openModal(tree) {
    // Populate Headers & Meta
    const speciesEl = document.getElementById('modalSpecies');
    const metaEl = document.getElementById('modalMeta');
    
    if (speciesEl) speciesEl.innerText = tree.SpeciesName || 'Tree Profile';
    if (metaEl) metaEl.innerText = `Tree ID: ${tree.TreeID || '-'} | Timber Grade: ${tree.timber_grade || '-'}`;

    // Fix Floating Point Numbers (e.g. 11.30000019 -> 11.30m)
    const heightEl = document.getElementById('modalHeight');
    const diameterEl = document.getElementById('modalDiameter');

    if (heightEl) {
        const parsedHeight = parseFloat(tree.TreeHeight);
        heightEl.innerText = !isNaN(parsedHeight) ? parsedHeight.toFixed(2) + 'm' : '-';
    }

    if (diameterEl) {
        const parsedDiameter = parseFloat(tree.TreeDiameter);
        diameterEl.innerText = !isNaN(parsedDiameter) ? parsedDiameter.toFixed(2) + 'cm' : '-';
    }

    // Health Status
    const statusEl = document.getElementById('modalStatus');
    if (statusEl) {
        if (tree.TreeStatus == 1) {
            statusEl.innerText = 'Healthy';
            statusEl.style.color = '#4caf50';
        } else {
            statusEl.innerText = 'Needs Attention';
            statusEl.style.color = '#f59e0b';
        }
    }

    // Image Setup
    const imgEl = document.getElementById('modalTreeImg');
    if (imgEl) {
        const fallbackImg = (window.BASE_URL ? window.BASE_URL : '') + '/assets/images/TREE.PNG';
        imgEl.src = tree.TreeImageDataUri || fallbackImg;
    }

    // Display Modal
    const modal = document.getElementById('treeModal');
    if (modal) modal.style.display = 'flex';
}

// Close Modal
function closeModal() {
    const modal = document.getElementById('treeModal');
    if (modal) modal.style.display = 'none';
}