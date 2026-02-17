<x-layout>
    <x-slot:heading>
        Domů
    </x-slot:heading>

    <!-- Drag and drop / kliknutí -->
    <div class="max-w-xl mx-auto mt-20">

        <form id="uploadForm" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <input id="fileInput" type="file" name="image" accept="image/*,image/heic,image/heif,.heic,.heif">
        </form>
        
        <!-- Toast notifikace -->
        <div id="toastContainer" class="fixed bottom-4 right-4 z-[9999] space-y-2"></div>

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
        // Toast notifikační systém
        function showToast(message, type = 'info', duration = 4000) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            const typeStyles = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            };
            
            const bgClass = typeStyles[type] || typeStyles['info'];
            
            toast.className = `${bgClass} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 animate-slide-in`;
            toast.innerHTML = `
                <span>${message}</span>
                <button class="ml-auto text-white hover:opacity-70" onclick="this.parentElement.remove()">✕</button>
            `;
            
            container.appendChild(toast);
            
            if (duration > 0) {
                setTimeout(() => {
                    toast.style.animation = 'fade-out 0.3s ease-out';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }
        }
        
        // CSS animace
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slide-in {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes fade-out {
                from {
                    opacity: 1;
                }
                to {
                    opacity: 0;
                }
            }
            .animate-slide-in {
                animation: slide-in 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
        
        // Validace a upload
        const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50 MB
        const MAX_WIDTH = 4000;  // Přesně 4K
        const MAX_HEIGHT = 2250; // Přesně 4K

        function validateAndUploadFile(file) {
            if (!file) return;

            // Validace velikosti
            if (file.size > MAX_FILE_SIZE) {
                showToast(`Soubor je příliš velký (${(file.size / 1024 / 1024).toFixed(1)} MB). Maximum je 50 MB.`, 'error', 5000);
                return;
            }

            // Validace rozlišení
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = new Image();
                img.onload = function() {
                    if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT) {
                        showToast(`Rozlišení je příliš velké (${img.width}×${img.height}). Maximum je 4K (4000×2250).`, 'error', 5000);
                        return;
                    }
                    
                    // Všechny validace prošly - odešli formulář
                    const form = document.getElementById('uploadForm');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('fileInput').files = dataTransfer.files;
                    
                    showToast(`Obrázek se nahrává (${img.width}×${img.height})...`, 'info');
                    form.submit();
                };
                img.onerror = function() {
                    showToast('Nelze načíst obrázek. Zkuste jiný soubor.', 'error');
                };
                img.src = evt.target.result;
            };
            reader.readAsDataURL(file);
        }

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
            validateAndUploadFile(file);
        });

        fileInput.addEventListener('change', () => {
            validateAndUploadFile(fileInput.files[0]);
        });
    </script>
