// src/App.jsx
import React, { useEffect, useState } from "react";
import { getCurrentUser } from "./api/authApi";
import LoginModal from "./components/LoginModal";
import Home from "./pages/Home";

function App() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // check existing session
  useEffect(() => {
    getCurrentUser().then((u) => {
      setUser(u);
      setLoading(false);
    });
  }, []);

  if (loading) return null;

  return (
    <>
      {!user && (
        <LoginModal onLoginSuccess={(u) => setUser(u)} />
      )}
      {user && <Home user={user} />}
    </>
  );
}

export default App;
