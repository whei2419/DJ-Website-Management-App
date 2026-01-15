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
        { data: 'id', name: 'id', width: '8%' },
        { data: 'date', name: 'date', width: '72%' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '20%' },
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

