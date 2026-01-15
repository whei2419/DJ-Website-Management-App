console.log('admin-dates.js loaded');

const table = $('#dates-table');

// Use the route passed from Blade
const route = typeof dataTablesRoute !== 'undefined' ? dataTablesRoute : '/admin/dates/list';

console.log('Initializing DataTable with route:', route);

table.DataTable({
    processing: true,
    serverSide: false,
    ajax: route,
    responsive: true,
    autoWidth: false,
    dom: "<'row mb-2'<'col-sm-6'l><'col-sm-6'f>>rt<'row mt-2'<'col-sm-6'i><'col-sm-6'p>>",
    columns: [
        { data: 'id', name: 'id', width: '8%', className: 'text-start' },
        { data: 'date', name: 'date', width: '72%', render: function(data, type, row){
            if (type === 'display') return '<span class="date-pill">'+data+'</span>';
            return data;
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '20%', className: 'text-end' },
    ],
    order: [[1, 'asc']], // Default sort by date ascending (column index 1)
});

// Example: Show a toast notification when the table is loaded
table.on('init.dt', function() {
    if (window.adminToaster) {
        window.adminToaster.show('success', 'Dates table loaded successfully!');
    }
    // Initialize Bootstrap tooltips for action icons
    const initTooltips = () => {
        if (typeof bootstrap !== 'undefined' && document.querySelectorAll) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                try { new bootstrap.Tooltip(el); } catch (e) { /* ignore */ }
            });
        }
    };

    initTooltips();
    // Insert icons into table header (avoid duplicates)
    const thIcons = [
        { idx: 0, html: '<i class="fas fa-hashtag me-2 text-muted"></i>' },
        { idx: 1, html: '<i class="fas fa-calendar-alt me-2 text-muted"></i>' },
        { idx: 2, html: '<i class="fas fa-ellipsis-v me-2 text-muted"></i>' }
    ];
    document.querySelectorAll('#dates-table thead th').forEach((th, i) => {
        if (!th.querySelector('i')) {
            const icon = thIcons.find(x => x.idx === i);
            if (icon) th.innerHTML = icon.html + th.innerHTML.trim();
        }
    });

    // Modernize length select and search input
    const lengthLabel = document.querySelector('.dataTables_length label');
    if (lengthLabel && !lengthLabel.querySelector('.length-icon')) {
        const span = document.createElement('span');
        span.className = 'length-icon';
        span.innerHTML = '<i class="fas fa-list-ol text-muted"></i>';
        lengthLabel.insertBefore(span, lengthLabel.firstChild);
    }

    const filterLabel = document.querySelector('.dataTables_filter label');
    if (filterLabel && !filterLabel.querySelector('.filter-icon')) {
        const span = document.createElement('span');
        span.className = 'filter-icon';
        span.innerHTML = '<i class="fas fa-search text-muted"></i>';
        filterLabel.insertBefore(span, filterLabel.firstChild);
    }

    // Replace default DataTables filter with a Tabler-style search input
    if (filterLabel) {
        // avoid recreating if already replaced
        if (!filterLabel.querySelector('input.tabler-input')) {
            // clear existing contents
            filterLabel.innerHTML = '';

            const wrapper = document.createElement('div');
            wrapper.className = 'tabler-search';

            const icon = document.createElement('span');
            icon.className = 'search-icon';
            icon.innerHTML = '<i class="fas fa-search"></i>';

            const input = document.createElement('input');
            input.type = 'search';
            input.className = 'tabler-input form-control';
            input.placeholder = 'Search dates...';
            input.value = table.search() || '';

            // bind input to DataTables search
            let searchTimeout = null;
            input.addEventListener('input', function(e){
                const val = this.value;
                // debounce to avoid excessive draws
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    table.search(val).draw();
                }, 200);
            });

            wrapper.appendChild(icon);
            wrapper.appendChild(input);
            filterLabel.appendChild(wrapper);
        }
    }
});

// Re-init tooltips after each draw (for AJAX/DOM updates)
table.on('draw.dt', function() {
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            try { new bootstrap.Tooltip(el); } catch (e) { /* ignore */ }
        });
    }
});

