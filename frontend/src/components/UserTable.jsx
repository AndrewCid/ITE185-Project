import React from "react";

export default function UserTable({
  users,
  sortCol,
  sortOrder,
  setSortCol,
  setSortOrder,
  onEdit,
  onDelete,
  currentUserRole
}) {
  function toggleSort(column) {
    if (sortCol === column) {
      setSortOrder(sortOrder === "ASC" ? "DESC" : "ASC");
    } else {
      setSortCol(column);
      setSortOrder("ASC");
    }
  }

  const canModify = currentUserRole === "admin" || currentUserRole === "superadmin";

  return (
    <table className="w-full border-collapse bg-[#111] border border-gray-800 rounded-xl overflow-hidden">
      <thead>
        <tr className="bg-[#161616] text-gray-300">
          <th className="p-3 cursor-pointer" onClick={() => toggleSort("id")}>ID</th>
          <th className="p-3 cursor-pointer" onClick={() => toggleSort("name")}>Name</th>
          <th className="p-3 cursor-pointer" onClick={() => toggleSort("email")}>Email</th>
          <th className="p-3 cursor-pointer" onClick={() => toggleSort("username")}>Username</th>
          <th className="p-3 cursor-pointer" onClick={() => toggleSort("role")}>Role</th>
          <th className="p-3 cursor-pointer" onClick={() => toggleSort("created_at")}>Created</th>
          {canModify && <th className="p-3 text-center">Actions</th>}
        </tr>
      </thead>

      <tbody>
        {users.map((u) => (
          <tr key={u.id} className="border-t border-gray-800 hover:bg-[#1b1b1b] transition">
            <td className="p-3">{u.id}</td>
            <td className="p-3">{u.name}</td>
            <td className="p-3">{u.email}</td>
            <td className="p-3">{u.username}</td>
            <td className="p-3">
              {u.role === "superadmin" && (
                <span className="px-3 py-1 bg-purple-600/20 text-purple-400 rounded-full text-sm">
                  Superadmin
                </span>
              )}
              {u.role === "admin" && (
                <span className="px-3 py-1 bg-blue-600/20 text-blue-400 rounded-full text-sm">
                  Admin
                </span>
              )}
              {u.role === "staff" && (
                <span className="px-3 py-1 bg-gray-600/20 text-gray-300 rounded-full text-sm">
                  Staff
                </span>
              )}
            </td>
            <td className="p-3 text-gray-400">{u.created_at}</td>

            {/* ACTION BUTTONS SECTION */}
            {canModify && (
              <td className="p-3 flex gap-2 justify-center">
                {/* EDIT BUTTON */}
                {onEdit && (
                  <button
                    className="px-3 py-1 bg-yellow-500 text-black rounded-lg hover:bg-yellow-600 text-sm"
                    onClick={() => onEdit(u)}
                  >
                    Edit
                  </button>
                )}

                {/* DELETE BUTTON */}
                {onDelete && u.role !== "superadmin" && (
                  <button
                    className="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm"
                    onClick={() => onDelete(u.id)}
                  >
                    Delete
                  </button>
                )}
              </td>
            )}
          </tr>
        ))}
      </tbody>
    </table>
  );
}
