<?php
require_once __DIR__ . '/../models/AdminModel.php';

$adminModel = new AdminModel();
$clients = $adminModel->getClients();
?>

<div class="page-header">
    <h1>Clients</h1>
    <p>Manage your client accounts and their files</p>
</div>

<!-- Clients Table -->
<div class="table-container">
    <?php if (empty($clients)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>No clients yet</h3>
            <p>Create your first client to get started</p>
            <button onclick="openClientModal()" class="btn" style="margin-top: var(--space-lg);">
                <i class="fas fa-user-plus"></i>
                Add Client
            </button>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Event Code</th>
                    <th>Files</th>
                    <th>Last Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client):
                    $fileInfo = $adminModel->getClientFileInfo($client['folder_name']);
                ?>
                    <tr id="row-<?php echo htmlspecialchars($client['folder_name']); ?>">
                        <td>
                            <a href="controllers/AuthController.php?auto_login=true&client=<?php echo urlencode($client['folder_name']); ?>">
                                <i class="fas fa-folder" style="opacity: 0.5;"></i>
                                <?php echo htmlspecialchars($client['client_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($client['event_name']); ?></td>
                        <td>
                            <span class="badge">
                                <i class="fas fa-calendar"></i>
                                <?php echo htmlspecialchars($client['event_date']); ?>
                            </span>
                        </td>
                        <td>
                            <code style="background: var(--color-bg-primary); padding: 4px 8px; border-radius: 6px; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($client['event_code']); ?>
                            </code>
                        </td>
                        <td>
                            <span class="badge <?php echo $fileInfo['count'] > 0 ? 'badge-success' : ''; ?>">
                                <i class="fas fa-file"></i>
                                <?php echo $fileInfo['count']; ?>
                            </span>
                        </td>
                        <td style="color: var(--color-text-tertiary); font-size: 0.875rem;">
                            <?php echo $fileInfo['last_edit'] ?: '—'; ?>
                        </td>
                        <td class="table-actions">
                            <button class="btn-action edit-btn"
                                title="Edit client"
                                onclick="openEditModal('<?php echo htmlspecialchars($client['folder_name']); ?>',
                                                       '<?php echo htmlspecialchars($client['client_name']); ?>',
                                                       '<?php echo htmlspecialchars($client['event_name']); ?>',
                                                       '<?php echo htmlspecialchars($client['event_date']); ?>')">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <button class="btn-action delete-btn"
                                title="Delete client"
                                onclick="confirmDeleteClient('<?php echo htmlspecialchars($client['folder_name']); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Create Client Modal -->
<div id="createClientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeClientModal()">&times;</span>
        <h2>New Client</h2>
        <form id="createClientForm" method="post" action="controllers/AdminController.php">
            <div class="form-group">
                <label for="new_client_name">Client Name</label>
                <input type="text" name="client_name" id="new_client_name" placeholder="e.g., John & Jane" required>
            </div>
            <div class="form-group">
                <label for="new_event_name">Event Name</label>
                <input type="text" name="event_name" id="new_event_name" placeholder="e.g., Wedding Reception" required>
            </div>
            <div class="form-group">
                <label for="new_event_date">Event Date</label>
                <input type="date" name="event_date" id="new_event_date" required>
            </div>
            <div style="display: flex; gap: var(--space-md); margin-top: var(--space-md);">
                <button type="button" class="btn btn-secondary" onclick="closeClientModal()" style="flex: 1;">
                    Cancel
                </button>
                <button type="submit" class="btn" style="flex: 1;">
                    <i class="fas fa-plus"></i>
                    Create Client
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Client Modal -->
<div id="editClientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Client</h2>
        <form id="editClientForm" method="post" action="controllers/AdminController.php">
            <input type="hidden" name="folder_name" id="edit_folder_name">
            <div class="form-group">
                <label for="edit_client_name">Client Name</label>
                <input type="text" name="client_name" id="edit_client_name" required>
            </div>
            <div class="form-group">
                <label for="edit_event_name">Event Name</label>
                <input type="text" name="event_name" id="edit_event_name" required>
            </div>
            <div class="form-group">
                <label for="edit_event_date">Event Date</label>
                <input type="date" name="event_date" id="edit_event_date" required>
            </div>
            <div style="display: flex; gap: var(--space-md); margin-top: var(--space-md);">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="flex: 1;">
                    Cancel
                </button>
                <button type="submit" class="btn" style="flex: 1;">
                    <i class="fas fa-check"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="text-align: center;">
        <div style="width: 64px; height: 64px; background: var(--color-danger-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg);">
            <i class="fas fa-trash" style="font-size: 1.5rem; color: var(--color-danger);"></i>
        </div>
        <h2 style="margin-bottom: var(--space-sm);">Delete Client?</h2>
        <p style="margin-bottom: var(--space-xl);">This action cannot be undone. All files will be permanently deleted.</p>
        <div style="display: flex; gap: var(--space-md);">
            <button class="btn btn-secondary" onclick="closeDeleteModal()" style="flex: 1;">
                Cancel
            </button>
            <button class="btn btn-danger" onclick="executeDelete()" style="flex: 1;">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </div>
    </div>
</div>

<script>
let clientToDelete = null;

function openClientModal() {
    document.getElementById("createClientModal").style.display = "flex";
}

function closeClientModal() {
    document.getElementById("createClientModal").style.display = "none";
}

function openEditModal(folderName, clientName, eventName, eventDate) {
    document.getElementById("edit_folder_name").value = folderName;
    document.getElementById("edit_client_name").value = clientName;
    document.getElementById("edit_event_name").value = eventName;
    document.getElementById("edit_event_date").value = eventDate;
    document.getElementById("editClientModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editClientModal").style.display = "none";
}

function confirmDeleteClient(folderName) {
    clientToDelete = folderName;
    document.getElementById("deleteConfirmModal").style.display = "flex";
}

function closeDeleteModal() {
    document.getElementById("deleteConfirmModal").style.display = "none";
    clientToDelete = null;
}

function executeDelete() {
    if (!clientToDelete) return;

    fetch("controllers/AdminController.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "delete_client=1&folder_name=" + encodeURIComponent(clientToDelete)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate row removal
            const row = document.getElementById("row-" + clientToDelete);
            if (row) {
                row.style.transition = "all 0.3s ease";
                row.style.opacity = "0";
                row.style.transform = "translateX(-20px)";
                setTimeout(() => location.reload(), 300);
            } else {
                location.reload();
            }
        } else {
            alert("Error: " + data.message);
        }
        closeDeleteModal();
    })
    .catch(error => {
        alert("Error deleting client");
        closeDeleteModal();
    });
}

// Close modals on outside click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
});
</script>
