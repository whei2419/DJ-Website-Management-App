document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addEditDJForm');
    const nameInput = document.getElementById('djName');
    const slotInput = document.getElementById('djSlot');
    const videoInput = document.getElementById('djVideo');
    const saveDJButton = document.getElementById('saveDJButton');
    const DjTitle = document.getElementById('DjTitle');
    const addOpen = document.getElementById('addEditDJModal');
    const saveUrl = saveDJRoute;
    const dateGrid = document.getElementById('dateGrid');
    
    let availableDates = [];
    let djTable = null;

    // Initialize DataTable
    function initDataTable() {
        djTable = $('#djsTable').DataTable({
            dom: "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            ajax: {
                url: djsDataRoute,
                type: 'GET',
                data: function(d) {
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
                    data: 'video_preview',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (data) {
                            const poster = row.poster ? `poster="${row.poster}"` : '';
                            const videoId = `video-${row.id}`;
                            return `<div style="position: relative; width: 100px; height: 75px; border-radius: 4px; overflow: hidden;">
                                <video id="${videoId}" 
                                       style="width: 100%; height: 100%; object-fit: cover;" 
                                       preload="auto"
                                       ${poster}
                                       autoplay
                                       muted 
                                       loop 
                                       playsinline>
                                    <source src="${data}" type="video/webm">
                                    <source src="${data}" type="video/mp4">
                                </video>
                            </div>`;
                        }
                        return '<span class="avatar avatar-sm" style="background-color: var(--tblr-muted-bg);"><i class="fas fa-video text-muted"></i></span>';
                    }
                },
                { 
                    data: 'name',
                    render: function(data, type, row) {
                        return `<div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name=${encodeURIComponent(data)}&background=random&size=128)"></span>
                            <div class="font-weight-medium">${data}</div>
                        </div>`;
                    }
                },
                { 
                    data: 'slot',
                    render: function(data) {
                        return `<div class="text-muted"><i class="fas fa-calendar-day me-1"></i>${data}</div>`;
                    }
                },
                { 
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<div class="dropdown">
                            <button class="btn btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="1" /><circle cx="12" cy="19" r="1" /><circle cx="12" cy="5" r="1" /></svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); window.editDJ(${data});">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit dropdown-item-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    Edit
                                </a>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); window.deleteDJ(${data});">
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
                emptyTable: '<div class="empty"><div class="empty-icon"><i class="fas fa-users"></i></div><p class="empty-title">No DJs found</p></div>',
                zeroRecords: '<div class="text-center py-3">No matching DJs found</div>',
                processing: '<div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="text-muted">Loading DJs...</span>',
                lengthMenu: '<span>Show</span> _MENU_ <span>entries</span>',
                search: '',
                searchPlaceholder: 'Search DJs...',
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
    const searchInput = document.getElementById('djsTableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            djTable.search(this.value).draw();
        });
    }

    // Wire up custom length selector
    const lengthSelect = document.getElementById('djsTableLength');
    if (lengthSelect) {
        lengthSelect.addEventListener('change', function() {
            djTable.page.len(parseInt(this.value)).draw();
        });
    }

    // Fetch available dates on page load
    fetchAvailableDates();
    
    function fetchAvailableDates() {
        fetch(availableDatesRoute, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            availableDates = data.data || [];
            renderDateGrid();
        })
        .catch(error => {
            console.error('Error fetching dates:', error);
            dateGrid.innerHTML = '<div class="text-danger text-center py-3">Failed to load dates</div>';
        });
    }

    function renderDateGrid() {
        dateGrid.innerHTML = availableDates.map(date => `
            <div class="date-card" data-date="${date.date}">
                <div class="date-card-date">${date.formatted_date}</div>
                ${date.event_name ? `<div class="date-card-event">${date.event_name}</div>` : ''}
                <div class="date-card-count">${date.dj_count} DJ${date.dj_count !== 1 ? 's' : ''}</div>
            </div>
        `).join('');

        // Add click handlers to date cards
        document.querySelectorAll('.date-card').forEach(card => {
            card.addEventListener('click', () => selectDate(card));
        });
    }

    function selectDate(card) {
        // Remove previous selection
        document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
        
        // Select new date
        card.classList.add('selected');
        slotInput.value = card.dataset.date;
        
        // Clear error if any
        slotInput.classList.remove('is-invalid');
        const errEl = getErrorEl(slotInput);
        if (errEl) errEl.textContent = '';
    }
    
    // on opening the modal, update the form action and inputs
    function updateModalInfo(isAdd, djData = null) {
        if (isAdd) {
            DjTitle.innerHTML = "Add DJ";
            // reset selections
            slotInput.value = "";
            document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
        } else {
            DjTitle.innerHTML = "Edit DJ";
            nameInput.value = djData.name;
            slotInput.value = djData.slot;
            // select the matching date card
            document.querySelectorAll('.date-card').forEach(card => {
                if (card.dataset.date === djData.slot) {
                    card.classList.add('selected');
                }
            });
            // video input left blank for security reasons
        }
    }

    addOpen.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const isAdd = true;
        updateModalInfo(isAdd);
    });

    // handle form submission
    saveDJButton.addEventListener('click', (e) => {
        e.preventDefault();
        saveOrUpdateDJ(true);
    });

    // save/update dj via AJAX
    function saveOrUpdateDJ(isAdd, djId = null) {
        // gather form data
        const formData = new FormData();
        formData.append('name', nameInput.value);
        // ensure date-only value (YYYY-MM-DD) is sent
        const slotValue = slotInput.value ? slotInput.value : '';
        formData.append('slot', slotValue);
        // append file object for video if present
        if (videoInput && videoInput.files && videoInput.files.length > 0) {
            formData.append('video', videoInput.files[0]);
        }

        // log form data for debugging
        console.log('Form Data:', {
            name: nameInput.value,
            slot: slotValue,
            video: (videoInput.files && videoInput.files.length > 0) ? videoInput.files[0].name : null
        });

        // validate form
        if (!validateForm()) {
            return;
        }

        // submit via fetch; request JSON to ensure Laravel returns JSON for validation/errors
        fetch(saveUrl, {
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
            // not JSON: capture text for debugging (likely an HTML error/redirect)
            const text = await response.text();
            throw new Error(`Non-JSON response (status ${response.status}): ${text}`);
        })
        .then(({ ok, data }) => {
            if (ok && data.success) {
                console.log('DJ saved successfully:', data.dj);
                
                // Show success toast
                showToast('Success!', 'DJ saved successfully', 'success');
                
                // Close the modal
                try {
                    const modal = bootstrap.Modal.getInstance(addOpen);
                    if (modal) {
                        modal.hide();
                    } else {
                        addOpen.querySelector('[data-bs-dismiss="modal"]')?.click();
                    }
                } catch (e) {
                    console.error('Error closing modal:', e);
                }
                
                // Reset form
                if (form) form.reset();
                slotInput.value = "";
                document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
                
                // Reload both the DJ list and available dates
                djTable.ajax.reload();
                fetchAvailableDates();
            } else {
                // validation errors or other errors
                if (data.errors) {
                    displayErrors(data.errors);
                } else if (data.message) {
                    showToast('Error', data.message, 'error');
                } else {
                    showToast('Error', 'Failed to save DJ', 'error');
                }
            }
        })
        .catch((error) => {
            console.error('Error saving DJ:', error);
            showToast('Error', 'An error occurred while saving the DJ', 'error');
        });
    }

    function validateForm() {
        let isValid = true;
        // clear all previous errors
        clearErrors();

        if (!nameInput.value.trim()) {
            showFieldError(nameInput, 'Name is required');
            isValid = false;
        }
        if (!slotInput.value.trim()) {
            showFieldError(slotInput, 'Please select a time slot');
            isValid = false;
        }
        if (!videoInput.files || videoInput.files.length === 0) {
            showFieldError(videoInput, 'Video is required');
            isValid = false;
        }

        return isValid;
    }

    function clearErrors() {
        document.querySelectorAll('.form-control, .form-select').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
        });
    }

    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        const errorEl = getErrorEl(field);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('text-danger');
        }
    }

    function getErrorEl(field) {
        return field.closest('.mb-3')?.querySelector('.error-message') || null;
    }

    function displayErrors(errors) {
        Object.keys(errors).forEach((key) => {
            const errorMessage = errors[key][0];
            const field = document.getElementById(`dj${capitalize(key)}`);
            if (field) {
                showFieldError(field, errorMessage);
            }
        });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function showToast(title, message, type) {
        if (window.adminToaster && window.adminToaster.show) {
            window.adminToaster.show(type, message);
        }
    };

    // Edit DJ function
    window.editDJ = function(id) {
        // Fetch DJ data and open modal
        fetch(`/admin/djs/${id}/edit`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.dj) {
                // Wait for modal to be shown before updating, to ensure date grid is rendered
                const modal = new bootstrap.Modal(addOpen);
                addOpen.addEventListener('shown.bs.modal', function() {
                    updateModalInfo(false, data.dj);
                }, { once: true });
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error fetching DJ:', error);
            showToast('Error', 'Failed to load DJ data', 'error');
        });
    };

    // Delete DJ function
    let djToDelete = null;
    const deleteModal = document.getElementById('deleteDJModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    window.deleteDJ = function(id) {
        djToDelete = id;
        const modal = new bootstrap.Modal(deleteModal);
        modal.show();
    };

    // Handle delete confirmation
    confirmDeleteBtn.addEventListener('click', function() {
        if (!djToDelete) return;

        fetch(`/admin/djs/${djToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', 'DJ deleted successfully', 'success');
                djTable.ajax.reload();
                fetchAvailableDates();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(deleteModal);
                if (modal) modal.hide();
                djToDelete = null;
            } else {
                showToast('Error', data.message || 'Failed to delete DJ', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting DJ:', error);
            showToast('Error', 'Failed to delete DJ', 'error');
        });
    });

});

// Video preview play/pause functions
window.playVideo = function(videoId) {
    const video = document.getElementById(videoId);
    if (video) {
        video.play().catch(err => {
            console.log('Video autoplay prevented:', err);
        });
    }
};

window.pauseVideo = function(videoId) {
    const video = document.getElementById(videoId);
    if (video) {
        video.pause();
        video.currentTime = 0; // Reset to beginning
    }
};
