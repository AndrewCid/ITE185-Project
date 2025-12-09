// src/components/graph/GraphContextMenu.jsx
import React from "react";

/**
 * position: {x, y} in client coords
 * type: "node" | "edge" | "empty"
 * data: node or edge object
 * callback props for actions
 */
export default function GraphContextMenu({
  position,
  type,
  data,
  onEditNode,
  onMoveNode,
  onDeleteNode,
  onEditEdge,
  onChangeEdgeConnection,
  onDeleteEdge,
  onClose,
}) {
  if (!position) return null;

  const style = {
    top: position.y,
    left: position.x,
  };

  return (
    <div
         className="graph-context-menu ui-layer bg-[#111] text-white border border-gray-700 rounded-lg shadow p-2 absolute z-[6000]"
         style={{
         position: "absolute",
         top: position.y,
         left: position.x,
        pointerEvents: "auto"
        }}

      onClick={(e) => e.stopPropagation()}
    >
      {type === "node" && (
        <div>
          <div className="px-2 py-1 text-gray-300 font-semibold">Node {data?.key}</div>

          <button
            className="block w-full text-left px-2 py-1 hover:bg-gray-800 rounded"
            onClick={() => { onEditNode?.(data); onClose(); }}
          >
            ✏ Edit (label/type)
          </button>

          <button
            className="block w-full text-left px-2 py-1 hover:bg-gray-800 rounded"
            onClick={() => { onMoveNode?.(data); onClose(); }}
          >
            🖐 Move Node
          </button>

          <button
            className="block w-full text-left px-2 py-1 text-red-400 hover:bg-gray-800 rounded"
            onClick={() => { onDeleteNode?.(data.key); onClose(); }}
          >
            🗑 Delete Node
          </button>
        </div>
      )}

      {type === "edge" && (
        <div>
          <div className="px-2 py-1 text-gray-300 font-semibold">Edge {data?.from} → {data?.to}</div>

          <button
            className="block w-full text-left px-2 py-1 hover:bg-gray-800 rounded"
            onClick={() => { onEditEdge?.(data); onClose(); }}
          >
            ✏ Edit Weight
          </button>

          <button
            className="block w-full text-left px-2 py-1 hover:bg-gray-800 rounded"
            onClick={() => { onChangeEdgeConnection?.(data); onClose(); }}
          >
            🔗 Change Connection
          </button>

          <button
            className="block w-full text-left px-2 py-1 text-red-400 hover:bg-gray-800 rounded"
            onClick={() => { onDeleteEdge?.(data); onClose(); }}
          >
            🗑 Delete Edge
          </button>
        </div>
      )}

      {type === "empty" && (
        <div className="px-2 py-1 text-gray-400">Right-click a node or edge.</div>
      )}

      <div className="mt-2 border-t border-gray-800 pt-2">
        <button className="w-full text-left px-2 py-1 text-gray-400 hover:bg-gray-800 rounded" onClick={onClose}>
          ✖ Close
        </button>
      </div>
    </div>
  );
}
