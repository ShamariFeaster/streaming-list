function filterTable() {
    let searchBox = document.getElementById('searchBox').value.toLowerCase();
    let table = document.getElementById('mainTable');
    let rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // skip header row
        let cells = rows[i].getElementsByTagName('td');
        let match = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j]) {
                let textValue = cells[j].textContent || cells[j].innerText;
                if (textValue.toLowerCase().indexOf(searchBox) > -1) {
                    match = true;
                    break;
                }
            }
        }
        rows[i].style.display = match ? '' : 'none';
    }
}

function applyFilter(platform = '', watched = '') {
    let table = document.getElementById('mainTable');
    let rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        let platformCell = rows[i].getElementsByTagName('td')[2];
        let watchedCell  = rows[i].getElementsByTagName('td')[3];

        if (platformCell && watchedCell) {
            let platformMatch = (platform === '' || platformCell.textContent === platform);
            let watchedMatch  = (watched  === '' || watchedCell.textContent === watched);

            rows[i].style.display = (platformMatch && watchedMatch) ? '' : 'none';
        }
    }
}

function resetFilters() {
    let table = document.getElementById('mainTable');
    let rows = table.getElementsByTagName('tr');
    document.getElementById('searchBox').value = '';

    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

function toggleChildren(parentId) {
    let children = document.getElementsByClassName('child-of-' + parentId);
    for (let i = 0; i < children.length; i++) {
        children[i].style.display = 
            (children[i].style.display === 'none' || children[i].style.display === '') 
            ? 'table-row' : 'none';
    }
}