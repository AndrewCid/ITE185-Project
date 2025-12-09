// src/components/graph/GraphNodeMarker.jsx
import React from "react";
import { Marker, Tooltip } from "react-leaflet";
import L from "leaflet";

/**
 * Renders a colored circle marker using DivIcon for crisp color control.
 * props:
 *  - node: { key, label, lat, lng, meta }
 *  - onClick, onContextMenu, draggable, onDragend
 */

function createIcon(color, label) {
  const html = `
    <div style="
      display:flex; align-items:center; justify-content:center;
      width:30px; height:30px; border-radius:50%;
      background:${color}; color:#000; font-weight:700;
      box-shadow: 0 2px 6px rgba(0,0,0,0.5);
    ">
      <span style="font-size:12px; color:white">${label}</span>
    </div>
  `;
  return L.divIcon({
    html,
    className: "",
    iconSize: [30, 30],
    iconAnchor: [15, 15],
  });
}

export default function GraphNodeMarker({
  node,
  color = "#38bdf8",
  onClick,
  onContextMenu,
  draggable = false,
  onDragend,
}) {
  return (
    <Marker
      key={node.key}
      position={[node.lat, node.lng]}
      icon={createIcon(color, node.key)}
      draggable={draggable}
      eventHandlers={{
        click: (e) => {
          if (onClick) onClick(node, e);
        },
        contextmenu: (e) => {
          if (onContextMenu) onContextMenu(node, e);
        },
        dragend: (e) => {
          const latlng = e.target.getLatLng();
          if (onDragend) onDragend(node, latlng);
        },
      }}
    >
      <Tooltip direction="top" offset={[0, -10]} opacity={0.9}>
        <div className="text-sm">
          <div className="font-semibold">{node.label || node.key}</div>
        </div>
      </Tooltip>
    </Marker>
  );
}
