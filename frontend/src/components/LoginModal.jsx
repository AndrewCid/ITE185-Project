import React, { useState } from "react";
import { loginUser } from "../api/authApi";

export default function LoginModal({ onLoginSuccess }) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  async function handleLogin(e) {
    e.preventDefault();
    setError("");

    const res = await loginUser(username, password);

    if (res.success) {
      onLoginSuccess(res.user); // always pass actual user
    } else {
      setError(res.message || "Login failed");
    }
  }

  return (
    <div
      className="
        fixed inset-0 
        bg-black/60 backdrop-blur-md 
        flex items-center justify-center
        z-50
        animate-fadeIn
      "
    >
      {/* Modal container */}
      <div
        className="
          bg-neutral-900 border border-neutral-700 
          shadow-2xl rounded-2xl 
          w-full max-w-md p-8 
          animate-scaleIn
        "
      >
        <h2 className="text-2xl font-semibold text-white mb-6 text-center">
          Welcome Back
        </h2>

        <form onSubmit={handleLogin} className="space-y-4">

          <div>
            <label className="text-gray-300 text-sm">Username</label>
            <input
              type="text"
              className="
                w-full mt-1 px-3 py-2 
                bg-neutral-800 text-white 
                rounded-lg border border-neutral-700 
                focus:outline-none focus:ring-2 focus:ring-blue-500
              "
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              required
            />
          </div>

          <div>
            <label className="text-gray-300 text-sm">Password</label>
            <input
              type="password"
              className="
                w-full mt-1 px-3 py-2 
                bg-neutral-800 text-white 
                rounded-lg border border-neutral-700 
                focus:outline-none focus:ring-2 focus:ring-blue-500
              "
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          {error && (
            <div className="text-red-400 text-sm mt-2 text-center">{error}</div>
          )}

          <button
            type="submit"
            className="
              w-full py-2 mt-4 
              bg-blue-600 hover:bg-blue-700 
              text-white font-medium 
              rounded-lg transition
            "
          >
            Login
          </button>
        </form>
      </div>
    </div>
  );
}
