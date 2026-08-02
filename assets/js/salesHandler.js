/**
 * Filters the sales table based on input matching.
 */
function filterSalesTable() {
    const input = document.getElementById('salesSearchInput');
    if (!input) return;

    const filter = input.value.toLowerCase();
    const table = document.getElementById('salesTable');
    if (!table) return;

    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        // Ignore empty state row if present
        if (tr[i].id === 'noDataRow') continue;

        let rowVisible = false;
        const tdList = tr[i].getElementsByTagName('td');

        // Search through all columns except the Actions column (last index)
        for (let j = 0; j < tdList.length - 1; j++) {
            if (tdList[j]) {
                const txtValue = tdList[j].textContent || tdList[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    rowVisible = true;
                    break;
                }
            }
        }
        tr[i].style.display = rowVisible ? '' : 'none';
    }
}

/**
 * Exports visible table data to a CSV file.
 */
function exportSalesToCSV() {
    const table = document.getElementById('salesTable');
    if (!table) {
        alert('Sales table not found.');
        return;
    }

    const rows = table.querySelectorAll('tr');
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        // Skip hidden rows from search filter or empty state row
        if (rows[i].style.display === 'none' || rows[i].id === 'noDataRow') continue;

        const row = [];
        const cols = rows[i].querySelectorAll('th, td');

        // Export all columns except Actions (last column)
        for (let j = 0; j < cols.length - 1; j++) {
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').trim();
            // Escape double quotes
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(','));
    }

    if (csv.length <= 1) {
        alert('No data available to export.');
        return;
    }

    // Create UTF-8 BOM so Excel opens special characters correctly
    const csvContent = '\uFEFF' + csv.join('\n');
    const csvFile = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    
    // Create download link
    const downloadLink = document.createElement('a');
    const fileName = 'Sales_And_Block_Assignments_' + new Date().toISOString().slice(0, 10) + '.csv';

    downloadLink.download = fileName;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';

    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}