// Delete flow: show confirmation modal, submit AJAX, remove row and show toast
(function(){
    let deleteForm = null;
    let deleteRowNode = null;

    // Delegate click handler for delete buttons
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-delete-date');
        if (!btn) return;
        e.preventDefault();

        // find the closest form and table row
        const form = btn.closest('form');
        const row = btn.closest('tr');

        deleteForm = form;
        deleteRowNode = row;

        // populate modal message with optional date text
        const dateText = row ? (row.querySelector('td:nth-child(2)')?.innerText || '') : '';
        const msg = document.getElementById('confirmDeleteMessage');
        if (msg && dateText) {
            msg.textContent = `Delete "${dateText.trim()}" — this action cannot be undone.`;
        }

        // show modal
        const modalEl = document.getElementById('confirmDeleteModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    // Confirm delete button
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function(){
            if (!deleteForm) return;

            // submit via AJAX using FormData (includes _token and _method)
            const action = deleteForm.getAttribute('action');
            const method = deleteForm.getAttribute('method') || 'POST';
            const fd = new FormData(deleteForm);

            fetch(action, {
                method: method.toUpperCase(),
                body: fd,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(r => r.json()).then(json => {
                // close modal
                const modalEl = document.getElementById('confirmDeleteModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    try { bootstrap.Modal.getInstance(modalEl).hide(); } catch (e) { /* ignore */ }
                }

                if (json && json.success) {
                    // remove row from DataTable if present
                    try {
                        const dtRow = table.row(deleteRowNode);
                        dtRow.remove().draw(false);
                    } catch (e) {
                        // fallback: remove DOM row
                        if (deleteRowNode && deleteRowNode.parentNode) deleteRowNode.parentNode.removeChild(deleteRowNode);
                    }

                    if (window.adminToaster) window.adminToaster.show('success', json.message || 'Date deleted');
                } else {
                    if (window.adminToaster) window.adminToaster.show('danger', (json && json.message) || 'Failed to delete');
                }
            }).catch(err => {
                if (window.adminToaster) window.adminToaster.show('danger', 'Failed to delete');
            }).finally(() => {
                deleteForm = null; deleteRowNode = null;
            });
        });
    }
})();

// Add date via AJAX (prevent full page reload)
(function(){
    const addForm = document.getElementById('addDateForm');
    if (!addForm) return;

    addForm.addEventListener('submit', function(e){
        e.preventDefault();

        const submitBtn = addForm.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        const action = addForm.getAttribute('action');
        const method = (addForm.getAttribute('method') || 'POST').toUpperCase();
        const fd = new FormData(addForm);

        fetch(action, {
            method: method,
            body: fd,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(async res => {
            const json = await res.json().catch(() => null);
            if (res.ok && json && json.success) {
                const d = json.date || json;
                const id = d.id;
                const dateText = d.date_formatted || d.date || d.date_formatted;

                // build actions HTML (client-side) using CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const actionsHtml = `
                    <form method="POST" action="/admin/dates/${id}" class="d-inline delete-date-form">
                        <input type="hidden" name="_token" value="${token}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-date" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                `;

                // add to DataTable
                try {
                    table.row.add({ id: id, date: dateText, actions: actionsHtml }).draw(false);
                } catch (e) {
                    // fallback: reload page
                    window.location.reload();
                }

                // close modal
                const modalEl = document.getElementById('addDateModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    try { bootstrap.Modal.getInstance(modalEl).hide(); } catch (e) { /* ignore */ }
                }

                // reset form
                addForm.reset();

                if (window.adminToaster) window.adminToaster.show('success', json.message || 'Date added');
            } else {
                // display errors
                if (json && json.errors) {
                    const messages = Object.values(json.errors).flat().join(' ');
                    if (window.adminToaster) window.adminToaster.show('danger', messages);
                } else {
                    if (window.adminToaster) window.adminToaster.show('danger', json?.message || 'Failed to create date');
                }
            }
        }).catch(err => {
            if (window.adminToaster) window.adminToaster.show('danger', 'Failed to create date');
        }).finally(() => {
            if (submitBtn) submitBtn.disabled = false;
        });
    });
})();

// Additional admin-dates.js functionality can be added here

