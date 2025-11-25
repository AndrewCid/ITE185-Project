<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>WDN Dashboard</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        darkbg: "#0f0f0f",
                        card: "#141414",
                    }
                }
            }
        }
    </script>

    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* custom animations */
        .slide-in {
            animation: slideIn 0.35s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
    </style>
</head>

<body class="bg-darkbg text-gray-200 font-sans">

    <!-- MOBILE TOP NAV -->
    <div class="md:hidden bg-card border-b border-gray-800 p-4 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-lg font-semibold">WDN System</h1>
        <button id="mobileMenuBtn" class="text-gray-300 hover:text-white">
            ☰
        </button>
    </div>

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <aside id="sidebar"
               class="w-64 bg-card border-r border-gray-800 p-4 flex flex-col slide-in
                      fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50">

            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-600 rounded-lg shadow"></div>
                <h1 class="text-lg font-semibold">WDN System</h1>
            </div>

            <nav class="flex-1 space-y-2 text-sm fade-in">
                <a href="home.php" class="block px-3 py-2 rounded-lg hover:bg-gray-800">Home</a>
                <a href="main.php" class="block px-3 py-2 rounded-lg hover:bg-gray-800">Mapping</a>
                <a href="authors.php" class="block px-3 py-2 rounded-lg hover:bg-gray-800">About Authors</a>
                <a href="settings.php" class="block px-3 py-2 rounded-lg hover:bg-gray-800">Settings</a>
            </nav>

            <div class="mt-auto">
                <a href="logout.php"
                   class="block px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-center transition">
                    Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-6 overflow-y-auto fade-in md:ml-0 ml-0">

                </main>
    </div>

<script>
    // Mobile toggle
    const sidebar = document.getElementById("sidebar");
    const mobileBtn = document.getElementById("mobileMenuBtn");

    mobileBtn?.addEventListener("click", () => {
        if (sidebar.classList.contains("-translate-x-full")) {
            sidebar.classList.remove("-translate-x-full");
        } else {
            sidebar.classList.add("-translate-x-full");
        }
    });
</script>

</body>
</html>
