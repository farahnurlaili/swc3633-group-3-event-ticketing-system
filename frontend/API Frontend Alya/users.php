<?php
require_once 'header.php';
?>

<div class="order-summary-card">
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h2 class="page-title">User Accounts Management</h2>
            <p class="page-subtitle">Manage registered user profiles, roles, and account permissions</p>
        </div>
        <span class="ticket-id-badge" id="user-count-badge">
            Loading Users...
        </span>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
                <tr>
                    <td colspan="6" class="p-4 text-center text-slate-500">Loading user accounts...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetchUsers();
});

function fetchUsers() {
    fetch('api_users.php', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || '')
        }
    })
    .then(response => response.json())
    .then(res => {
        const tbody = document.getElementById('users-table-body');
        tbody.innerHTML = '';

        if (res.status === 'success' && res.data && res.data.length > 0) {
            document.getElementById('user-count-badge').innerText = res.data.length + ' Registered Users';
            
            let currentUserId = 0;
            try {
                const token = localStorage.getItem('auth_token');
                const base64Payload = token.split('.')[0].replace(/-/g, '+').replace(/_/g, '/');
                const decoded = JSON.parse(decodeURIComponent(atob(base64Payload).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join('')));
                currentUserId = decoded.user_id || 0;
            } catch (e) {}

            res.data.forEach(u => {
                const isSelf = (u.user_id === currentUserId);
                const roleLower = (u.role || '').toLowerCase();

                let row = `
                <tr class="hover:bg-slate-50 transition">
                    <td class="font-mono text-xs text-slate-500">USR-${u.user_id}</td>
                    <td class="font-semibold text-slate-900">${escapeHtml(u.full_name)}</td>
                    <td class="text-xs text-slate-600">${escapeHtml(u.email)}</td>
                    <td>
                        <select onchange="updateUserRole(${u.user_id}, this.value)" class="role-select">
                            <option value="customer" ${roleLower === 'customer' ? 'selected' : ''}>Customer</option>
                            <option value="organiser" ${roleLower === 'organiser' ? 'selected' : ''}>Organiser</option>
                            <option value="admin" ${roleLower === 'admin' ? 'selected' : ''}>Administrator</option>
                        </select>
                    </td>
                    <td class="text-xs text-slate-500">${escapeHtml(u.created_at || 'N/A')}</td>
                    <td class="text-right space-x-2">
                        ${!isSelf ? `
                            <button onclick="deleteUser(${u.user_id})" class="btn-delete-row space-x-1">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Delete</span>
                            </button>
                        ` : `
                            <span class="text-[11px] text-slate-400 font-medium italic">Active Account</span>
                        `}
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-slate-500">No users found.</td></tr>';
            document.getElementById('user-count-badge').innerText = '0 Registered Users';
        }
    })
    .catch(err => {
        console.error('Error fetching users:', err);
        document.getElementById('users-table-body').innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Failed to load user accounts from API.</td></tr>';
    });
}

function updateUserRole(userId, newRole) {
    const token = localStorage.getItem('auth_token') || '';
    fetch('api_users.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({ user_id: userId, role: newRole })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('User role updated successfully!');
            fetchUsers();
        } else {
            alert(data.message || 'Failed to update user role.');
            fetchUsers();
        }
    })
    .catch(err => {
        console.error('Error updating role:', err);
        alert('An unexpected error occurred.');
    });
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete user USR-' + userId + '?')) return;

    const token = localStorage.getItem('auth_token') || '';
    fetch(`api_users.php?user_id=${userId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('User removed successfully!');
            fetchUsers();
        } else {
            alert(data.message || 'Failed to delete user.');
        }
    })
    .catch(err => {
        console.error('Error deleting user:', err);
        alert('An unexpected error occurred.');
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php require_once 'footer.php'; ?>