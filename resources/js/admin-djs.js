console.log('admin-djs.js loaded');

const djsTableEl = $('#djs-table');
const djsRoute = typeof djsDataRoute !== 'undefined' ? djsDataRoute : '/admin/djs/list';

const djsTable = djsTableEl.DataTable({
    processing: true,
    serverSide: false,
    ajax: djsRoute,
    responsive: true,
    autoWidth: false,
    dom: "<'row mb-2'<'col-sm-6'l><'col-sm-6'f>>rt<'row mt-2'<'col-sm-6'i><'col-sm-6'p>>",
    columns: [
        { data: 'id', name: 'id', width: '8%', className: 'text-start' },
        { data: 'name', name: 'name', width: '48%' },
        { data: 'slot', name: 'slot', width: '24%', className: 'text-center' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '20%', className: 'text-end' },
    ],
    order: [[1, 'asc']],
});

// initialize tooltips on init and draw
djsTable.on('init.dt draw.dt', function() {
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            try { new bootstrap.Tooltip(el); } catch (e) { }
        });
    }
    // show/hide empty state
    try {
        const emptyEl = document.getElementById('djs-empty');
        const tableResp = document.querySelector('#djs-table')?.closest('.table-responsive');
        if (emptyEl) {
            if (djsTable.rows().count() === 0) {
                emptyEl.style.display = 'block';
                if (tableResp) tableResp.style.display = 'none';
            } else {
                emptyEl.style.display = 'none';
                if (tableResp) tableResp.style.display = '';
            }
        }
    } catch (e) { /* ignore */ }
});

// also respond to xhr event when ajax loads
djsTable.on('xhr.dt', function() {
    try {
        const emptyEl = document.getElementById('djs-empty');
        const tableResp = document.querySelector('#djs-table')?.closest('.table-responsive');
        if (emptyEl) {
            if (djsTable.rows().count() === 0) {
                emptyEl.style.display = 'block';
                if (tableResp) tableResp.style.display = 'none';
            } else {
                emptyEl.style.display = 'none';
                if (tableResp) tableResp.style.display = '';
            }
        }
    } catch (e) { /* ignore */ }
});

// delete flow for DJs (uses a local confirm modal similar to dates page)
(function(){
    // create a small modal in DOM if not present
    if (!document.getElementById('confirmDeleteModalDJ')) {
        const modalHtml = `
        <div class="modal fade" id="confirmDeleteModalDJ" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete DJ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmDeleteMessageDJ">Are you sure you want to delete this DJ? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtnDJ">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    let deleteForm = null;
    let deleteRowNode = null;

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-delete-dj');
        if (!btn) return;
        e.preventDefault();

        deleteForm = btn.closest('form');
        deleteRowNode = btn.closest('tr');

        const nameText = deleteRowNode ? (deleteRowNode.querySelector('td:nth-child(2)')?.innerText || '') : '';
        const msg = document.getElementById('confirmDeleteMessageDJ');
        if (msg && nameText) msg.textContent = `Delete "${nameText.trim()}" — this action cannot be undone.`;

        const modalEl = document.getElementById('confirmDeleteModalDJ');
        if (modalEl && typeof bootstrap !== 'undefined') { new bootstrap.Modal(modalEl).show(); }
    });

    const confirmBtn = document.getElementById('confirmDeleteBtnDJ');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function(){
            if (!deleteForm) return;
            const action = deleteForm.getAttribute('action');
            const method = deleteForm.getAttribute('method') || 'POST';
            const fd = new FormData(deleteForm);

            fetch(action, {
                method: method.toUpperCase(),
                body: fd,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(json => {
                const modalEl = document.getElementById('confirmDeleteModalDJ');
                if (modalEl && typeof bootstrap !== 'undefined') try { bootstrap.Modal.getInstance(modalEl).hide(); } catch(e){}

                if (json && json.success) {
                    try { djsTable.row(deleteRowNode).remove().draw(false); } catch(e){ if (deleteRowNode) deleteRowNode.remove(); }
                    if (window.adminToaster) window.adminToaster.show('success', json.message || 'DJ deleted');
                } else {
                    if (window.adminToaster) window.adminToaster.show('danger', json?.message || 'Failed to delete');
                }
            }).catch(err => { if (window.adminToaster) window.adminToaster.show('danger', 'Failed to delete'); });
        });
    }
})();
