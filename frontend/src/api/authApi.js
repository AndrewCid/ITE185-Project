// src/api/authApi.js
export async function loginUser(username, password) {
  const formData = new FormData();
  formData.append("username", username);
  formData.append("password", password);

  const res = await fetch("http://localhost/wdn-app/backend/auth/login.php", {
    method: "POST",
    credentials: "include",
    body: formData,
  });

  return res.json();
}

export async function getCurrentUser() {
  const res = await fetch(
    "http://localhost/wdn-app/backend/auth/login.php?session=1",
    { credentials: "include" }
  );
  return res.json();
}