<!-- Šablony prázdného plátna -->
<div class="max-w-2xl mx-auto mt-6 space-y-6 text-center">
    <p class="font-semibold text-lg">Šablony plátna</p>

    <!-- Sociální sítě -->
    <div>
        <p class="font-semibold text-md mb-2">Sociální sítě</p>
        <div class="flex flex-wrap justify-center gap-4">
            <button type="button" data-width="1080" data-height="1080"
                    class="flex flex-col items-center p-3 bg-blue-500 text-white rounded hover:bg-blue-600 min-w-[100px]">
                <span class="material-icons text-3xl">photo</span>
                Instagram Post
                <small>1080×1080</small>
            </button>

            <button type="button" data-width="1080" data-height="1920"
                    class="flex flex-col items-center p-3 bg-blue-500 text-white rounded hover:bg-blue-600 min-w-[100px]">
                <span class="material-icons text-3xl">slideshow</span>
                Instagram Story
                <small>1080×1920</small>
            </button>

            <button type="button" data-width="1920" data-height="1080"
                    class="flex flex-col items-center p-3 bg-blue-500 text-white rounded hover:bg-blue-600 min-w-[100px]">
                <span class="material-icons text-3xl">desktop_windows</span>
                Full HD
                <small>1920×1080</small>
            </button>

            <button type="button" data-width="1200" data-height="630"
                    class="flex flex-col items-center p-3 bg-blue-500 text-white rounded hover:bg-blue-600 min-w-[100px]">
                <span class="material-icons text-3xl">facebook</span>
                Facebook Post
                <small>1200×630</small>
            </button>

            <button type="button" data-width="1280" data-height="720"
                    class="flex flex-col items-center p-3 bg-blue-500 text-white rounded hover:bg-blue-600 min-w-[100px]">
                <span class="material-icons text-3xl">videocam</span>
                YouTube Thumbnail
                <small>1280×720</small>
            </button>

            <button type="button" data-width="1200" data-height="628"
                    class="flex flex-col items-center p-3 bg-blue-500 text-white rounded hover:bg-blue-600 min-w-[100px]">
                <span class="material-icons text-3xl">campaign</span>
                Twitter Post
                <small>1200×628</small>
            </button>
        </div>
    </div>

    <!-- Tiskové formáty -->
    <div>
        <p class="font-semibold text-md mb-2">Tisk</p>
        <div class="flex flex-wrap justify-center gap-4">
            <button type="button" data-width="2480" data-height="3508"
                    class="flex flex-col items-center p-3 bg-green-500 text-white rounded hover:bg-green-600 min-w-[100px]">
                <span class="material-icons text-3xl">print</span>
                A4
                <small>210×297 mm</small>
            </button>

            <button type="button" data-width="3508" data-height="4961"
                    class="flex flex-col items-center p-3 bg-green-500 text-white rounded hover:bg-green-600 min-w-[100px]">
                <span class="material-icons text-3xl">print</span>
                A3
                <small>297×420 mm</small>
            </button>

            <button type="button" data-width="1748" data-height="2480"
                    class="flex flex-col items-center p-3 bg-green-500 text-white rounded hover:bg-green-600 min-w-[100px]">
                <span class="material-icons text-3xl">print</span>
                A5
                <small>148×210 mm</small>
            </button>

            <button type="button" data-width="1240" data-height="1748"
                    class="flex flex-col items-center p-3 bg-green-500 text-white rounded hover:bg-green-600 min-w-[100px]">
                <span class="material-icons text-3xl">print</span>
                A6
                <small>105×148 mm</small>
            </button>
        </div>
    </div>

    <!-- Malé fotky -->
    <div>
        <p class="font-semibold text-md mb-2">Malé fotky</p>
        <div class="flex flex-wrap justify-center gap-4">
            <button type="button" data-width="600" data-height="600"
                    class="flex flex-col items-center p-3 bg-yellow-500 text-white rounded hover:bg-yellow-600 min-w-[100px]">
                <span class="material-icons text-3xl">image</span>
                Small Photo
                <small>600×600</small>
            </button>

            <button type="button" data-width="300" data-height="300"
                    class="flex flex-col items-center p-3 bg-yellow-500 text-white rounded hover:bg-yellow-600 min-w-[100px]">
                <span class="material-icons text-3xl">image</span>
                Thumbnail
                <small>300×300</small>
            </button>

            <button type="button" data-width="600" data-height="900"
                    class="flex flex-col items-center p-3 bg-yellow-500 text-white rounded hover:bg-yellow-600 min-w-[100px]">
                <span class="material-icons text-3xl">image</span>
                Wallet
                <small>600×900</small>
            </button>
        </div>
    </div>
</div>


<script>
    const templateButtons = document.querySelectorAll('[data-width][data-height]');
    const widthInput = document.querySelector('input[name="width"]');
    const heightInput = document.querySelector('input[name="height"]');
    
    const createBlankForm = document.querySelector('form[action="{{ route('createBlank') }}"]');

    

    templateButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            
            const width = btn.getAttribute('data-width');
            const height = btn.getAttribute('data-height');
            
            widthInput.value = width;
            heightInput.value = height;

            if (createBlankForm) {
                createBlankForm.submit();
            } else {
                console.error('Formulář pro vytvoření prázdného plátna nebyl nalezen!');
            }
        });
    });
</script>



</x-layout>
