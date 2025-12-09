import React, { useEffect, useState } from "react";
import { listGraphs, loadGraph, deleteGraph } from "../../api/graphApi";

export default function LoadGraphModal({ onClose, onSelectGraph }) {
  const [graphs, setGraphs] = useState([]);

  useEffect(() => {
    listGraphs().then((res) => {
      setGraphs(res.graphs || []);
    });
  }, []);

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div className="bg-[#111] p-6 rounded-xl w-[420px] max-h-[80vh] overflow-auto border border-gray-700">
        <h2 className="text-xl mb-4">Load Graph</h2>

        <ul className="space-y-3">
          {graphs.map((g) => (
            <li key={g.id} className="bg-[#222] p-3 rounded border border-gray-700 flex justify-between items-center">
              <div>
                <div className="font-semibold">{g.name}</div>
                <div className="text-gray-400 text-sm">
                  {new Date(g.created_at).toLocaleString()}
                </div>
              </div>

              <div className="flex gap-2">
                <button
                  className="px-2 py-1 bg-green-600 rounded"
                  onClick={() => {
                    loadGraph(g.id).then((res) => {
                      onSelectGraph(res); 
                    });
                  }}
                >
                  Load
                </button>

                <button
                  className="px-2 py-1 bg-red-600 rounded"
                  onClick={() => {
                    deleteGraph(g.id).then(() => {
                      setGraphs(graphs.filter((x) => x.id !== g.id));
                    });
                  }}
                >
                  Del
                </button>
              </div>
            </li>
          ))}
        </ul>

        <div className="flex justify-end mt-4">
          <button className="px-3 py-1 bg-gray-700 rounded" onClick={onClose}>
            Close
          </button>
        </div>
      </div>
    </div>
  );
}
