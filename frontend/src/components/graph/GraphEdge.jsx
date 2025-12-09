// src/components/graph/GraphEdge.jsx
import React from "react";
import { Polyline, Marker } from "react-leaflet";
import L from "leaflet";

export default function GraphEdge({
  edge,
  fromNode,
  toNode,
  color = "#06b6d4",
  isDirected = false,
  onContextMenu
}) {
  if (!fromNode || !toNode) return null;

  const positions = [
    [fromNode.lat, fromNode.lng],
    [toNode.lat, toNode.lng],
  ];

  // Create arrow marker (for directed graphs)
  const arrowIcon = L.divIcon({
    className: "",
    html: `<div style="font-size:20px; transform: rotate(0deg)">➤</div>`
  });

  return (
    <>
      {/* The edge line */}
      <Polyline
        pathOptions={{
          color,
          weight: Math.max(2, edge.weight || 1),
        }}
        positions={positions}
        eventHandlers={{
          contextmenu: (e) => onContextMenu?.(edge, e),
        }}
      />

      {/* Arrow at the end of line */}
      {isDirected && (
        <Marker
          position={[toNode.lat, toNode.lng]}
          icon={arrowIcon}
          eventHandlers={{
            contextmenu: (e) => onContextMenu?.(edge, e),
          }}
        />
      )}
    </>
  );
}
