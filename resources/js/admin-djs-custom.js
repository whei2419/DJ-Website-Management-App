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
    let currentPage = 1;
    let currentPerPage = 20;
    let currentSearch = '';

    // Fetch available dates on page load
    fetchAvailableDates();
    
    // Fetch and render DJ list on page load
    fetchDJList();

    // Hook up search input
    const searchInput = document.getElementById('advanced-table-search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = e.target.value;
                currentPage = 1;
                fetchDJList();
            }, 500); // Debounce 500ms
        });
    }

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
        if (availableDates.length === 0) {
            dateGrid.innerHTML = '<div class="text-muted text-center py-3">No dates available</div>';
            return;
        }

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
                    console.warn('Modal close failed:', e);
                }
                
                // Reset form
                if (form) form.reset();
                document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
                
                // Refresh dates to update DJ counts
                fetchAvailableDates();
                
                // Reload DJ list
                fetchDJList();
                
            } else if (!ok && data) {
                // server returned JSON error payload (e.g., validation errors)
                console.error('Server validation/error response:', data);
                // map validation errors to form fields if present
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const input = document.getElementById(`dj${key.charAt(0).toUpperCase() + key.slice(1)}`) || document.querySelector(`[name="${key}"]`);
                        const errEl = input ? getErrorEl(input) : null;
                        if (input) input.classList.add('is-invalid');
                        if (errEl) errEl.textContent = data.errors[key].join(', ');
                    });
                }
                showToast('Error', data.message || 'Failed to save DJ', 'danger');
            } else {
                console.error('Unexpected server response:', data);
                showToast('Error', 'Unexpected server response', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Only show error toast if it's a real fetch/network error, not a post-success issue
            if (!error.message || !error.message.includes('Modal') && !error.message.includes('form')) {
                showToast('Error', 'Failed to save DJ. Please try again.', 'error');
            }
        });
    };

    // helper to find the nearest .error-message for an input
    function getErrorEl(input) {
        return input?.closest('.mb-3')?.querySelector('.error-message') || null;
    }

    // reset input class and error message on typing
    [nameInput, slotInput, videoInput].forEach(input => {
        if (!input) return;
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
            const err = getErrorEl(input);
            if (err) err.textContent = '';
        });
    });

    function validateForm() {
        let isValid = true;
        // Simple validation: check if name and slot are not empty
        // Name
        const nameErr = getErrorEl(nameInput);
        if (!nameInput.value.trim()) {
            isValid = false;
            nameInput.classList.add('is-invalid');
            if (nameErr) nameErr.textContent = 'Name is required.';
        } else {
            nameInput.classList.remove('is-invalid');
            if (nameErr) nameErr.textContent = '';
        }

        // Video (file input)
        const videoErr = getErrorEl(videoInput);
        const hasVideo = videoInput && videoInput.files && videoInput.files.length > 0;
        if (!hasVideo) {
            isValid = false;
            videoInput.classList.add('is-invalid');
            if (videoErr) videoErr.textContent = 'Video Preview is required.';
        } else {
            const allowedTypes = ['video/webm', 'video/mp4', 'video/ogg']; // Add valid video types
            const fileType = videoInput.files[0].type;
            if (!allowedTypes.includes(fileType)) {
                isValid = false;
                videoInput.classList.add('is-invalid');
                if (videoErr) videoErr.textContent = `Invalid file type. Allowed: ${allowedTypes.join(', ')}`;
            } else {
                videoInput.classList.remove('is-invalid');
                if (videoErr) videoErr.textContent = '';
            }
        }

        // Slot
        const slotErr = getErrorEl(slotInput);
        if (!slotInput.value.trim()) {
            isValid = false;
            slotInput.classList.add('is-invalid');
            if (slotErr) slotErr.textContent = 'Time Slot is required.';
        } else {
            slotInput.classList.remove('is-invalid');
            if (slotErr) slotErr.textContent = '';
        }

        return isValid;
    }

    // Toast notification function
    function showToast(title, message, type = 'info') {
        if (window.adminToaster) {
            window.adminToaster.show(type, message, { 
                title: title,
                autohide: true,
                timeout: 4000
            });
        }
    }

    // Fetch DJ list
    function fetchDJList(page = null, perPage = null, search = null) {
        // Use current state if not provided
        if (page !== null) currentPage = page;
        if (perPage !== null) currentPerPage = perPage;
        if (search !== null) currentSearch = search;

        const url = new URL(djsDataRoute);
        url.searchParams.append('page', currentPage);
        url.searchParams.append('per_page', currentPerPage);
        if (currentSearch) url.searchParams.append('search', currentSearch);

        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            renderDJTable(result.data || []);
            renderPagination(result.meta || {});
        })
        .catch(error => {
            console.error('Error fetching DJ list:', error);
            document.querySelector('.table-tbody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load DJs</td></tr>';
        });
    }

    function renderDJTable(djs) {
        const tbody = document.querySelector('.table-tbody');
        
        if (djs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4"><div class="empty"><div class="empty-icon"><i class="fas fa-users"></i></div><p class="empty-title">No DJs found</p></div></td></tr>';
            return;
        }

        tbody.innerHTML = djs.map(dj => `
            <tr>
                <td class="text-muted">#${dj.id}</td>
                <td>
                    ${dj.video_preview ? `
                        <div style="position: relative; width: 100px; height: 75px; border-radius: 4px; overflow: hidden;">
                            <video style="width: 100%; height: 100%; object-fit: cover;" preload="metadata">
                                <source src="${dj.video_preview}" type="video/webm">
                            </video>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; opacity: 0.9;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    ` : '<span class="avatar avatar-sm" style="background-color: var(--tblr-muted-bg);"><i class="fas fa-video text-muted"></i></span>'}
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name=${encodeURIComponent(dj.name)}&background=random&size=128)"></span>
                        <div class="font-weight-medium">${dj.name}</div>
                    </div>
                </td>
                <td>
                    <div class="text-muted">
                        <i class="fas fa-calendar-day me-1"></i>${dj.slot}
                    </div>
                </td>
                <td class="text-end">
                    <div class="btn-list">
                        <button class="btn btn-sm btn-primary" onclick="window.editDJ(${dj.id})" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="window.deleteDJ(${dj.id})" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(meta) {
        const paginationContainer = document.querySelector('.pagination');
        const pageCount = document.getElementById('page-count');
        
        if (!meta || !paginationContainer) return;

        // Update per page display
        if (pageCount) {
            pageCount.textContent = meta.per_page || 20;
        }

        // Build pagination links
        if (meta.last_page && meta.last_page > 1) {
            let paginationHTML = '';
            
            // Previous button
            if (meta.current_page > 1) {
                paginationHTML += `<li class="page-item"><a class="page-link cursor-pointer" data-page="${meta.current_page - 1}">‹</a></li>`;
            }

            // Page numbers
            for (let i = 1; i <= meta.last_page; i++) {
                // Show first, last, current, and nearby pages
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    const activeClass = i === meta.current_page ? ' active' : '';
                    paginationHTML += `<li class="page-item${activeClass}"><a class="page-link cursor-pointer" data-page="${i}">${i}</a></li>`;
                } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
                    paginationHTML += `<li class="page-item disabled"><a class="page-link cursor-pointer">...</a></li>`;
                }
            }

            // Next button
            if (meta.current_page < meta.last_page) {
                paginationHTML += `<li class="page-item"><a class="page-link cursor-pointer" data-page="${meta.current_page + 1}">›</a></li>`;
            }

            paginationContainer.innerHTML = paginationHTML;

            // Add click handlers to pagination links
            paginationContainer.querySelectorAll('a[data-page]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.dataset.page);
                    if (page) fetchDJList(page);
                });
            });
        } else {
            paginationContainer.innerHTML = '<li class="page-item active"><a class="page-link">1</a></li>';
        }
    }

    // Hook up per-page dropdown
    window.setPageListItems = function(event) {
        const value = parseInt(event.target.dataset.value);
        if (value) {
            currentPerPage = value;
            currentPage = 1;
            fetchDJList();
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
                fetchDJList();
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
