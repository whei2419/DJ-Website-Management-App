document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addEditDJForm');
    const nameInput = document.getElementById('djName');
    // slot field removed from UI/DB; date selection uses `djDateId`
    const videoInput = document.getElementById('djVideo');
    const saveDJButton = document.getElementById('saveDJButton');
    const DjTitle = document.getElementById('DjTitle');
    const addOpen = document.getElementById('addEditDJModal');
    const saveUrl = saveDJRoute;
    const dateIdInput = document.getElementById('djDateId');
    const dateGrid = document.getElementById('dateGrid');
    const visibleInput = document.getElementById('djVisible');

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
                headers: {
                    'Accept': 'application/json'
                },
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
                    data: 'video_preview',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (data || row.hls) {
                            const poster = row.poster ? `poster="${row.poster}"` : '';
                            const videoId = `video-${row.id}`;
                            // If HLS is available prefer HLS playback
                            if (row.hls) {
                                return `<div style="position: relative; width: 100px; height: 75px; border-radius: 4px; overflow: hidden;">
                                        <video id="${videoId}" data-hls="${row.hls}" style="width:100%;height:100%;object-fit:cover;" controls muted playsinline></video>
                                    </div>`;
                            }
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
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name=${encodeURIComponent(data)}&background=random&size=128)"></span>
                            <div class="font-weight-medium">${data}</div>
                        </div>`;
                    }
                },
                // slot column removed (no longer stored in DB)
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
                    data: 'visible',
                    width: '110px',
                    render: function (data) {
                        if (data) {
                            return `<span class="badge bg-success">Visible</span>`;
                        }
                        return `<span class="badge bg-secondary">Hidden</span>`;
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

    // After each draw attach HLS.js to any video elements with data-hls
    $('#djsTable').on('draw.dt', function () {
        document.querySelectorAll('video[data-hls]').forEach(video => {
            const hlsUrl = video.getAttribute('data-hls');
            if (!hlsUrl) return;
            try {
                if (window.Hls && window.Hls.isSupported()) {
                    const hls = new window.Hls();
                    hls.loadSource(hlsUrl);
                    hls.attachMedia(video);
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = hlsUrl;
                }
            } catch (e) {
                console.error('HLS attach failed', e);
            }
        });
    });

    // Wire up custom search input
    const searchInput = document.getElementById('djsTableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            djTable.search(this.value).draw();
        });
    }

    // Wire up custom length selector
    const lengthSelect = document.getElementById('djsTableLength');
    if (lengthSelect) {
        lengthSelect.addEventListener('change', function () {
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
            <div class="date-card" data-id="${date.id}" data-date="${date.date}">
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
        if (dateIdInput) {
            dateIdInput.value = card.dataset.id || '';
        }

        // Clear error if any on the date input
        if (dateIdInput) dateIdInput.classList.remove('is-invalid');
        const errEl = getErrorEl(dateIdInput);
        if (errEl) errEl.textContent = '';
    }

    // on opening the modal, update the form action and inputs
    function updateModalInfo(isAdd, djData = null) {
        if (isAdd) {
            DjTitle.innerHTML = "Add DJ";
            // reset form fields and selections
            if (form) form.reset();
            // ensure file input is cleared
            if (videoInput) try { videoInput.value = ''; } catch (e) { /* ignore */ }
            if (dateIdInput) dateIdInput.value = '';
            document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
            if (visibleInput) visibleInput.checked = true;
            // clear validation errors
            clearErrors();
        } else {
            DjTitle.innerHTML = "Edit DJ";
            nameInput.value = djData.name;
            if (dateIdInput) dateIdInput.value = djData.date_id || '';
            // select the matching date card (prefer matching date_id, fallback to slot)
            document.querySelectorAll('.date-card').forEach(card => {
                if ((djData.date_id && card.dataset.id == djData.date_id) || (!djData.date_id && card.dataset.date === djData.slot)) {
                    card.classList.add('selected');
                }
            });
            if (visibleInput) visibleInput.checked = !!djData.visible;
            // video input left blank for security reasons
        }
    }

    addOpen.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const isAdd = true;
        updateModalInfo(isAdd);
    });

    // handle form submission
    saveDJButton.addEventListener('click', async (e) => {
        e.preventDefault();
        await saveOrUpdateDJ(true);
    });

    // save/update dj via AJAX
    async function saveOrUpdateDJ(isAdd, djId = null) {
        // gather form data
        const formData = new FormData();
        formData.append('name', nameInput.value);
        // `slot` removed from backend; associate by `date_id` only
        // include date_id if present
        if (dateIdInput && dateIdInput.value) {
            formData.append('date_id', dateIdInput.value);
        }
        // include visibility flag
        if (visibleInput) {
            formData.append('visible', visibleInput.checked ? 1 : 0);
        }
        // Note: do not append file here. If an Uppy file is present we'll upload it via chunking
        // and set `video_path` on the formData. If a native file exists, the existing XHR
        // upload behavior below will include it.

        // log form data for debugging
        console.log('Form Data:', {
            name: nameInput.value,
            date_id: dateIdInput ? dateIdInput.value : null,
            video: (videoInput.files && videoInput.files.length > 0) ? videoInput.files[0].name : null
        });

        // validate form
        if (!validateForm()) {
            return;
        }

        // Determine if an Uppy file is present
        let uppyFile = null;
        if (window.uppy && window.uppy.getFiles().length > 0) {
            uppyFile = window.uppy.getFiles()[0];
        }

        // If Uppy file present, perform chunked upload first and attach video_path
        if (uppyFile) {
            try {
                showUploadProgressModal();
                // Construct a File object for chunking
                let fileObj;
                try {
                    fileObj = new File([uppyFile.data], uppyFile.name, { type: uppyFile.type });
                } catch (e) {
                    fileObj = uppyFile.data;
                    fileObj.name = uppyFile.name || fileObj.name || 'upload';
                }

                const result = await uploadFileInChunks(fileObj, (pct) => updateUploadProgress(pct));
                // Attach server-side path returned by assemble endpoint
                formData.append('video_path', result.path);

                // Now submit form without file (server will use video_path)
                const xhr = new XMLHttpRequest();
                xhr.open('POST', saveUrl, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                xhr.setRequestHeader('Accept', 'application/json');

                // minimal progress for final POST
                if (xhr.upload) {
                    xhr.upload.onprogress = function (event) {
                        if (event.lengthComputable) {
                            const percent = Math.round((event.loaded / event.total) * 100);
                            updateUploadProgress(percent);
                        }
                    };
                }

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        updateUploadProgress(100);
                        let parsed = {};
                        try { parsed = JSON.parse(xhr.responseText || '{}'); } catch (e) { }

                        if (xhr.status >= 200 && xhr.status < 300 && parsed.success) {
                            showToast('Success!', 'DJ saved successfully', 'success');
                            try { const modal = bootstrap.Modal.getInstance(addOpen); if (modal) modal.hide(); } catch (e) { }
                            if (form) form.reset(); if (dateIdInput) dateIdInput.value = ''; document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
                            djTable.ajax.reload(); fetchAvailableDates();
                        } else {
                            if (parsed && parsed.errors) displayErrors(parsed.errors);
                            else showToast('Error', parsed.message || 'Failed to save DJ', 'error');
                        }

                        hideUploadProgressModal();
                        // reset Uppy
                        try { window.uppy.reset(); } catch (e) { }
                    }
                };

                xhr.onerror = function () { showToast('Error', 'An error occurred while saving the DJ', 'error'); hideUploadProgressModal(); };
                xhr.send(formData);
            } catch (err) {
                showToast('Error', err.message || 'Upload failed', 'error');
                hideUploadProgressModal();
            }
            return;
        }

        // No Uppy file — proceed with original XHR upload path (native file or no file)
        const xhr = new XMLHttpRequest();
        xhr.open('POST', saveUrl, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.setRequestHeader('Accept', 'application/json');

        // Show upload modal
        showUploadProgressModal();

        // Track upload progress
        if (xhr.upload) {
            xhr.upload.onprogress = function (event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    updateUploadProgress(percent);
                }
            };
        }

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                // Hide progress modal once finished
                updateUploadProgress(100);

                let parsed = null;
                try {
                    parsed = JSON.parse(xhr.responseText || '{}');
                } catch (e) {
                    console.error('Non-JSON response:', xhr.responseText);
                    showToast('Error', 'Server returned unexpected response', 'error');
                    hideUploadProgressModal();
                    return;
                }

                if (xhr.status >= 200 && xhr.status < 300 && parsed.success) {
                    showToast('Success!', 'DJ saved successfully', 'success');
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

                    // Reset form and reload
                    if (form) form.reset();
                    if (dateIdInput) dateIdInput.value = '';
                    document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
                    djTable.ajax.reload();
                    fetchAvailableDates();
                } else {
                    if (parsed && parsed.errors) {
                        displayErrors(parsed.errors);
                    } else if (parsed && parsed.message) {
                        showToast('Error', parsed.message, 'error');
                    } else {
                        showToast('Error', 'Failed to save DJ', 'error');
                    }
                }

                hideUploadProgressModal();
            }
        };

        xhr.onerror = function (err) {
            console.error('Upload error:', err);
            showToast('Error', 'An error occurred while saving the DJ', 'error');
            hideUploadProgressModal();
        };

        // append native file if exists
        if (videoInput && videoInput.files && videoInput.files.length > 0) {
            formData.append('video', videoInput.files[0]);
        }

        xhr.send(formData);
    }

    function validateForm() {
        let isValid = true;
        // clear all previous errors
        clearErrors();

        if (!nameInput.value.trim()) {
            showFieldError(nameInput, 'Name is required');
            isValid = false;
        }
        // require a selected date (date_id)
        if (dateIdInput && !dateIdInput.value.trim()) {
            showFieldError(dateIdInput, 'Please select a date');
            isValid = false;
        }
        const hasNativeFile = videoInput && videoInput.files && videoInput.files.length > 0;
        const hasUppyFile = window.uppy && window.uppy.getFiles && window.uppy.getFiles().length > 0;
        if (!hasNativeFile && !hasUppyFile) {
            // prefer to attach error to uploader if present, otherwise video input
            const fieldEl = document.getElementById('uploader') || videoInput;
            showFieldError(fieldEl, 'Video is required');
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
            let field = null;
            if (key === 'date_id') {
                field = document.getElementById('djDateId') || document.getElementById('date_id');
            } else {
                field = document.getElementById(`dj${capitalize(key)}`);
            }
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

    // Upload progress modal helpers
    function showUploadProgressModal() {
        // Prefer inline progress container if available
        const inline = document.getElementById('uploadInlineProgress');
        if (inline) {
            inline.classList.remove('d-none');
            updateUploadProgress(0);
            return;
        }

        // Fallback to legacy modal if present
        const modalEl = document.getElementById('uploadProgressModal');
        if (modalEl) {
            try {
                const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
                updateUploadProgress(0);
                modal.show();
            } catch (e) {
                console.warn('Could not show upload progress modal', e);
            }
        }
    }

    function updateUploadProgress(percent) {
        // Try inline bar first, then fallback to modal bar
        const bar = document.getElementById('uploadInlineProgressBar') || document.getElementById('uploadProgressBar');
        const text = document.getElementById('uploadInlineProgressText') || document.getElementById('uploadProgressText');
        if (bar) {
            bar.style.width = percent + '%';
            bar.textContent = percent + '%';
        }
        if (text) {
            if (percent < 100) text.textContent = `Uploading...`;
            else text.textContent = `Finalizing...`;
        }
    }

    function hideUploadProgressModal() {
        const inline = document.getElementById('uploadInlineProgress');
        if (inline) {
            inline.classList.add('d-none');
            return;
        }

        const modalEl = document.getElementById('uploadProgressModal');
        if (modalEl) {
            try {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } catch (e) {
                // ignore
            }
        }
    }

    // Edit DJ function
    window.editDJ = function (id) {
        // Fetch DJ data and open modal
        fetch(`/admin/djs/${id}/edit`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(response => response.json())
            .then(data => {
                if (data.dj) {
                    // Wait for modal to be shown before updating, to ensure date grid is rendered
                    const modal = new bootstrap.Modal(addOpen);
                    addOpen.addEventListener('shown.bs.modal', function () {
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

    window.deleteDJ = function (id) {
        djToDelete = id;
        const modal = new bootstrap.Modal(deleteModal);
        modal.show();
    };

    // Handle delete confirmation
    confirmDeleteBtn.addEventListener('click', function () {
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
window.playVideo = function (videoId) {
    const video = document.getElementById(videoId);
    if (video) {
        video.play().catch(err => {
            console.log('Video autoplay prevented:', err);
        });
    }
};

window.pauseVideo = function (videoId) {
    const video = document.getElementById(videoId);
    if (video) {
        video.pause();
        video.currentTime = 0; // Reset to beginning
    }
};
