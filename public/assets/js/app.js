(function () {
    function byId(id) { return document.getElementById(id); }

    function showMessage(el, text, ok) {
        if (!el) return;
        el.textContent = text || '';
        el.classList.toggle('success', !!ok);
    }

    function ajaxForm(form, url, messageEl, onSuccess) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
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
        row.innerHTML = '<input type="text" name="' + type + '[]" required><button type="button" class="btn-secondary" data-action="remove-row">-</button>';
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

    const signupForm = byId('signupForm');
    if (signupForm) {
        ajaxForm(signupForm, '/Ems/public/api/signup', byId('signupMessage'), function (data) {
            if (data.redirect) window.location.href = data.redirect;
        });
    }

    const loginForm = byId('loginForm');
    if (loginForm) {
        ajaxForm(loginForm, '/Ems/public/api/login', byId('loginMessage'), function (data) {
            if (data.redirect) window.location.href = data.redirect;
        });
    }

    const adminLoginForm = byId('adminLoginForm');
    if (adminLoginForm) {
        ajaxForm(adminLoginForm, '/Ems/public/api/admin/login', byId('adminLoginMessage'), function (data) {
            if (data.redirect) window.location.href = data.redirect;
        });
    }

    const profileForm = byId('profileForm');
    if (profileForm) {
        ajaxForm(profileForm, '/Ems/public/api/profile/update', byId('profileMessage'));
    }
})();
