// src/pages/Home.jsx
import React, { useState, useEffect } from "react";
import Sidebar from "../components/Sidebar";
import Topbar from "../components/Topbar";
import DashboardCard from "../components/DashboardCard";
import { getCurrentUser } from "../api/authApi";

export default function Home({ user: initialUser }) {
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [user, setUser] = useState(initialUser);

  useEffect(() => {
    if (!initialUser) {
      getCurrentUser().then((u) => setUser(u));
    }
  }, []);

  return (
    <div className="flex min-h-screen bg-[#0d0d0d] text-white">
      {/* SIDEBAR */}
      <Sidebar user={user} isOpen={sidebarOpen} />

      {/* MAIN CONTENT */}
      <div className={`flex-1 transition-all duration-300 ${sidebarOpen ? "ml-64" : "ml-0"}`}>
        <Topbar onMenuClick={() => setSidebarOpen(!sidebarOpen)} />

        <div className="p-8 space-y-8">
          
          {/* TOP CARDS */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <DashboardCard title="Most Recent Project">
              <p>No projects yet.</p>
            </DashboardCard>

            <DashboardCard title="Start New Project">
              <button className="bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">
                + Create
              </button>
            </DashboardCard>

            <DashboardCard title="User Profile">
              {user && (
                <>
                  <p className="font-medium">{user.username}</p>
                  <p className="text-sm text-gray-400">{user.role}</p>
                </>
              )}
            </DashboardCard>
          </div>

          {/* NOTIFICATIONS */}
          <DashboardCard title="Recent Notifications">
            <ul className="space-y-2 text-gray-300">
              <li>📧 System initialized successfully.</li>
              <li>🔥 New update available soon.</li>
            </ul>
          </DashboardCard>
        </div>
      </div>
    </div>
  );
}
