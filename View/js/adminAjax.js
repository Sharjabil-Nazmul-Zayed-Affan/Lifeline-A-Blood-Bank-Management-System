// ==========================================
// View/js/adminAjax.js - AJAX Operations
// ==========================================

// Helper to show inline alerts
function showNotification(message, type = 'success') {
    let alertBox = document.getElementById('ajaxAlert');
    if (!alertBox) {
        alertBox = document.createElement('div');
        alertBox.id = 'ajaxAlert';
        const container = document.querySelector('.container') || document.querySelector('.login-box');
        if (container) {
            container.insertBefore(alertBox, container.firstChild);
        }
    }
    alertBox.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
    alertBox.innerHTML = message;
    alertBox.style.display = 'block';

    // Auto scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// 1. AJAX Admin Login
function handleAdminLogin(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('ajax', '1');

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerText;
    submitBtn.innerText = "Authenticating...";
    submitBtn.disabled = true;

    fetch('../Controller/adminLoginController.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect || 'adminDashboard.php';
            }, 800);
        } else {
            showNotification(data.message || 'Login failed', 'error');
            submitBtn.innerText = originalBtnText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Login Error:', error);
        showNotification('An error occurred during login. Please try again.', 'error');
        submitBtn.innerText = originalBtnText;
        submitBtn.disabled = false;
    });

    return false;
}

// 2. AJAX Delete Hospital without page reload
function deleteHospitalAjax(tin, hospitalName, btnElement) {
    const isConfirmed = confirm(`Are you sure you want to delete ${hospitalName} (TIN: ${tin})?\n\nThis will remove all associated blood bags, reservations, and access.`);
    if (!isConfirmed) {
        return;
    }

    const row = btnElement.closest('tr');
    btnElement.innerText = 'Deleting...';
    btnElement.style.opacity = '0.6';
    btnElement.disabled = true;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('tin', tin);
    formData.append('ajax', '1');

    fetch('../Controller/adminHospitalController.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            
            // Animate and remove table row
            if (row) {
                row.style.transition = 'all 0.4s ease';
                row.style.backgroundColor = '#f8d7da';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    updateHospitalCount();
                }, 400);
            }
        } else {
            showNotification(data.message || 'Failed to delete hospital.', 'error');
            btnElement.innerText = 'Delete';
            btnElement.disabled = false;
            btnElement.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Delete Error:', error);
        showNotification('An error occurred while deleting the hospital.', 'error');
        btnElement.innerText = 'Delete';
        btnElement.disabled = false;
        btnElement.style.opacity = '1';
    });
}

// Helper to update total hospital count in table
function updateHospitalCount() {
    const tbody = document.querySelector('tbody');
    const countDisplay = document.getElementById('totalHospitalCount');
    if (tbody && countDisplay) {
        const remaining = tbody.querySelectorAll('tr').length;
        countDisplay.innerText = `Total: ${remaining} Hospitals`;

        if (remaining === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #777; padding: 20px;">No approved hospitals found in the database.</td></tr>';
        }
    }
}

// 3. AJAX Handle Approval & Rejection for Create & Edit requests
function handleApprovalAjax(event, actionType) {
    event.preventDefault();
    const form = event.target.closest('form');
    if (!form) return false;

    const confirmMsg = actionType.includes('approve') 
        ? 'Confirm approval of this request?' 
        : 'Are you sure you want to reject this request?';

    if (!confirm(confirmMsg)) {
        return false;
    }

    const formData = new FormData(form);
    formData.append('action', actionType);
    formData.append('ajax', '1');

    const submitBtns = form.querySelectorAll('button');
    submitBtns.forEach(btn => btn.disabled = true);

    fetch('../Controller/adminHospitalController.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect || 'adminDashboard.php';
            }, 1000);
        } else {
            showNotification(data.message || 'Action failed.', 'error');
            submitBtns.forEach(btn => btn.disabled = false);
        }
    })
    .catch(error => {
        console.error('Approval Error:', error);
        showNotification('An error occurred while processing request.', 'error');
        submitBtns.forEach(btn => btn.disabled = false);
    });

    return false;
}

// 4. Live Search Filter for Approved Hospitals Table
function searchHospitals() {
    const input = document.getElementById('searchHospitalInput');
    if (!input) return;
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (text.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
