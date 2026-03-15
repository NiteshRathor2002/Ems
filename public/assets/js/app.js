(function () {
    function byId(id) { return document.getElementById(id); }

    function basePath() {
        var el = document.body;
        return (el && el.getAttribute('data-base')) ? el.getAttribute('data-base') : '/Ems/public';
    }

    function showMessage(el, text, ok) {
        if (!el) return;
        el.textContent = text || '';
        el.classList.remove('text-success', 'text-danger');
        if (text) el.classList.add(ok ? 'text-success' : 'text-danger');
    }

    function ajaxForm(form, url, messageEl, onSuccess) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                form.classList.add('was-validated');
                showMessage(messageEl, 'Please fill the required fields.', false);
                return;
            }

            showMessage(messageEl, 'Please wait...', false);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    showMessage(messageEl, data.message || 'Request failed', false);
                    return;
                }
                showMessage(messageEl, data.message || 'Success', true);
                if (typeof onSuccess === 'function') onSuccess(data);
            } catch (err) {
                showMessage(messageEl, 'Network error', false);
            }
        });
    }

    function createDynamicRow(type) {
        const row = document.createElement('div');
        row.className = 'dynamic-row';
        row.innerHTML = '<input type="text" name="' + type + '[]" required><button type="button" class="btn btn-outline-secondary btn-sm" data-action="remove-row">-</button>';
        return row;
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        if (action === 'add-qualification') {
            const wrap = byId('qualificationWrap');
            if (wrap) wrap.appendChild(createDynamicRow('qualifications'));
            return;
        }
        if (action === 'add-experience') {
            const wrap = byId('experienceWrap');
            if (wrap) wrap.appendChild(createDynamicRow('experiences'));
            return;
        }
        if (action === 'remove-row') {
            const row = btn.closest('.dynamic-row');
            if (!row) return;
            const container = row.parentElement;
            if (container && container.querySelectorAll('.dynamic-row').length === 1) return;
            row.remove();
        }
    });

    // Bootstrap client-side validation for non-AJAX forms
    document.querySelectorAll('form.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // File validations + preview
    document.addEventListener('change', function (e) {
        var input = e.target;
        if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;

        var maxSize = parseInt(input.getAttribute('data-max-size') || '0', 10);
        var file = (input.files && input.files[0]) ? input.files[0] : null;
        if (!file) {
            input.setCustomValidity('');
            return;
        }

        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (allowed.indexOf(file.type) === -1) {
            input.setCustomValidity('Only jpg, png, webp are allowed.');
        } else if (maxSize > 0 && file.size > maxSize) {
            input.setCustomValidity('Image size must be <= 2MB.');
        } else {
            input.setCustomValidity('');
        }

        // Admin employee preview
        var preview = byId('employeeProfilePreview');
        if (preview && input.name === 'profile_picture') {
            var wrap = byId('employeeProfilePreviewWrap');
            if (wrap) wrap.classList.remove('d-none');
            var url = URL.createObjectURL(file);
            preview.src = url;
            preview.onload = function () { URL.revokeObjectURL(url); };
        }
    });

    const signupForm = byId('signupForm');
    if (signupForm) {
        ajaxForm(signupForm, basePath() + '/api/signup', byId('signupMessage'), function (data) {
            if (data.redirect) window.location.href = data.redirect;
        });
    }

    const loginForm = byId('loginForm');
    if (loginForm) {
        ajaxForm(loginForm, basePath() + '/api/login', byId('loginMessage'), function (data) {
            if (data.redirect) window.location.href = data.redirect;
        });
    }

    const adminLoginForm = byId('adminLoginForm');
    if (adminLoginForm) {
        ajaxForm(adminLoginForm, basePath() + '/api/admin/login', byId('adminLoginMessage'), function (data) {
            if (data.redirect) window.location.href = data.redirect;
        });
    }

    const profileForm = byId('profileForm');
    if (profileForm) {
        ajaxForm(profileForm, basePath() + '/api/profile/update', byId('profileMessage'));
    }
})();
