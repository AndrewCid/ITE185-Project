// src/components/UserEditModal.jsx
import React, { useState, useEffect } from "react";
import { updateUser, getUser } from "../api/usersApi";

export default function UserEditModal({ userData, onClose }) {
  const [user, setUser] = useState(userData);
  const isSuperadmin = userData?.currentUserRole === "superadmin";

  async function save() {
    const res = await updateUser(user);
    if (res.success) onClose();
    else alert(res.error || "Failed to update");
  }

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50">
      <div className="bg-[#111] p-6 rounded-xl border border-gray-700 w-96">
        <h2 className="text-xl mb-4 font-semibold">Edit User</h2>

        <div className="space-y-4">

          <input
            className="w-full p-2 bg-[#222] rounded-lg border border-gray-700"
            value={user.name}
            onChange={(e) => setUser({ ...user, name: e.target.value })}
          />

          <input
            className="w-full p-2 bg-[#222] rounded-lg border border-gray-700"
            value={user.email}
            onChange={(e) => setUser({ ...user, email: e.target.value })}
          />

          <input
            className="w-full p-2 bg-[#222] rounded-lg border border-gray-700"
            value={user.username}
            onChange={(e) => setUser({ ...user, username: e.target.value })}
          />

          {/* Role dropdown */}
          <select
            disabled={userData.currentUserRole !== "superadmin"}
            className="w-full p-2 bg-[#222] rounded-lg border border-gray-700 disabled:opacity-40"
            value={user.role}
            onChange={(e) => setUser({ ...user, role: e.target.value })}
          >
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
            <option value="superadmin">Superadmin</option>
          </select>

        </div>

        <div className="flex justify-end mt-6 space-x-3">
          <button className="px-4 py-2 bg-gray-600 rounded-lg" onClick={onClose}>
            Cancel
          </button>

          <button className="px-4 py-2 bg-blue-600 rounded-lg" onClick={save}>
            Save
          </button>
        </div>
      </div>
    </div>
  );
}
