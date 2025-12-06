<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user"]) || !in_array($_SESSION["user"]["role"], ["admin", "superadmin"])) {
    header("Location: home.php");
    exit;
}

$conn = getDB();
$loggedRole = $_SESSION["user"]["role"];

// Fetch all users
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

<?php include "includes/layout.php"; ?>

<div class="main-content">

    <h1 class="text-3xl font-bold mb-6">User Management</h1>

    <!-- Table -->
    <div class="bg-white p-6 rounded-xl shadow">
        <table class="w-full border-collapse" id="userTable">
            <thead>
                <tr class="border-b text-gray-600">
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
                <tr id="row-<?= $u['id'] ?>" class="border-b hover:bg-gray-50">
                    <td class="p-3"><?= htmlspecialchars($u["name"]) ?></td>
                    <td class="p-3"><?= htmlspecialchars($u["email"]) ?></td>
                    <td class="p-3"><?= htmlspecialchars($u["username"]) ?></td>

                    <td class="p-3">
                        <?php if ($u["role"] === "superadmin"): ?>
                            <span class="px-3 py-1 bg-purple-200 text-purple-700 rounded-lg text-sm">Superadmin</span>
                        <?php elseif ($u["role"] === "admin"): ?>
                            <span class="px-3 py-1 bg-blue-200 text-blue-700 rounded-lg text-sm">Admin</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg text-sm">Staff</span>
                        <?php endif; ?>
                    </td>

                    <td class="p-3 text-gray-500 text-sm"><?= $u["created_at"] ?></td>

                    <td class="p-3 text-center flex gap-2 justify-center">

                        <!-- Edit Button -->
                        <button 
                            onclick="openEditModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['name']) ?>', '<?= htmlspecialchars($u['email']) ?>', '<?= htmlspecialchars($u['username']) ?>', '<?= $u['role'] ?>')"
                            class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">
                            Edit
                        </button>

                        <!-- Delete Button -->
                        <button 
                            onclick="openDeleteModal(<?= $u['id'] ?>)"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                            Delete
                        </button>

                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<!-- ========================= -->
<!-- EDIT USER MODAL -->
<!-- ========================= -->
<div id="editModal" class="hidden fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-xl w-96 shadow-xl">

        <h2 class="text-xl font-bold mb-4">Edit User</h2>

        <form id="editForm">
            <input type="hidden" name="id" id="edit_id">

            <label class="block font-semibold mb-1">Name</label>
            <input id="edit_name" name="name" class="w-full p-2 border rounded mb-3">

            <label class="block font-semibold mb-1">Email</label>
            <input id="edit_email" name="email" class="w-full p-2 border rounded mb-3">

            <label class="block font-semibold mb-1">Username</label>
            <input id="edit_username" name="username" class="w-full p-2 border rounded mb-3">

            <label class="block font-semibold mb-1">New Password</label>
            <input id="edit_password" name="password" type="password" class="w-full p-2 border rounded mb-3">

            <!-- Role field only for superadmin -->
            <?php if ($loggedRole === "superadmin"): ?>
            <label class="block font-semibold mb-1">Role</label>
            <select id="edit_role" name="role" class="w-full p-2 border rounded mb-4">
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Superadmin</option>
            </select>
            <?php endif; ?>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>

    </div>
</div>


<!-- ========================= -->
<!-- DELETE CONFIRM MODAL -->
<!-- ========================= -->
<div id="deleteModal" class="hidden fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-xl w-80 shadow-xl text-center">

        <h2 class="text-xl font-bold mb-4 text-red-600">Delete User?</h2>

        <p class="mb-4 text-gray-700">This action cannot be undone.</p>

        <input type="hidden" id="delete_id">

        <div class="flex justify-center gap-2">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>

            <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded">
                Delete
            </button>
        </div>

    </div>
</div>

<script>
// -------------------
// OPEN EDIT MODAL
// -------------------
function openEditModal(id, name, email, username, role) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_name").value = name;
    document.getElementById("edit_email").value = email;
    document.getElementById("edit_username").value = username;
    document.getElementById("edit_role")?.value = role;

    document.getElementById("editModal").classList.remove("hidden");
}

// -------------------
function closeEditModal() {
    document.getElementById("editModal").classList.add("hidden");
}

// -------------------
// SUBMIT EDIT FORM (AJAX)
// -------------------
document.getElementById("editForm").onsubmit = async function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    let res = await fetch("ajax_edit_user.php", {
        method: "POST",
        body: formData
    });

    let data = await res.json();

    if (data.success) {
        location.reload();
    } else {
        alert("Error: " + data.error);
    }
};

// -------------------
// DELETE USER
// -------------------
function openDeleteModal(id) {
    document.getElementById("delete_id").value = id;
    document.getElementById("deleteModal").classList.remove("hidden");
}

function closeDeleteModal() {
    document.getElementById("deleteModal").classList.add("hidden");
}

async function confirmDelete() {
    let id = document.getElementById("delete_id").value;

    let res = await fetch("ajax_delete_user.php?id=" + id);
    let data = await res.json();

    if (data.success) {
        document.getElementById("row-" + id).remove();
        closeDeleteModal();
    } else {
        alert("Error: " + data.error);
    }
}
</script>

</body>
</html>
