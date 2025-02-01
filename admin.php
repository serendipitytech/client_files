<?php
require "config.php";
if (!isAdminLoggedIn()) {
    header("Location: admin_login.php");
    exit();
}

$clientsFile = "clients.json";
$clients = file_exists($clientsFile) ? json_decode(file_get_contents($clientsFile), true) : [];
if (!is_array($clients)) {
    $clients = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Clients</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function openModal() {
            document.getElementById("clientModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("clientModal").style.display = "none";
        }

        function submitClientForm(event) {
            event.preventDefault();

            let formData = new FormData(document.getElementById("clientForm"));
            fetch("admin_add_client.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let newRow = `<tr>
                        <td>${data.client.client_name}</td>
                        <td>${data.client.event_name}</td>
                        <td>${data.client.event_date}</td>
                        <td>${data.client.event_code}</td>
                        <td>${data.client.folder_name}</td>
                    </tr>`;
                    document.getElementById("clientTableBody").innerHTML += newRow;
                    closeModal();
                } else {
                    alert("Error adding client: " + data.error);
                }
            })
            .catch(error => alert("Request failed: " + error));
        }
    </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="menu">
        <h2>Admin Menu</h2>
        <button onclick="openModal()" class="btn">+ New Client</button>
    </div>
    <a href="logout.php" class="btn logout-btn">Logout</a>
</div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Client List</h1>

        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Event Code</th>
                    <th>Folder</th>
                </tr>
            </thead>
            <tbody id="clientTableBody">
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($client['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($client['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($client['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($client['event_code']); ?></td>
                        <td><?php echo htmlspecialchars($client['folder_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Popup Modal -->
    <div id="clientModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add Client</h2>
            <form id="clientForm" onsubmit="submitClientForm(event)">
                <input type="text" name="client_name" placeholder="Client Name" required>
                <input type="text" name="event_name" placeholder="Event Name" required>
                <input type="date" name="event_date" required>
                <button type="submit">Create Client</button>
            </form>
        </div>
    </div>

</body>
</html>