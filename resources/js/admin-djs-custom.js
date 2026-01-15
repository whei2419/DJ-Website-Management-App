document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addEditDJForm');
    const nameInput = document.getElementById('djName');
    const slotInput = document.getElementById('djSlot');
    const videoInput = document.getElementById('djVideo');
    const saveDJButton = document.getElementById('saveDJButton');
    const DjTitle = document.getElementById('DjTitle');
    const addOpen = document.getElementById('addEditDJModal');

    // on opening the modal, update the form action and inputs
    function updateModalInfo(isAdd, djData = null) {
        if (isAdd) {
            DjTitle.innerHTML = "Add DJ";
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
        formData.append('slot', slotInput.value);
        if (videoInput.value) {
            formData.append('video', videoInput.value);
        }

        // log form data for debugging
        console.log('Form Data:', {
            name: nameInput.value,
            slot: slotInput.value,
            video: videoInput.value
        });

        // validate form
        if (!validateForm()) {
            return;
        }

        // submit via fetch
        
        
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
            videoInput.classList.remove('is-invalid');
            if (videoErr) videoErr.textContent = '';
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
