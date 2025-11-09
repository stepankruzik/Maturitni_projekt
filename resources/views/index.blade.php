<x-layout>
    <x-slot:heading>
        Domů
    </x-slot:heading>

    <h2 class="text-xl font-semibold mb-4">Nahraj nebo vytvoř nový obrázek</h2>

    <!-- Upload formulář -->
     <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" accept="image/*" class="block">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
            Nahrát obrázek
        </button>
    </form>
    

    <!-- Vytvoření prázdného -->
    <form action="{{ route('createBlank') }}" method="POST" class="space-y-2">
        @csrf
        <input type="number" name="width" placeholder="Šířka" class="border p-1">
        <input type="number" name="height" placeholder="Výška" class="border p-1">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
            Vytvořit prázdný
        </button>
    </form>
</x-layout>
