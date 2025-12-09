// src/components/AddUserModal.jsx
import React, { useState } from "react";
import { createUser } from "../api/usersApi";

export default function AddUserModal({ onClose, onSuccess }) {
  const [form, setForm] = useState({
    name: "",
    email: "",
    username: "",
    password: "",
    role: "staff",
  });

  const [error, setError] = useState("");

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");

    const res = await createUser(form);
    if (res.success) {
      onSuccess();
      onClose();
    } else {
      setError(res.message || "Failed to create user");
    }
  }

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50">

      <div className="bg-[#111] border border-gray-700 p-8 rounded-2xl w-full max-w-lg shadow-xl">
        <h2 className="text-xl font-bold mb-4">Create New User</h2>

        <form className="space-y-4" onSubmit={handleSubmit}>
          <input
            type="text"
            name="name"
            placeholder="Full name"
            value={form.name}
            onChange={handleChange}
            className="w-full px-4 py-2 bg-[#1a1a1a] border border-gray-700 rounded-lg"
            required
          />

          <input
            type="email"
            name="email"
            placeholder="Email"
            value={form.email}
            onChange={handleChange}
            className="w-full px-4 py-2 bg-[#1a1a1a] border border-gray-700 rounded-lg"
            required
          />

          <input
            type="text"
            name="username"
            placeholder="Username"
            value={form.username}
            onChange={handleChange}
            className="w-full px-4 py-2 bg-[#1a1a1a] border border-gray-700 rounded-lg"
            required
          />

          <input
            type="password"
            name="password"
            placeholder="Password"
            value={form.password}
            onChange={handleChange}
            className="w-full px-4 py-2 bg-[#1a1a1a] border border-gray-700 rounded-lg"
            required
          />

          {/* Only admins & superadmins can see this — backend will validate too */}
          <select
            name="role"
            value={form.role}
            onChange={handleChange}
            className="w-full px-3 py-2 bg-[#1a1a1a] border border-gray-700 rounded-lg"
          >
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
            <option value="superadmin">Super Admin</option>
          </select>

          {/* Error message */}
          {error && <p className="text-red-400 text-sm">{error}</p>}

          <div className="flex justify-end gap-3 mt-4">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 bg-gray-700 rounded-lg"
            >
              Cancel
            </button>

            <button
              type="submit"
              className="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg"
            >
              Create User
            </button>
          </div>
        </form>
      </div>

    </div>
  );
}
