<x-layout>
    <x-slot:heading>
        Domů
    </x-slot:heading>

    <!-- Drag and drop / kliknutí -->
    <div class="max-w-xl mx-auto mt-20">

        <form id="uploadForm" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <input id="fileInput" type="file" name="image" accept="image/*">
        </form>

        <div id="dropZone"
             class="border-4 border-dashed border-gray-400 rounded-2xl p-12 text-center cursor-pointer
                    hover:border-indigo-400 hover:bg-indigo-50 transition duration-200">
            Přetáhni obrázek sem nebo klikni pro výběr
        </div>
    </div>

    <!-- Vytvoření prázdného obrázku -->
    <form action="{{ route('createBlank') }}" method="POST" class="space-y-2 mt-6 max-w-xl mx-auto">
        @csrf
        <input type="number" name="width" placeholder="Šířka" class="border p-1">
        <input type="number" name="height" placeholder="Výška" class="border p-1">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
            Vytvořit prázdný
        </button>
    </form>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const form = document.getElementById('uploadForm');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-indigo-500');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-indigo-500');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500');

            const file = e.dataTransfer.files[0];
            if (!file) return;

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            form.submit();
        });

        fileInput.addEventListener('change', () => {
            form.submit();
        });
    </script>
</x-layout>
