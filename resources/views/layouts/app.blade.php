<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembukuan Otomatis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3">
            <h1 class="text-lg font-bold">Pembukuan Otomatis</h1>
        </div>
    </nav>

    <main class="container mx-auto py-6">
        @yield('content')
    </main>

    <footer class="bg-gray-200 text-center py-4 mt-10">
        <p class="text-sm text-gray-600">&copy; 2026 Pembukuan Otomatis</p>
    </footer>
</body>
</html>
