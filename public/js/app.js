document.addEventListener('DOMContentLoaded', () => {
    initFormValidation();
    initFilters();
    initDeleteHandlers();
});

// ── Form Validation (JS-side, mirrors PHP backend rules) ───────────────────

function initFormValidation() {
    const taskForm  = document.getElementById('task-form');
    const loginForm = document.getElementById('login-form');

    if (taskForm)  taskForm.addEventListener('submit',  e => validateTaskForm(e, taskForm));
    if (loginForm) loginForm.addEventListener('submit', e => validateLoginForm(e, loginForm));
}

function validateTaskForm(e, form) {
    clearErrors(form);
    const errors = [];

    const title    = form.querySelector('#title');
    const priority = form.querySelector('#priority');
    const status   = form.querySelector('#status');
    const dueDate  = form.querySelector('#due_date');

    if (!title.value.trim()) {
        setError(title, 'Title is required.');
        errors.push('title');
    } else if (title.value.trim().length > 200) {
        setError(title, 'Title must not exceed 200 characters.');
        errors.push('title');
    }

    const validPriorities = ['low', 'medium', 'high'];
    if (!validPriorities.includes(priority.value)) {
        setError(priority, 'Please select a valid priority.');
        errors.push('priority');
    }

    const validStatuses = ['pending', 'in_progress', 'completed'];
    if (!validStatuses.includes(status.value)) {
        setError(status, 'Please select a valid status.');
        errors.push('status');
    }

    if (dueDate.value && isNaN(Date.parse(dueDate.value))) {
        setError(dueDate, 'Please enter a valid date.');
        errors.push('due_date');
    }

    if (errors.length > 0) {
        e.preventDefault();
        form.querySelector('#' + errors[0])?.focus();
    }
}

function validateLoginForm(e, form) {
    clearErrors(form);
    const errors = [];

    const email    = form.querySelector('#email');
    const password = form.querySelector('#password');

    if (!email.value.trim()) {
        setError(email, 'Email address is required.');
        errors.push('email');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        setError(email, 'Please enter a valid email address.');
        errors.push('email');
    }

    if (!password.value) {
        setError(password, 'Password is required.');
        errors.push('password');
    }

    if (errors.length > 0) {
        e.preventDefault();
        form.querySelector('#' + errors[0])?.focus();
    }
}

function setError(field, message) {
    field.classList.add('input-error');

    const msg = document.createElement('span');
    msg.className   = 'field-error';
    msg.textContent = message;
    field.closest('.form-group')?.appendChild(msg);

    field.addEventListener('input', () => {
        field.classList.remove('input-error');
        msg.remove();
    }, { once: true });
}

function clearErrors(form) {
    form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    form.querySelectorAll('.field-error').forEach(el => el.remove());
}


// ── Search & Filter (client-side, no page reload) ──────────────────────────

function initFilters() {
    const search   = document.getElementById('search-input');
    const status   = document.getElementById('filter-status');
    const priority = document.getElementById('filter-priority');
    const clearBtn = document.getElementById('filter-clear');

    if (!search) return; // not on dashboard

    const applyFilters = () => {
        const q        = search.value.trim().toLowerCase();
        const sts      = status.value;
        const pri      = priority.value;
        const hasAny   = q || sts || pri;

        clearBtn.style.display = hasAny ? '' : 'none';
        filterRows(q, sts, pri);
    };

    search.addEventListener('input', applyFilters);
    status.addEventListener('change', applyFilters);
    priority.addEventListener('change', applyFilters);

    clearBtn.addEventListener('click', () => {
        search.value   = '';
        status.value   = '';
        priority.value = '';
        clearBtn.style.display = 'none';
        filterRows('', '', '');
    });
}

function filterRows(query, status, priority) {
    const rows      = document.querySelectorAll('#task-table tbody tr[data-title]');
    const noResults = document.getElementById('no-results-row');
    let   visible   = 0;

    rows.forEach(row => {
        const matchTitle    = !query    || row.dataset.title.includes(query);
        const matchStatus   = !status   || row.dataset.status   === status;
        const matchPriority = !priority || row.dataset.priority === priority;

        const show = matchTitle && matchStatus && matchPriority;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
}

// ── Delete with fetch() ─────────────────────────────────────────────────────

function initDeleteHandlers() {
    const overlay = buildModal();

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            showModal(overlay, () => submitDelete(form));
        });
    });
}

function submitDelete(form) {
    const data = new FormData(form);

    fetch(form.action, {
        method:  'POST',
        body:    data,
        headers: { 'X-Requested-With': 'fetch' },
    })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                const row = form.closest('tr');
                row.remove();
                checkEmptyTable();
            }
        })
        .catch(() => form.submit()); // fallback: full page submit
}

function checkEmptyTable() {
    const visibleRows = document.querySelectorAll('#task-table tbody tr[data-title]:not([style*="display: none"])');
    const noResults   = document.getElementById('no-results-row');

    if (document.querySelectorAll('#task-table tbody tr[data-title]').length === 0) {
        // All rows gone — swap table for empty state
        document.querySelector('.table-wrapper')?.remove();
        const section = document.querySelector('.task-section');
        if (section) {
            section.innerHTML = '<div class="empty-state" id="empty-state"><p>No tasks yet. <a href="index.php?action=create">Create your first task →</a></p></div>';
        }
    } else if (noResults && visibleRows.length === 0) {
        noResults.style.display = '';
    }
}

// ── Modal ───────────────────────────────────────────────────────────────────

function buildModal() {
    const overlay = document.createElement('div');
    overlay.className    = 'modal-overlay';
    overlay.style.display = 'none';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.innerHTML = `
        <div class="modal">
            <h2 id="modal-title">Delete Task?</h2>
            <p>This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="modal-cancel" class="btn btn-ghost">Cancel</button>
                <button id="modal-confirm" class="btn btn-danger">Delete</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    overlay.querySelector('#modal-cancel').addEventListener('click', () => overlay.style.display = 'none');
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.style.display = 'none'; });

    return overlay;
}

function showModal(overlay, onConfirm) {
    overlay.style.display = 'flex';

    // Replace confirm button to clear any stale listeners
    const btn   = overlay.querySelector('#modal-confirm');
    const fresh = btn.cloneNode(true);
    btn.replaceWith(fresh);

    fresh.addEventListener('click', () => {
        overlay.style.display = 'none';
        onConfirm();
    });
}
