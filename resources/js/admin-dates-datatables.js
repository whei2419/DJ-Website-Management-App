document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addDateForm');
    const dateInput = document.getElementById('date');
    const dateIdInput = document.getElementById('dateId');
    const saveDateButton = document.getElementById('saveDateButton');
    const dateTitle = document.getElementById('dateTitle');
    const addDateModal = document.getElementById('addDateModal');
    const deleteModal = document.getElementById('deleteDateModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    let datesTable = null;
    let dateToDelete = null;

    // Initialize DataTable
    function initDataTable() {
        datesTable = $('#datesTable').DataTable({
            dom: "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            ajax: {
                url: datesDataRoute,
                type: 'GET',
                data: function (d) {
                    return {
                        draw: d.draw,
                        page: (d.start / d.length) + 1,
                        per_page: d.length,
                        search: d.search.value
                    };
                }
            },
            columns: [
                { data: 'id', width: '60px' },
                {
                    data: 'date',
                    render: function (data) {
                        if (!data) return '';
                        return `<span class="d-inline-flex align-items-center border border-primary text-primary rounded-1 px-2 py-1" style="font-size:0.95rem;">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <span>${data}</span>
                        </span>`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                            <button class="btn btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="1" /><circle cx="12" cy="19" r="1" /><circle cx="12" cy="5" r="1" /></svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); window.editDate(${data});">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit dropdown-item-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    Edit
                                </a>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); window.deleteDate(${data});">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash dropdown-item-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    Delete
                                </a>
                            </div>
                        </div>`;
                    }
                }
            ],
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
            language: {
                emptyTable: '<div class="empty"><div class="empty-icon"><i class="fas fa-calendar-alt"></i></div><p class="empty-title">No dates found</p></div>',
                zeroRecords: '<div class="text-center py-3">No matching dates found</div>',
                processing: '<div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="text-muted">Loading dates...</span>',
                lengthMenu: '<span>Show</span> _MENU_ <span>entries</span>',
                search: '',
                searchPlaceholder: 'Search dates...',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total entries)',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Prev'
                }
            },
            order: [[0, 'desc']]
        });
    }

    // Initialize DataTable on load
    initDataTable();

    // Wire up custom search input
    const searchInput = document.getElementById('datesTableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            datesTable.search(this.value).draw();
        });
    }

    // Wire up custom length selector
    const lengthSelect = document.getElementById('datesTableLength');
    if (lengthSelect) {
        lengthSelect.addEventListener('change', function () {
            datesTable.page.len(parseInt(this.value)).draw();
        });
    }

    // Update modal for add/edit
    function updateModalInfo(isAdd, dateData = null) {
        if (isAdd) {
            dateTitle.innerHTML = "Add Date";
            dateIdInput.value = "";
            dateInput.value = "";
        } else {
            dateTitle.innerHTML = "Edit Date";
            dateIdInput.value = dateData.id;
            dateInput.value = dateData.date;
        }
    }

    // Modal show event
    addDateModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        updateModalInfo(true);
    });

    // Handle form submission
    saveDateButton.addEventListener('click', (e) => {
        e.preventDefault();
        saveOrUpdateDate();
    });

    // Save/update date via AJAX
    function saveOrUpdateDate() {
        const formData = new FormData();
        formData.append('date', dateInput.value);

        const dateId = dateIdInput.value;
        const url = dateId ? `/admin/dates/${dateId}` : saveDateRoute;
        const method = dateId ? 'PUT' : 'POST';

        if (dateId) {
            formData.append('_method', 'PUT');
        }

        // Validate
        if (!dateInput.value.trim()) {
            showFieldError(dateInput, 'Date is required');
            return;
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
            .then(async (response) => {
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    return { ok: response.ok, status: response.status, data };
                }
                const text = await response.text();
                throw new Error(`Non-JSON response (status ${response.status}): ${text}`);
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    showToast('Success!', 'Date saved successfully', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(addDateModal);
                    if (modal) modal.hide();

                    // Reset form
                    if (form) form.reset();
                    dateIdInput.value = "";

                    // Reload table
                    datesTable.ajax.reload();
                } else {
                    if (data.errors) {
                        displayErrors(data.errors);
                    } else if (data.message) {
                        showToast('Error', data.message, 'error');
                    } else {
                        showToast('Error', 'Failed to save date', 'error');
                    }
                }
            })
            .catch((error) => {
                console.error('Error saving date:', error);
                showToast('Error', 'An error occurred while saving the date', 'error');
            });
    }

    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        const errorEl = field.closest('.mb-3')?.querySelector('.error-message');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('text-danger');
        }
    }

    function displayErrors(errors) {
        Object.keys(errors).forEach((key) => {
            const errorMessage = errors[key][0];
            const field = document.getElementById(key);
            if (field) {
                showFieldError(field, errorMessage);
            }
        });
    }

    function showToast(title, message, type) {
        if (window.adminToaster && window.adminToaster.show) {
            window.adminToaster.show(type, message);
        }
    }

    // Edit date function
    window.editDate = function (id) {
        fetch(`/admin/dates/${id}/edit`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(response => response.json())
            .then(data => {
                if (data.date) {
                    updateModalInfo(false, { id: data.date.id, date: data.date.date });
                    const modal = new bootstrap.Modal(addDateModal);
                    modal.show();
                }
            })
            .catch(error => {
                console.error('Error fetching date:', error);
                showToast('Error', 'Failed to load date data', 'error');
            });
    };

    // Delete date function
    window.deleteDate = function (id) {
        dateToDelete = id;
        const modal = new bootstrap.Modal(deleteModal);
        modal.show();
    };

    // Handle delete confirmation
    confirmDeleteBtn.addEventListener('click', function () {
        if (!dateToDelete) return;

        fetch(`/admin/dates/${dateToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Success', 'Date deleted successfully', 'success');
                    datesTable.ajax.reload();

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(deleteModal);
                    if (modal) modal.hide();
                    dateToDelete = null;
                } else {
                    showToast('Error', data.message || 'Failed to delete date', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting date:', error);
                showToast('Error', 'Failed to delete date', 'error');
            });
    });

});
