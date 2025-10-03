<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $heading ?? 'App' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <nav class="bg-gray-800 p-4 text-white">
            <div class="flex space-x-4">
                <a href="{{ route('home') }}" class="hover:underline">Home</a>
            </div>
        </nav>

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ $heading ?? '' }}</h1>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 px-4">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
