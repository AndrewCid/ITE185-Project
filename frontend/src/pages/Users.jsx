    // src/pages/Users.jsx
    import React, { useState, useEffect } from "react";
    import Sidebar from "../components/Sidebar";
    import UserTable from "../components/UserTable";
    import UserEditModal from "../components/UserEditModal";
    import UserDeleteModal from "../components/UserDeleteModal";
    import AddUserModal from "../components/UserAddModal";
    import { fetchUsers } from "../api/usersApi";
    
    

    export default function Users({ user, setUser }) {
    const [createOpen, setCreateOpen] = useState(false);
    const [users, setUsers] = useState([]);
    const [sortCol, setSortCol] = useState("created_at");
    const [sortOrder, setSortOrder] = useState("DESC");
    const [search, setSearch] = useState("");
    const [date, setDate] = useState("");

    const [editUser, setEditUser] = useState(null);
    const [deleteUser, setDeleteUser] = useState(null);
    const [showAddModal, setShowAddModal] = useState(false);

    function reload() {
        fetchUsers(sortCol, sortOrder, search, date).then(setUsers);
    }

    useEffect(() => {
        reload();
    }, [sortCol, sortOrder, search, date]);

    const isAdmin = user.role === "admin" || user.role === "superadmin";

    return (
        <div className="flex bg-[#0d0d0d] text-white min-h-screen">
        <Sidebar user={user} setUser={setUser} isOpen={true} />

        <div className="flex-1 ml-64 p-10">
            <h1 className="text-3xl font-bold mb-6">User Management</h1>
            
            {/* Create User Button */}
            <div className="flex items-center justify-between mb-6">
                <button
                   className="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg"
                   onClick={() => setShowAddModal(true)}
                >
                    + Create User
                </button>

            {/* Search Bar */}
             <div className="flex gap-4">
                <input
                    type="text"
                    placeholder="Search name, username, email…"
                    className="px-4 py-2 bg-[#111] border border-gray-700 rounded-lg w-80"
                    onChange={(e) => setSearch(e.target.value)}
                />

                <input
                    type="date"
                    className="px-4 py-2 bg-[#111] border border-gray-700 rounded-lg"
                    onChange={(e) => setDate(e.target.value)}
                />
            </div>
            </div>

            <UserTable
            users={users}
            sortCol={sortCol}
            sortOrder={sortOrder}
            setSortCol={setSortCol}
            setSortOrder={setSortOrder}
            onEdit={isAdmin ? setEditUser : null}
            onDelete={isAdmin ? setDeleteUser : null}
            currentUserRole={user.role}
            />

            {editUser && (
            <UserEditModal
                userData={{ ...editUser, currentUserRole: user.role }}
                onClose={() => {
                setEditUser(null);
                reload();
                }}
            />
            )}

            {deleteUser && (
            <UserDeleteModal
                userId={deleteUser}
                onClose={() => {
                setDeleteUser(null);
                reload();
                }}
            />
            )}
            {showAddModal && (
              <AddUserModal
                onClose={() => setShowAddModal(false)}
                onSuccess={reload}
            />
            )}
        </div>
        </div>
    );
    }
