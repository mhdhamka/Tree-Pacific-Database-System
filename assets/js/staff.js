/**
 * Toggles manager status via AJAX and updates UI badge dynamically.
 */
function toggleManagerStatus(checkbox, staffID) {
    const isManager = checkbox.checked ? 1 : 0;
    const badge = checkbox.closest('.role-toggle').querySelector('.role-badge');
    const roleText = badge.querySelector('.role-text');
    const roleIcon = badge.querySelector('i');

    fetch('../../controllers/staff/userController.php?action=toggleManager', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `staffID=${encodeURIComponent(staffID)}&isManager=${isManager}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isManager) {
                badge.classList.remove('badge-staff');
                badge.classList.add('badge-manager');
                roleIcon.className = 'fa-solid fa-user-tie';
                roleText.textContent = 'Manager';
            } else {
                badge.classList.remove('badge-manager');
                badge.classList.add('badge-staff');
                roleIcon.className = 'fa-solid fa-user';
                roleText.textContent = 'Staff';
            }
        } else {
            alert('Failed to update manager status.');
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status.');
        checkbox.checked = !checkbox.checked;
    });
}

/**
 * Calculates human-readable tenure from a date string.
 */
function calculateTenure(dateString) {
    if (!dateString) return "N/A";
    const start = new Date(dateString);
    const now = new Date();
    
    let years = now.getFullYear() - start.getFullYear();
    let months = now.getMonth() - start.getMonth();
    
    if (months < 0) {
        years--;
        months += 12;
    }
    
    let tenure = [];
    if (years > 0) tenure.push(`${years} yr${years > 1 ? 's' : ''}`);
    if (months > 0 || years === 0) tenure.push(`${months} mo${months !== 1 ? 's' : ''}`);
    
    return tenure.join(', ') + " at company";
}

/**
 * DOM Initialization for Inline Editing & Popover Insights
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // --- 1. Inline Editing Setup ---
    document.querySelectorAll(".editable-cell").forEach(cell => {
        const textSpan = cell.querySelector(".cell-value");
        const input = cell.querySelector(".cell-input");
        if (!textSpan || !input) return;

        // Open edit mode on click
        cell.addEventListener("click", function(e) {
            // Ignore if clicking on input itself or popover
            if (e.target === input || e.target.closest('.cell-popover')) return;
            
            textSpan.style.display = "none";
            input.style.display = "block";
            input.focus();
        });

        // Save logic
        const saveChange = () => {
            const staffId = cell.dataset.staffId;
            const field = cell.dataset.field;
            const newValue = input.value;

            fetch("../../controllers/staff/userController.php?action=updateField", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `staffID=${encodeURIComponent(staffId)}&field=${encodeURIComponent(field)}&value=${encodeURIComponent(newValue)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (field === "Salary") {
                        const numericVal = parseFloat(newValue) || 0;
                        textSpan.textContent = "RM " + numericVal.toFixed(2);
                        
                        // Update Salary Popover text dynamically
                        const popoverValue = cell.querySelector('.popover-annual');
                        if (popoverValue) {
                            popoverValue.textContent = "RM " + (numericVal * 12).toFixed(2);
                        }
                    } else if (field === "EmployDate") {
                        textSpan.textContent = newValue;
                        
                        // Update Tenure Popover text dynamically
                        const popoverTenure = cell.querySelector('.popover-tenure');
                        if (popoverTenure) {
                            popoverTenure.textContent = calculateTenure(newValue);
                        }
                    } else {
                        textSpan.textContent = newValue;
                    }
                } else {
                    alert("Failed to save change.");
                }
            })
            .catch(() => alert("Network error while saving."))
            .finally(() => {
                input.style.display = "none";
                textSpan.style.display = "inline";
            });
        };

        input.addEventListener("blur", saveChange);
        input.addEventListener("keydown", (e) => {
            if (e.key === "Enter") saveChange();
            if (e.key === "Escape") {
                input.style.display = "none";
                textSpan.style.display = "inline";
            }
        });
    });

    // --- 2. Dynamic Tenure Calculation for initial page load ---
    document.querySelectorAll(".popover-tenure").forEach(el => {
        const date = el.dataset.date;
        el.textContent = calculateTenure(date);
    });
});