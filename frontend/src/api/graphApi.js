// src/api/graphApi.js
const BASE = "http://localhost/wdn-app/backend/graph";

export async function listGraphs() {
  const res = await fetch(`${BASE}/list_graph.php`, { credentials: "include" });
  return res.json(); // { graphs: [...] }
}

export async function loadGraph(id) {
  const res = await fetch(`${BASE}/load_graph.php?id=${id}`, { credentials: "include" });
  return res.json(); // { graph: {...}, nodes: [...], edges: [...] }
}

export async function saveGraph(payload) {
  const res = await fetch(`${BASE}/save_graph.php`, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return res.json(); // { success: true, graph_id: ... } expected
}

export async function deleteGraph(id) {
  const form = new FormData();
  form.append("graph_id", id);
  const res = await fetch(`${BASE}/delete_graph.php`, {
    method: "POST",
    credentials: "include",
    body: form,
  });
  return res.json();
}
