// src/App.jsx
import React, { useEffect, useState } from "react";
import { getCurrentUser } from "./api/authApi";
import LoginModal from "./components/LoginModal";
import Home from "./pages/Home";
import "./index.css";

function App() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Check session when app loads
  useEffect(() => {
    getCurrentUser().then((u) => {
      setUser(u);
      setLoading(false);
    });
  }, []);

  // Prevent UI flicker while checking session
  if (loading) return null;

  const handleLoginSuccess = (u) => {
    setUser(u); // Save logged-in user
  };

  const handleLogout = () => {
    setUser(null); // Returning to login modal
  };

  return (
    <div className="relative min-h-screen bg-[#0d0d0d] text-white">
      {/* MAIN UI (Home) */}
      <Home user={user} setUser={setUser} />

      {/* LOGIN POPUP */}
      {!user && (
        <div className="absolute inset-0 z-50 pointer-events-auto">
          <LoginModal onLoginSuccess={handleLoginSuccess} />
        </div>
      )}

      {/* BLUR + DIM BACKGROUND WHEN LOGGED OUT */}
      {!user && (
        <div className="absolute inset-0 bg-black/40 backdrop-blur-sm z-40 pointer-events-none"></div>
      )}
    </div>
  );
}

export default App;
