@props([
    'hideNav' => false,
    'heading' => null,
    'description' => null,
    'seoTitle' => null,
    'metaDescription' => null,
    'canonicalUrl' => null,
    'jsonLd' => null,
])
<!DOCTYPE html>
<html lang="cs" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle ?? $heading ?? 'Online editor fotek' }}</title>
    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if($canonicalUrl)
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @if($jsonLd)
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Open+Sans&family=Lato&family=Montserrat&family=Poppins&family=Playfair+Display&family=Oswald&display=swap" rel="stylesheet">
</head>
<body class="h-full">

@if(!$hideNav)
<div class="min-h-full">
  <header class="bg-gradient-to-r from-indigo-500 to-purple-600 py-12">
    <div class="mx-auto max-w-7xl px-4 text-center">
      <h1 class="text-5xl font-bold text-white mb-2">{{ $heading ?? 'Editor fotek' }}</h1>
      <p class="text-indigo-100 text-lg">{{ $description ?? 'Jednoduchý online editor pro úpravu obrázků' }}</p>
    </div>
  </header>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      {{ $slot }}
    </div>
  </main>
</div>
@else
<div class="min-h-full">
  <main>
    <div class="p-4">
      {{ $slot }}
    </div>
  </main>
</div>
@endif

</body>
</html>