<?php
require_once __DIR__ . '/../models/AdminModel.php';

$adminModel = new AdminModel();
$clients = $adminModel->getClients();
?>

<h1>Admin Dashboard</h1>

<!-- Clients Table -->
<table>
    <thead>
        <tr>
            <th>Client</th>
            <th>Event</th>
            <th>Event Date</th>
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
            <tr id="row-<?php echo $client['folder_name']; ?>">
                <td>
                    <a href="controllers/AuthController.php?auto_login=true&client=<?php echo urlencode($client['folder_name']); ?>">
                        <?php echo htmlspecialchars($client['client_name']); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($client['event_name']); ?></td>
                <td><?php echo htmlspecialchars($client['event_date']); ?></td>
                <td><?php echo htmlspecialchars($client['event_code']); ?></td>
                <td><?php echo $fileInfo['count']; ?></td>
                <td><?php echo $fileInfo['last_edit']; ?></td>
                <td class="table-actions">
                    <button class="btn-action edit-btn" 
                        onclick="openEditModal('<?php echo $client['folder_name']; ?>', 
                                               '<?php echo htmlspecialchars($client['client_name']); ?>', 
                                               '<?php echo htmlspecialchars($client['event_name']); ?>', 
                                               '<?php echo $client['event_date']; ?>')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>

                    <button class="btn-action delete-btn" 
                        onclick="confirmDeleteClient('<?php echo $client['folder_name']; ?>')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal for Adding/Editing Clients -->
<!-- Edit Client Modal -->
<div id="editClientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Client</h2>
        <form id="editClientForm" method="post" action="controllers/AdminController.php">
            <input type="hidden" name="folder_name" id="edit_folder_name">
            
            <label>Client Name</label>
            <input type="text" name="client_name" id="edit_client_name" required>
            
            <label>Event Name</label>
            <input type="text" name="event_name" id="edit_event_name" required>
            
            <label>Event Date</label>
            <input type="date" name="event_date" id="edit_event_date" required>
            
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(folderName, clientName, eventName, eventDate) {
    document.getElementById("edit_folder_name").value = folderName;
    document.getElementById("edit_client_name").value = clientName;
    document.getElementById("edit_event_name").value = eventName;
    document.getElementById("edit_event_date").value = eventDate;
    document.getElementById("editClientModal").style.display = "block";
}

function closeEditModal() {
    document.getElementById("editClientModal").style.display = "none";
}

function confirmDeleteClient(folderName) {
    if (confirm("Are you sure you want to delete this client?")) {
        fetch("controllers/AdminController.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "delete_client=1&folder_name=" + encodeURIComponent(folderName)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Client deleted successfully!");
                location.reload(); // Reload page to update the client table
            } else {
                alert("Error deleting client: " + data.message);
            }
        });
    }
}
</script>

<?php include __DIR__ . "/footer.php"; ?>