import React, { useEffect, useRef, useState } from "react";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

export default function GraphMap({ loadedGraph }) {
  const mapRef = useRef(null);
  const [map, setMap] = useState(null);

  const [nodes, setNodes] = useState([]);
  const [edges, setEdges] = useState([]);

  const [selectedNode, setSelectedNode] = useState(null);
  const [awaitingEdgeTo, setAwaitingEdgeTo] = useState(null);

  // generate node names: a, b, c, ..., z, aa, ab, ...
  function generateNodeName(index) {
    let name = "";
    while (index >= 0) {
      name = String.fromCharCode((index % 26) + 97) + name;
      index = Math.floor(index / 26) - 1;
    }
    return name;
  }

  // Initialize Leaflet map
  useEffect(() => {
    if (mapRef.current && !map) {
      const m = L.map(mapRef.current).setView([8.226, 124.245], 16);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
      }).addTo(m);

      m.on("click", onMapClick);

      setMap(m);
    }
  }, [map]);

  // Load saved graph
  useEffect(() => {
    if (loadedGraph && map) {
      setNodes(loadedGraph.nodes);
      setEdges(loadedGraph.edges);

      map.setView(loadedGraph.view.center, loadedGraph.view.zoom);
    }
  }, [loadedGraph, map]);

  // USER CLICKS MAP → ADD NODE
  function onMapClick(e) {
    if (awaitingEdgeTo) return;

    const newIndex = nodes.length;
    const nodeName = generateNodeName(newIndex);

    const newNode = {
      id: crypto.randomUUID(),
      name: nodeName,
      lat: e.latlng.lat,
      lng: e.latlng.lng,
      weight: 1,
    };

    setNodes([...nodes, newNode]);
  }

  // USER CLICKS A NODE → EDGE CREATION OR MENU
  function onNodeClick(node) {
    if (!selectedNode) {
      setSelectedNode(node);
      setAwaitingEdgeTo(true);
      return;
    }

    if (awaitingEdgeTo) {
      if (node.id !== selectedNode.id) {
        const newEdge = {
          id: crypto.randomUUID(),
          from: selectedNode.id,
          to: node.id,
          weight: 1,
        };
        setEdges([...edges, newEdge]);
      }
    }

    setSelectedNode(null);
    setAwaitingEdgeTo(false);
  }

  return (
    <div ref={mapRef} className="w-full h-full">
      {/* Render all nodes */}
      {map &&
        nodes.map((node) =>
          L.marker([node.lat, node.lng], {
            draggable: false,
          })
            .addTo(map)
            .on("click", () => onNodeClick(node))
        )}

      {/* Render edges (polylines) */}
      {map &&
        edges.forEach((edge) => {
          const fromNode = nodes.find((n) => n.id === edge.from);
          const toNode = nodes.find((n) => n.id === edge.to);

          if (fromNode && toNode) {
            L.polyline(
              [
                [fromNode.lat, fromNode.lng],
                [toNode.lat, toNode.lng],
              ],
              { color: "cyan", weight: 3 }
            ).addTo(map);
          }
        })}
    </div>
  );
}
