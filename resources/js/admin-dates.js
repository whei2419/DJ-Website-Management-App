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

// Additional admin-dates.js functionality can be added here

