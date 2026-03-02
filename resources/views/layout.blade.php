<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    @include('partials.head', ['title' => $heading ?? config('app.name')])
</head>
<body class="h-full">
    <div class="min-h-full">
        @if(!isset($hideNav) || !$hideNav)
        <header class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg">
            <div class="max-w-7xl mx-auto py-8 px-4 text-center">
                <h1 class="text-4xl font-bold text-white">{{ $heading ?? 'Editor fotek' }}</h1>
            </div>
        </header>
        @endif

        <main class="@if(!isset($hideNav) || !$hideNav) max-w-7xl mx-auto py-6 px-4 @endif">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
