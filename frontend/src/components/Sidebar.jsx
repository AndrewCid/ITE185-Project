// src/components/Sidebar.jsx
import React from "react";
import { logoutUser } from "../api/authApi";
import { Link } from "react-router-dom";

export default function Sidebar({ user, isOpen, onLogout }) {

  async function handleLogout() {
    await logoutUser();  // destroy PHP session
    onLogout();          // tell App to hide home + show login modal
  }

  const role = user?.role || "guest";

  return (
    <aside
      className={`fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#111] to-[#0a0a0a] border-r border-gray-800
      text-white shadow-xl transition-transform duration-300 z-40
      ${isOpen ? "translate-x-0" : "-translate-x-64"}`}
    >
      <div className="p-6 border-b border-gray-800">
        <h2 className="text-xl font-bold">{user?.username}</h2>
        <p className="text-sm text-gray-400 mt-1">{role.toUpperCase()}</p>
      </div>

      <nav className="p-5 text-gray-300 space-y-4">
        <a className="block hover:text-white transition" href="/">🏠 Dashboard</a>
        <a className="block hover:text-white transition" href="/map">🗺 Water Network</a>

        {(role === "admin" || role === "superadmin") && (
          <>
            <Link to="/users" className="block hover:text-white transition">
            👤 Users
            </Link>

            <a className="block hover:text-white transition" href="/projects">📁 Projects</a>
          </>
        )}

        <button
          onClick={handleLogout}
          className="block text-red-400 hover:text-red-300 mt-6"
        >
          🚪 Logout
        </button>
      </nav>
    </aside>
  );
}
