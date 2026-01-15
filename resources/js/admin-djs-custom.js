document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addEditDJForm');
    const nameInput = document.getElementById('djName');
    const slotInput = document.getElementById('djSlot');
    const videoInput = document.getElementById('djVideo');
    const saveDJButton = document.getElementById('saveDJButton');
    const DjTitle = document.getElementById('DjTitle');
    const addOpen = document.getElementById('addEditDJModal');
    const saveUrl = saveDJRoute;
    

    // on opening the modal, update the form action and inputs
    function updateModalInfo(isAdd, djData = null) {
        if (isAdd) {
            DjTitle.innerHTML = "Add DJ";
            // default the slot to today's date (YYYY-MM-DD) for date-only input
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            slotInput.value = `${yyyy}-${mm}-${dd}`;
        } else {
            DjTitle.innerHTML = "Edit DJ";
            nameInput.value = djData.name;
            slotInput.value = djData.slot;
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
            } else {
                console.error('Unexpected server response:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
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

});
