// src/components/DashboardCard.jsx
export default function DashboardCard({ title, children }) {
  return (
    <div className="bg-[#141414] p-6 border border-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition">
      <h3 className="text-lg font-semibold mb-3">{title}</h3>
      <div className="text-gray-300">{children}</div>
    </div>
  );
}
