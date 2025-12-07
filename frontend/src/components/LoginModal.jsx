// src/components/LoginModal.jsx
import React, { useState } from "react";
import { loginUser } from "../api/authApi";

export default function LoginModal({ onLoginSuccess }) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  async function handleLogin(e) {
    e.preventDefault();

    const result = await loginUser(username, password);

    if (result.status === "success") {
      onLoginSuccess(result.user);
    } else {
      setError("Invalid username or password.");
    }
  }

  return (
    <div className="fixed inset-0 flex items-center justify-center backdrop-blur-md bg-black/40 z-50">
      <div className="bg-[#181818] p-8 rounded-xl shadow-xl w-80 text-white animate-fadeIn">
        <h2 className="text-2xl font-bold mb-4 text-center">Login</h2>

        <form onSubmit={handleLogin}>
          <input
            type="text"
            placeholder="Username"
            className="w-full p-2 mb-3 rounded bg-[#222] border border-gray-700"
            onChange={(e) => setUsername(e.target.value)}
          />

          <input
            type="password"
            placeholder="Password"
            className="w-full p-2 mb-3 rounded bg-[#222] border border-gray-700"
            onChange={(e) => setPassword(e.target.value)}
          />

          <button
            className="w-full bg-blue-600 hover:bg-blue-700 p-2 rounded mt-2 font-semibold"
            type="submit"
          >
            Login
          </button>

          {error && (
            <p className="text-red-400 text-sm text-center mt-3">{error}</p>
          )}
        </form>
      </div>
    </div>
  );
}
