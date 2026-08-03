/**
 * Tab Switcher Utility Function
 * @param {string} tabId - The DOM ID of the target tab content container.
 * @param {HTMLElement} btnElement - The clicked tab button element (`this`).
 */
function switchTab(tabId, btnElement) {
    if (!tabId || !btnElement) return;

    // 1. Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // 2. Remove active state from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // 3. Activate target tab content and clicked button
    const targetTab = document.getElementById(tabId);
    if (targetTab) {
        targetTab.classList.add('active');
        btnElement.classList.add('active');
    }
}