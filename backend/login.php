<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-[#0f0f0f] to-[#1a1a1a] h-screen flex items-center justify-center">

<div class="w-96 bg-[#141414] border border-gray-800 p-8 rounded-xl shadow-xl">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <form action="login_process.php" method="POST" class="space-y-4">
        <input type="text" name="username" placeholder="Username" class="w-full p-3 rounded-lg bg-[#1f1f1f] border border-gray-700">
        <input type="password" name="password" placeholder="Password" class="w-full p-3 rounded-lg bg-[#1f1f1f] border border-gray-700">
        
        <button class="w-full p-3 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold">
            Login
        </button>
    </form>
</div>

</body>
</html>
