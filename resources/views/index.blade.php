<x-layout>
    <x-slot:heading>
        Editor fotek
    </x-slot:heading>

    <!-- Toast notifikace -->
    <div id="toastContainer" class="fixed bottom-4 right-4 z-[9999] space-y-2"></div>

    <!-- Drag and drop / kliknutí -->
    <div class="max-w-2xl mx-auto mt-12">
        <form id="uploadForm" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <input id="fileInput" type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,image/jpeg,image/png,image/gif,image/bmp,image/webp">
        </form>

        <div id="dropZone"
             class="border-3 border-dashed border-indigo-300 rounded-3xl p-20 text-center cursor-pointer
                    bg-white shadow-lg hover:border-indigo-500 hover:shadow-xl transition-all duration-300
                    hover:scale-105">
            <svg class="mx-auto h-16 w-16 text-indigo-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-xl text-gray-700 font-medium mb-2">Přetáhni obrázek sem</p>
            <p class="text-gray-500">nebo klikni pro výběr ze zařízení</p>
        </div>
    </div>

    <!-- Vytvoření prázdného plátna -->
    <div class="max-w-2xl mx-auto mt-8">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Vytvořit prázdné plátno</h3>
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm text-gray-600 mb-1">Šířka (px)</label>
                    <input type="number" id="blankWidth" placeholder="500" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="flex-1">
                    <label class="block text-sm text-gray-600 mb-1">Výška (px)</label>
                    <input type="number" id="blankHeight" placeholder="500" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <button id="createBlankBtn" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    Vytvořit
                </button>
            </div>
        </div>
    </div>

    @php
        $parseIniSizeToBytes = function ($value) {
            $value = trim((string) $value);

            if ($value === '') {
                return 0;
            }

            $unit = strtolower(substr($value, -1));
            $number = (float) $value;

            return match ($unit) {
                'g' => (int) ($number * 1024 * 1024 * 1024),
                'm' => (int) ($number * 1024 * 1024),
                'k' => (int) ($number * 1024),
                default => (int) $number,
            };
        };

        $phpUploadMaxRaw = ini_get('upload_max_filesize');
        $phpPostMaxRaw = ini_get('post_max_size');
        $phpUploadMaxBytes = $parseIniSizeToBytes($phpUploadMaxRaw);
        $phpPostMaxBytes = $parseIniSizeToBytes($phpPostMaxRaw);
        $serverUploadLimitBytes = min($phpUploadMaxBytes, $phpPostMaxBytes);
        $serverUploadLimitLabel = $phpUploadMaxBytes <= $phpPostMaxBytes ? $phpUploadMaxRaw : $phpPostMaxRaw;
    @endphp

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
        const APP_MAX_FILE_SIZE = 50 * 1024 * 1024; // 50 MB
        const SERVER_MAX_FILE_SIZE = {{ $serverUploadLimitBytes }};
        const SERVER_MAX_FILE_SIZE_LABEL = @json($serverUploadLimitLabel);
        const MAX_WIDTH = 4032;  // Běžná mobilní fotka 12 Mpx
        const MAX_HEIGHT = 3024; // Běžná mobilní fotka 12 Mpx

        function isHeicFile(file) {
            const fileName = (file?.name || '').toLowerCase();
            const fileType = (file?.type || '').toLowerCase();

            return fileName.endsWith('.heic') ||
                fileName.endsWith('.heif') ||
                fileType.includes('heic') ||
                fileType.includes('heif');
        }

        function validateAndUploadFile(file) {
            if (!file) return;

            if (isHeicFile(file)) {
                showToast('HEIC/HEIF tato verze editoru zatím nepodporuje. Převeď obrázek na JPG, PNG nebo WebP.', 'error', 7000);
                return;
            }

            // Validace velikosti
            if (file.size > APP_MAX_FILE_SIZE) {
                showToast(`Soubor je příliš velký (${(file.size / 1024 / 1024).toFixed(1)} MB). Maximum je 50 MB.`, 'error', 5000);
                return;
            }

            if (file.size > SERVER_MAX_FILE_SIZE) {
                showToast(`Soubor je příliš velký pro aktuální nastavení serveru (${(file.size / 1024 / 1024).toFixed(1)} MB). Server teď povoluje jen ${SERVER_MAX_FILE_SIZE_LABEL}.`, 'error', 7000);
                return;
            }

            // Validace rozlišení
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = new Image();
                img.onload = function() {
                    const longSide = Math.max(img.width, img.height);
                    const shortSide = Math.min(img.width, img.height);

                    if (longSide > MAX_WIDTH || shortSide > MAX_HEIGHT) {
                        showToast(`Rozlišení je příliš velké (${img.width}×${img.height}). Maximum je ${MAX_WIDTH}×${MAX_HEIGHT} v libovolné orientaci.`, 'error', 5000);
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
            reader.onerror = function() {
                showToast('Chyba při čtení souboru. Zkuste znovu.', 'error');
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
    @if ($errors->any())
        <script>
            showToast(@json($errors->first('image')), 'error', 6000);
        </script>
    @endif
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
    const widthInput = document.getElementById('blankWidth');
    const heightInput = document.getElementById('blankHeight');
    const createBlankBtn = document.getElementById('createBlankBtn');

    // Funkce pro vytvoření prázdného obrázku
    function createBlankImage(width, height) {
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, width, height);

        // Převést na blob a odeslat na server pomocí standardního formuláře
        canvas.toBlob((blob) => {
            const formData = new FormData();
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('fileInput');
            
            // Vytvořit File objekt z blobu
            const file = new File([blob], 'blank.png', { type: 'image/png' });
            
            // Vytvořit nový DataTransfer pro nastavení souborů do inputu
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Odeslat formulář
            form.submit();
        }, 'image/png');
    }

    // Tlačítka šablon
    templateButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const width = parseInt(btn.getAttribute('data-width'));
            const height = parseInt(btn.getAttribute('data-height'));

            createBlankImage(width, height);
        });
    });

    // Ruční vytvoření prázdného obrázku
    createBlankBtn.addEventListener('click', () => {
        const width = parseInt(widthInput.value) || 500;
        const height = parseInt(heightInput.value) || 500;

        createBlankImage(width, height);
    });
</script>



</x-layout>
