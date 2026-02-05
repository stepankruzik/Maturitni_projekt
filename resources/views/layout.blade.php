<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    @include('partials.head', ['title' => $heading ?? config('app.name')])
</head>
<body class="h-full">
    <div class="min-h-full">
        @if(!isset($hideNav) || !$hideNav)
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
        @endif

        <main class="@if(!isset($hideNav) || !$hideNav) max-w-7xl mx-auto py-6 px-4 @endif">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
