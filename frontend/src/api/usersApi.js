// src/api/usersApi.js
const BASE = "http://localhost/wdn-app/backend/users/";

export async function fetchUsers(sort="created_at", order="DESC", search="", date="") {
  const res = await fetch(
    `${BASE}list.php?sort=${sort}&order=${order}&search=${search}&date=${date}`,
    { credentials: "include" }
  );
  return res.json();
}


export async function getUser(id) {
  const res = await fetch(`${BASE}get.php?id=${id}`, {
    credentials: "include"
  });
  return res.json();
}

export async function createUser(data) {
  const formData = new FormData();
  Object.keys(data).forEach((key) => {
    formData.append(key, data[key]);
  });

  const res = await fetch("http://localhost/wdn-app/backend/users/create.php", {
    method: "POST",
    credentials: "include",
    body: formData,
  });

  return res.json();
}


export async function updateUser(data) {
  const formData = new FormData();
  Object.entries(data).forEach(([k, v]) => formData.append(k, v));

  const res = await fetch(`${BASE}update.php`, {
    method: "POST",
    body: formData,
    credentials: "include",
  });
  return res.json();
}

export async function deleteUser(id) {
  const formData = new FormData();
  formData.append("id", id);

  const res = await fetch(`${BASE}delete.php`, {
    method: "POST",
    body: formData,
    credentials: "include",
  });
  return res.json();
}
