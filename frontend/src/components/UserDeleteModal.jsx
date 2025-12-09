// src/components/UserDeleteModal.jsx
import React from "react";
import { deleteUser } from "../api/usersApi";

export default function UserDeleteModal({ userId, onClose }) {
  async function confirmDelete() {
    const res = await deleteUser(userId);
    if (res.success) onClose();
    else alert(res.error || "Delete failed");
  }

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50">
      <div className="bg-[#111] p-6 rounded-xl border border-gray-700 w-96 text-center">
        <h2 className="text-xl mb-4 font-semibold text-red-400">
          Confirm Delete
        </h2>

        <p className="text-gray-300 mb-6">Are you sure you want to delete this user?</p>

        <div className="flex justify-center space-x-4">
          <button className="px-4 py-2 bg-gray-600 rounded-lg" onClick={onClose}>
            Cancel
          </button>

          <button className="px-4 py-2 bg-red-600 rounded-lg" onClick={confirmDelete}>
            Delete
          </button>
        </div>
      </div>
    </div>
  );
}
