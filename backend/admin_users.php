<?php
session_start();
if (!isset($_SESSION["user"]) || !in_array($_SESSION["user"]["role"], ["admin", "superadmin"])) {
    header("Location: home.php");
    exit;
}

require_once "includes/db.php";
$conn = getDB();

// Fetch users
$stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Database</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Page Container -->
    <div class="flex">
        <!-- SIDEBAR (Optional later) -->
        
        <!-- MAIN CONTENT -->
        <div class="flex-1 p-8">

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
                <p class="text-gray-500">Admins and superadmins can view and manage user accounts.</p>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search users..." 
                    class="w-full p-3 border rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    onkeyup="filterTable()"
                >
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                
                <table class="w-full border-collapse" id="userTable">
                    <thead>
                        <tr class="border-b text-left text-gray-600">
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Username</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Created</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-3 font-medium"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($u['username']) ?></td>

                                <!-- Role Badge -->
                                <td class="p-3">
                                    <?php if ($u['role'] === "superadmin"): ?>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Superadmin</span>
                                    <?php elseif ($u['role'] === "admin"): ?>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Admin</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm">Staff</span>
                                    <?php endif; ?>
                                </td>

                                <td class="p-3 text-sm text-gray-500">
                                    <?= $u['created_at'] ?>
                                </td>

                                <!-- Actions -->
                                <td class="p-3 text-center flex gap-2">

                                    <a href="edit_user.php?id=<?= $u['id'] ?>"
                                       class="px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 text-sm">
                                        Edit
                                    </a>

                                    <a href="delete_user.php?id=<?= $u['id'] ?>"
                                       onclick="return confirm('Delete this user?');"
                                       class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                        Delete
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

<script>
function filterTable() {
    let query = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#userTable tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(query) ? "" : "none";
    });
}
</script>

</body>
</html>
