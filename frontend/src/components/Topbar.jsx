// src/components/Topbar.jsx
export default function Topbar({ onMenuClick }) {
  return (
    <div className="h-16 flex items-center px-6 bg-[#111] border-b border-gray-800 shadow-lg">
      <button
        onClick={onMenuClick}
        className="text-2xl mr-4 text-gray-300 hover:text-white transition"
      >
        ☰
      </button>

      <h1 className="text-xl font-semibold tracking-wide">
        Water Distribution Network Dashboard
      </h1>
    </div>
  );
}
