<div class="sidebar">
    <div class="menu">
        <h2>Admin Menu</h2>
        <button onclick="openModal()" class="btn">New Client</button>
        <form action="admin.php" method="get" style="width: 100%;">
            <input type="hidden" name="show_past" value="<?php echo $showPastEvents ? 'false' : 'true'; ?>">
            <button type="submit" class="btn" style="width: 100%;">
                <?php echo $showPastEvents ? 'Show Upcoming Events' : 'Show Past Events'; ?>
            </button>
        </form>
        <form action="admin.php" method="get" style="width: 100%;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn logout-btn" style="width: 100%;">Logout</button>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1>Client List</h1>

    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Event</th>
                <th>Event Date</th>
                <th>Event Code</th>
                <th>Files</th>
                <th>Last Edit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="clientTableBody">
            <?php foreach ($filteredClients as $client): 
                $fileInfo = getClientFileInfo($client['folder_name']);
            ?>
                <tr id="row-<?php echo $client['folder_name']; ?>">
                    <td>
                        <a href="dashboard.php?client=<?php echo urlencode($client['folder_name']); ?>&admin_access=true" target="_blank">
                            <?php echo htmlspecialchars($client['client_name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($client['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($client['event_date']); ?></td>
                    <td><?php echo htmlspecialchars($client['event_code']); ?></td>
                    <td><?php echo $fileInfo['count']; ?></td>
                    <td><?php echo $fileInfo['last_edit']; ?></td>
                    <td class="table-actions">
                        <button class="btn-action edit-btn" onclick="editClient('<?php echo $client['folder_name']; ?>', '<?php echo htmlspecialchars($client['client_name']); ?>', '<?php echo htmlspecialchars($client['event_name']); ?>', '<?php echo $client['event_date']; ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <i class="fas fa-question-circle help-icon" onclick="toggleHelp()"></i>
        <div class="help-content" id="helpContent">
            <h3>Help Information</h3>
            <p>Here you can manage clients, view file counts, and see the last edit times for each client's files.</p>
            <p>To add a new client, fill out the client details and click "Add Client".</p>
            <p>To edit an existing client, click the "Edit" button next to the client you want to edit.</p>
            <p>To delete a client, click the "Delete" button next to the client you want to remove.</p>
        </div>
</div>

<!-- Modal for Adding/Editing Clients -->
<div id="clientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Client</h2>
        <form id="clientForm" method="post" action="admin.php">
            <input type="hidden" name="folder_name" id="folder_name">
            <input type="text" name="client_name" id="client_name" placeholder="Client Name" required>
            <input type="text" name="event_name" id="event_name" placeholder="Event Name" required>
            <input type="date" name="event_date" id="event_date" required>
            <button type="submit" class="btn">Save</button>
        </form>
        <form id="deleteForm" method="post" action="admin.php">
            <input type="hidden" name="folder_name" id="delete_folder_name">
            <button type="submit" name="delete_client" class="btn delete-btn">Delete</button>
        </form>
    </div>
</div>

<script>
 //Open Help
 function toggleHelp() {
            var helpContent = document.getElementById("helpContent");
            if (helpContent.style.display === "none" || helpContent.style.display === "") {
                helpContent.style.display = "block";
            } else {
                helpContent.style.display = "none";
            }
        }

// Open/Close Modal
function openModal() { 
    document.getElementById("clientModal").style.display = "flex"; 
    document.getElementById("modalTitle").innerText = "Add Client";
    document.getElementById("clientForm").reset();
    document.getElementById("deleteForm").style.display = "none";
}
function closeModal() { document.getElementById("clientModal").style.display = "none"; }

// Edit Client
function editClient(folderName, clientName, eventName, eventDate) {
    document.getElementById("clientModal").style.display = "flex";
    document.getElementById("modalTitle").innerText = "Edit Client";
    document.getElementById("folder_name").value = folderName;
    document.getElementById("client_name").value = clientName;
    document.getElementById("event_name").value = eventName;
    document.getElementById("event_date").value = eventDate;
    document.getElementById("delete_folder_name").value = folderName;
    document.getElementById("deleteForm").style.display = "block";
}
</script>