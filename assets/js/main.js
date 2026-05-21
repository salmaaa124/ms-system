/**
 * NeuroDiag MS - Client-side interactions.
 */

// -------- Upload page: drag & drop + preview --------
(function initUploadPage() {
    const dz       = document.getElementById('dropzone');
    const input    = document.getElementById('fileInput');
    const preview  = document.getElementById('previewWrap');
    const img      = document.getElementById('previewImg');
    const nameEl   = document.getElementById('fileName');
    const submitBtn= document.getElementById('submitBtn');
    const form     = document.getElementById('uploadForm');
    if (!dz || !input) return;

    dz.addEventListener('click', () => input.click());

    ['dragenter', 'dragover'].forEach(ev => {
        dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('drag'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
        dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('drag'); });
    });
    dz.addEventListener('drop', e => {
        if (e.dataTransfer.files && e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            showPreview(input.files[0]);
        }
    });
    input.addEventListener('change', () => {
        if (input.files && input.files[0]) showPreview(input.files[0]);
    });

    function showPreview(file) {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            preview.classList.add('show');
            nameEl.textContent = file.name + ' · ' + formatBytes(file.size);
            if (submitBtn) submitBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    function formatBytes(b) {
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(2) + ' MB';
    }

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            const label = submitBtn.querySelector('.label');
            if (label) label.textContent = submitBtn.dataset.analysing || 'Analysing…';
            submitBtn.innerHTML = '<span class="spinner"></span> ' + (submitBtn.dataset.analysing || 'Analysing…');
            submitBtn.disabled = true;
        });
    }
})();

// -------- Result page: animate progress bar --------
(function animateConfidence() {
    const bar = document.querySelector('.progress-bar[data-value]');
    if (!bar) return;
    const val = parseFloat(bar.dataset.value);
    requestAnimationFrame(() => { bar.style.width = val + '%'; });
})();
