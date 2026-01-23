<x-layout>
    <x-slot:heading>Editor obrázku</x-slot:heading>
    <div class="flex gap-2 mb-3">
    <button id="undoBtn" class="px-3 py-1 bg-gray-700 text-white rounded transition-all duration-150 active:bg-gray-900 disabled:opacity-40 disabled:cursor-not-allowed" disabled title="Zpět">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-undo2-icon lucide-undo-2 w-5 h-5 inline">
            <path d="M9 14 4 9l5-5"/>
            <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/>
        </svg>
    </button>
    <button id="redoBtn" class="px-3 py-1 bg-gray-700 text-white rounded transition-all duration-150 active:bg-gray-900 disabled:opacity-40 disabled:cursor-not-allowed" disabled title="Dopředu">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-redo2-icon lucide-redo-2 w-5 h-5 inline" style="transform: scaleX(-1);">
            <path d="M9 14 4 9l5-5"/>
            <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/>
        </svg>
    </button>
</div>

    <div class="flex gap-2 mb-4">
        <button class="tab-btn px-3 py-1 bg-blue-500 text-white rounded" data-target="panelResize">Resize/Ořez</button>
        <button class="tab-btn px-3 py-1 bg-green-500 text-white rounded" data-target="panelFilters">Filtry</button>
        <button class="tab-btn px-3 py-1 bg-orange-500 text-white rounded" data-target="panelDownload">Export</button>
        <button class="tab-btn px-3 py-1 bg-red-500 text-white rounded" data-target="panelLevels">Úrovně</button>
        <button class="tab-btn px-3 py-1 bg-indigo-500 text-white rounded" data-target="panelDraw">Kreslení</button>
        <button class="tab-btn px-3 py-1 bg-pink-500 text-white rounded" data-target="panelText">Text</button>
    </div>

    <div class="flex h-screen">
    <!-- Sidebar vlevo -->
    <div class="w-72 bg-gray-100 border-r border-gray-300 p-4 overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Úpravy obrázku</h2>

    <button id="addImageBtn" class="w-full mb-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Přidat další obrázek</button>
    <input type="file" id="addImageInput" accept="image/*" class="hidden">

        <p id="imageSize" class="text-gray-600 font-semibold mb-1"></p>
        <p id="rotationAngle" class="text-gray-600 font-semibold mb-3"></p>

    <div id="panelResize" class="tab-panel">
        <div class="space-y-2 mb-4">
            <button id="toggleMode" class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                Režim: Změnit velikost
            </button>
            <button id="cropBtn" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">
                Oříznout
            </button>
        </div>
    </div>

    <div id="panelFilters" class="tab-panel hidden">
        <div id="filterPreview" class="flex flex-wrap gap-2 mt-2">
            <img src="/thumbnails/original.png" class="filter-thumb" data-filter="original">
            <img src="/thumbnails/grayscale.png" class="filter-thumb" data-filter="grayscale">
            <img src="/thumbnails/sepia.png" class="filter-thumb" data-filter="sepia">
            <img src="/thumbnails/invert.png" class="filter-thumb" data-filter="invert">
            <img src="/thumbnails/blur.png" class="filter-thumb" data-filter="blur">
            <img src="/thumbnails/sharpen.png" class="filter-thumb" data-filter="sharpen">
        </div>
    </div>

    <div id="panelDownload" class="tab-panel hidden">
        <details open class="mb-4 bg-white rounded-lg shadow p-4">
            <summary class="cursor-pointer font-bold text-lg text-green-700">
                Nastavení Exportu
            </summary>
            <div class="space-y-3 mt-3">
                <div>
                    <label for="exportFormat" class="block text-sm font-medium text-gray-700 mb-1">Formát souboru:</label>
                    <select id="exportFormat" class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                        <option value="png">PNG (bezeztrátový)</option>
                        <option value="jpeg">JPEG (menší velikost)</option>
                        <option value="webp">WEBP (webové stránky)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Obsah exportu:</label>
                    <div class="flex flex-col gap-2 bg-gray-50 p-3 rounded-md border border-gray-200">
                        <label class="inline-flex items-center">
                            <input type="radio" name="exportContent" value="canvas" checked class="form-radio text-green-600">
                            <span class="ml-2 text-sm">Celý Canvas</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="exportContent" value="image" class="form-radio text-green-600">
                            <span class="ml-2 text-sm">Jen obrázek</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="exportFileName" class="block text-sm font-medium text-gray-700 mb-1">Název souboru:</label>
                    <input type="text" id="exportFileName" class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500" placeholder="např. muj-obrazek">
                </div>
                <button id="startDownloadBtn" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    Stáhnout
                </button>
            </div>
        </details>
    </div>

    <div id="panelLevels" class="tab-panel hidden">
        <div class="space-y-3 mt-2">
            <label class="flex items-center gap-2">Jas:
                <input type="range" id="brightness" min="-1" max="1" step="0.1" value="0" class="w-full">
            </label>
            <label class="flex items-center gap-2">Kontrast:
                <input type="range" id="contrast" min="-1" max="1" step="0.1" value="0" class="w-full">
            </label>
            <label class="flex items-center gap-2">Sytost:
                <input type="range" id="saturation" min="-1" max="1" step="0.1" value="0" class="w-full">
            </label>
        </div>
    </div>

   <div id="panelDraw" class="tab-panel hidden">

    <!-- BARVY -->
    <div class="mb-4 space-y-2">
        <label class="block text-sm font-medium text-gray-700">
            Výplň:
            <input type="color" id="fillColor" value="#ffffff"
                class="w-full h-10 rounded border cursor-pointer">
            <label class="flex items-center gap-2 mt-1 text-sm">
                <input type="checkbox" id="fillTransparent">
                Průhledná
            </label>
        </label>

        <label class="block text-sm font-medium text-gray-700">
            Obrys:
            <input type="color" id="drawColor" value="#ff0000"
                class="w-full h-10 rounded border cursor-pointer">
        </label>
    </div>

    <!-- KRESLENÍ -->
    <div class="flex gap-2 mb-4 justify-around">

    <!-- VÝBĚR -->
     <button id="drawSelectBtn" class="tool-btn" title="Výběr/Přesun">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/>
            <path d="M13 13l6 6"/>
        </svg>
    </button>

    <!-- čára -->
    <button id="drawLineBtn" class="tool-btn" title="Čára">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19L19 5" />
        </svg>
    </button>

    <!-- kruh -->
    <button id="drawCircleBtn" class="tool-btn" title="Kruh">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <!-- obdélník -->
    <button id="drawRectBtn" class="tool-btn" title="Obdélník">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect x="4" y="4" width="16" height="16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    </div>

    <div class="flex gap-2 mb-4 justify-around">
       <!--  Trojúhelník -->
        <button id="drawTriangleBtn" class="tool-btn" title="Trojúhelník">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 2L22 20H2L12 2Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- Pravoúhlý trojúhelník -->
        <button id="drawRightTriangleBtn" class="tool-btn" title="Pravoúhlý trojúhelník">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M4 20L20 20L4 4L4 20Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- Elipsa -->
        <button id="drawEllipseBtn" class="tool-btn" title="Elipsa">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <ellipse cx="12" cy="12" rx="9" ry="6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- Hvězda -->
        <button id="drawStarBtn" class="tool-btn" title="Hvězda">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 2L15 9H22L17 14L19 21L12 17L5 21L7 14L2 9H9L12 2Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

     <div class="flex gap-2 mb-4 justify-around">
        <!-- Srdce -->
        <button id="drawHeartBtn" class="tool-btn" title="Srdce">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 21C12 21 4 13.5 4 8.5C4 5.42 6.42 3 9.5 3C11.24 3 12.91 3.81 14 5.08C15.09 3.81 16.76 3 18.5 3C21.58 3 24 5.42 24 8.5C24 13.5 16 21 16 21H12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- Šipka -->
        <button id="drawArrowBtn" class="tool-btn" title="Šipka">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 12L22 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 6L22 12L16 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- bublina -->
        <button id="drawSpeechBubbleBtn" class="tool-btn" title="Bublina">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4H20V16H5.17L4 17.17V4Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- oblá bublina -->
        <button id="drawRoundedSpeechBubbleBtn" class="tool-btn" title="Oblá bublina">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 5C4 3.89543 4.89543 3 6 3H18C19.1046 3 20 3.89543 20 5V15C20 16.1046 19.1046 17 18 17H6L4 19V5Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
     </div>

     <div class="flex gap-2 mb-4 justify-around">
        <!-- zaoblený obdélník -->
        <button id="drawRoundedRectBtn" class="tool-btn" title="Zaoblený obdélník">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="4" y="4" width="16" height="16" rx="4" ry="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- zakřivená šipka -->
        <button id="drawArrowRightBtn" class="tool-btn" title="Zakřivená šipka">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 17 Q 3 7, 13 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 3 L 13 7 L 9 11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- šestiúhelník -->
        <button id="drawHexagonBtn" class="tool-btn" title="Šestiúhelník">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L21 7V17L12 22L3 17V7L12 2Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <!-- křížek -->
        <button id="drawCrossBtn" class="tool-btn" title="Křížek">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="5" x2="19" y2="19" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="19" y1="5" x2="5" y2="19" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
     </div>

    <label class="block text-sm font-medium text-gray-700 mt-3">
       Šířka čáry:
       <input
           type="range"
           id="brushWidth"
           min="1"
           max="30"
           value="3"
           class="w-full">
    </label>
    <label class="block text-sm font-medium text-gray-700 mt-3">
        Velikost gumy:
        <input
            type="range"
            id="eraserSize"
            min="5"
            max="60"
            value="20"
            class="w-full">
    </label>
<hr class="my-3">

<p class="text-sm font-semibold text-gray-600">Nastavení objektu</p>

<label class="block text-sm mt-2">
    Typ čáry:
    <select id="strokeStyle" class="w-full border rounded p-1">
        <option value="solid">Plná</option>
        <option value="dashed">Čárkovaná</option>
        <option value="dotted">Tečkovaná</option>
    </select>
</label>

<label class="block text-sm mt-2">
    Zakončení čáry:
    <select id="strokeCap" class="w-full border rounded p-1">
        <option value="round">Zaoblené</option>
        <option value="butt">Rovné</option>
    </select>
</label>

    <div class="flex gap-2 mb-4 justify-around mt-4">
    <!-- tužka-->
    <button id="drawBrushBtn" class="tool-btn" title="Kreslení tužkou">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 20h9"/>
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
        </svg>
    </button>

    <!-- guma -->
    <button id="drawEraserBtn" class="tool-btn" title="Guma">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="m7 21-4-4 9-9 4 4-9 9z"/>
            <path d="M14 6 8 12"/>
            <path d="m16 10 2 2"/>
        </svg>
    </button>

    <!-- pravítko -->
    <button id="toggleRulerBtn" class="tool-btn" title="Pravítko">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21.3 8.7 8.7 21.3c-1 1-2.5 1-3.4 0l-2.6-2.6c-1-1-1-2.5 0-3.4L15.3 2.7c1-1 2.5-1 3.4 0l2.6 2.6c1 1 1 2.5 0 3.4Z"/>
            <path d="m7.5 10.5 2 2"/>
            <path d="m10.5 7.5 2 2"/>
            <path d="m13.5 4.5 2 2"/>
            <path d="m4.5 13.5 2 2"/>
        </svg>
    </button>

    <!-- zamknout obrázek -->
    <button id="lockObjectBtn" class="tool-btn" title="Zamknout/Odemknout obrázek">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </button>

</div>

    <!-- VRSTVY -->
    <div class="border-t pt-3 space-y-2">
        <p class="text-sm font-semibold text-gray-600">Vrstvy</p>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" class="layerImageCheck" checked>
            Obrázek
        </label>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" class="layerDrawCheck" checked>
            Kreslení
        </label>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" class="layerTextCheck" checked>
            Text
        </label>
    </div>

</div>

<div id="panelText" class="tab-panel hidden">

    <div class="flex gap-2 mb-4 justify-around">
        <!-- VÝBĚR -->
         <button id="textSelectBtn" class="tool-btn" title="Výběr/Přesun textu">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/>
                <path d="M13 13l6 6"/>
            </svg>
        </button>

        <!-- Kreslení textového pole -->
        <button id="drawTextBoxBtn" class="tool-btn" title="Nakreslit textové pole">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                <path d="M14 2v6h6"/>
                <path d="M9 13h6"/>
                <path d="M9 17h3"/>
            </svg>
        </button>
    </div>

    <label class="block text-sm font-medium text-gray-700 mb-2">
        Text:
        <input type="text" id="textInput"
               class="w-full border rounded p-2"
               placeholder="Napiš text…">
    </label>

    <label class="block text-sm mt-2">
        Velikost písma:
        <input type="range" id="textSize" min="10" max="120" value="32" class="w-full">
    </label>

    <div class="mt-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Font:</label>
        <div id="fontPickerBtn" class="w-full border rounded p-2 bg-white cursor-pointer flex items-center justify-between hover:border-pink-400 transition">
            <span id="fontPickerLabel" style="font-family: Arial;">Arial</span>
            <svg id="fontPickerArrow" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <input type="hidden" id="textFont" value="Arial">
        
        <!-- Font Picker Dropdown-->
        <div id="fontPickerDropdown" class="hidden mt-1 border rounded-lg bg-white shadow-lg">
            <div class="p-2 border-b">
                <input type="text" id="fontSearch" placeholder="Hledat font..." 
                       class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:border-pink-400">
            </div>
            <div id="fontList" class="overflow-y-auto max-h-48 p-1">
            </div>
        </div>
    </div>

    <label class="block text-sm mt-2">
        Barva textu:
        <input type="color" id="textColor" value="#000000"
               class="w-full h-10 rounded border cursor-pointer">
    </label>

    <!-- Zarovnání textu -->
    <div class="mt-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Zarovnání:</label>
        <div class="flex gap-1">
            <button id="alignLeft" class="text-align-btn flex-1 p-2 border rounded hover:bg-pink-100 transition bg-pink-200" title="Zarovnat vlevo">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="15" y2="12"/>
                    <line x1="3" y1="18" x2="18" y2="18"/>
                </svg>
            </button>
            <button id="alignCenter" class="text-align-btn flex-1 p-2 border rounded hover:bg-pink-100 transition" title="Zarovnat na střed">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="6" y1="12" x2="18" y2="12"/>
                    <line x1="4" y1="18" x2="20" y2="18"/>
                </svg>
            </button>
            <button id="alignRight" class="text-align-btn flex-1 p-2 border rounded hover:bg-pink-100 transition" title="Zarovnat vpravo">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="9" y1="12" x2="21" y2="12"/>
                    <line x1="6" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Styl textu -->
    <div class="mt-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Styl:</label>
        <div class="flex gap-1">
            <button id="textBold" class="text-style-btn flex-1 p-2 border rounded hover:bg-pink-100 transition font-bold" title="Tučné">
                B
            </button>
            <button id="textItalic" class="text-style-btn flex-1 p-2 border rounded hover:bg-pink-100 transition italic" title="Kurzíva">
                I
            </button>
            <button id="textUnderline" class="text-style-btn flex-1 p-2 border rounded hover:bg-pink-100 transition underline" title="Podtržení">
                U
            </button>
            <button id="textLinethrough" class="text-style-btn flex-1 p-2 border rounded hover:bg-pink-100 transition line-through" title="Přeškrtnutí">
                S
            </button>
        </div>
    </div>

    <button id="addTextBtn"
            class="w-full mt-3 px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">
        Přidat text
    </button>

    <!-- VRSTVY -->
    <div class="border-t pt-3 mt-4 space-y-2">
        <p class="text-sm font-semibold text-gray-600">Vrstvy</p>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" class="layerImageCheck" checked>
            Obrázek
        </label>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" class="layerDrawCheck" checked>
            Kreslení
        </label>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" class="layerTextCheck" checked>
            Text
        </label>
    </div>

</div>

</div>

    <!-- Canvas vpravo -->
    <div class="flex-1 flex justify-center items-center bg-gray-50 relative">
        <canvas id="canvas" class="border border-gray-300 shadow-lg"></canvas>
        
        <!-- Kontextové menu -->
        <div id="contextMenu" class="hidden absolute bg-white border border-gray-300 rounded-lg shadow-lg py-1 z-50 min-w-[150px]">
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="bringForward">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 19V5M5 12l7-7 7 7"/>
                </svg>
                Dopředu
            </button>
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="sendBackward">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12l7 7 7-7"/>
                </svg>
                Dozadu
            </button>
            <div class="border-t border-gray-200 my-1"></div>
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="bringToFront">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 19V5M5 12l7-7 7 7"/>
                    <path d="M5 5h14"/>
                </svg>
                Na vrch
            </button>
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="sendToBack">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12l7 7 7-7"/>
                    <path d="M5 19h14"/>
                </svg>
                Na spodek
            </button>
            <div class="border-t border-gray-200 my-1"></div>
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600 flex items-center gap-2" data-action="delete">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                </svg>
                Smazat
            </button>
            <div class="border-t border-gray-200 my-1"></div>
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="copy">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                </svg>
                Kopírovat
            </button>
            <button class="context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="paste">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                </svg>
                Vložit
            </button>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
const canvas = new fabric.Canvas('canvas', {
    preserveObjectStacking: true  // Zachová pořadí vrstev při výběru objektu
});
// UNDO / REDO 
const HISTORY = {
  undoStack: [],
  redoStack: [],
  isRestoring: false,
  max: 50,
  extraProps: [
    'layer', 'erasable', 'excludeFromExport', 'isRuler',
    'selectable', 'evented'
  ],
}; 

function isHistoryObject(obj) {
  if (!obj) return false;
  if (obj === eraserCursor) return false;
  if (typeof snapIndicator !== 'undefined' && obj === snapIndicator) return false;
  if (typeof lengthIndicator !== 'undefined' && obj === lengthIndicator) return false;
  if (obj.isRuler) return false;
  if (obj === cropRect) return false;
  return true;
}

function saveHistoryState(reason = '') {
  if (HISTORY.isRestoring) return;
  if (historyBatch) return;

  const json = canvas.toDatalessJSON(HISTORY.extraProps);

  const str = JSON.stringify(json);

  // anti-duplicate: neukládej stejný stav dvakrát po sobě
  const last = HISTORY.undoStack[HISTORY.undoStack.length - 1];
  if (last === str) return;

  HISTORY.undoStack.push(str);
  if (HISTORY.undoStack.length > HISTORY.max) HISTORY.undoStack.shift();

  HISTORY.redoStack.length = 0;

  updateUndoRedoButtons();
}

function cleanupAfterRestore() {
    // skryj indikátory
    if (typeof snapIndicator !== 'undefined' && snapIndicator) snapIndicator.visible = false;
    if (typeof lengthIndicator !== 'undefined' && lengthIndicator) lengthIndicator.visible = false;
    hideEraserCursor?.();

    canvas.getObjects().forEach(o => {
        if (o.isRuler) canvas.remove(o);
    });

    canvas.getObjects().forEach(o => {
        if (o.type === 'line' && o.layer === 'draw') {
            enableLineEndpointsControls(o);
            o.setCoords();
        }
    });

    // Obnov velikost canvasu a viewportTransform
    canvas.setWidth(MAX_CANVAS_WIDTH);
    canvas.setHeight(MAX_CANVAS_HEIGHT);
    canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);

    // Pokud je obrázek, centrovat ho
    const img = canvas.getObjects().find(o => o.type === 'image');
    if (img) {
        img.left = canvas.width / 2;
        img.top = canvas.height / 2;
        img.setCoords();
    }

    canvas.requestRenderAll();
    updateUndoRedoButtons();
}

function restoreFromString(str) {
  HISTORY.isRestoring = true;

  const json = JSON.parse(str);
  canvas.loadFromJSON(json, () => {
    // Po načtení z historie znovu najdi obrázek a nastav currentImage
    currentImage = canvas.getObjects().find(o => o.type === 'image') || null;
    HISTORY.isRestoring = false;
    cleanupAfterRestore();
    // Oprav pozici obrázku pokud existuje
    if (currentImage) {
      fitObjectToViewport(currentImage);
    }
    // Vždy aktualizuj stav tlačítek
    updateUndoRedoButtons();
  }, (o, object) => {

  });
}

function undo() {
  if (HISTORY.undoStack.length <= 1) return; 
  const current = HISTORY.undoStack.pop();
  HISTORY.redoStack.push(current);

  const prev = HISTORY.undoStack[HISTORY.undoStack.length - 1];
  restoreFromString(prev);
}

function redo() {
  if (!HISTORY.redoStack.length) return;
  const next = HISTORY.redoStack.pop();
  HISTORY.undoStack.push(next);
  restoreFromString(next);
}

function updateUndoRedoButtons() {
  const undoBtn = document.getElementById('undoBtn');
  const redoBtn = document.getElementById('redoBtn');
  if (undoBtn) undoBtn.disabled = HISTORY.undoStack.length <= 1;
  if (redoBtn) redoBtn.disabled = HISTORY.redoStack.length === 0;
}


let currentImage = null;

let cropRect = null;
let mode = 'resize';
const MAX_CANVAS_WIDTH = 900;
const MAX_CANVAS_HEIGHT = 600;
let previousAngle = 0;
//kresleni
let isPanning = false;
let isDown = false;
let lastPosX, lastPosY;

let drawMode = null; // 'line' | 'circle' | 'angle'
let line, circle, rect, triangle, rightTriangle, ellipse, star, heart, arrow, speechBubble, roundedSpeechBubble, roundedRect, plusShape, minusShape, crossShape;
let textBox = null;
let origX, origY;
let lastCreatedObject = null; // Spolehlivá reference na právě vytvořený objekt

// Vizuální kurzor gumy
let snapIndicator = null;
let lengthIndicator = null;
let historyBatch = false;
let filterHistoryTimer = null;

function scheduleFilterHistory() {
    clearTimeout(filterHistoryTimer);
    filterHistoryTimer = setTimeout(() => {
        saveHistoryState('filters');
    }, 300);
}

let textHistoryTimer = null;
function scheduleTextHistory() {
    clearTimeout(textHistoryTimer);
    textHistoryTimer = setTimeout(() => {
        saveHistoryState('text-style');
    }, 400);
}

let eraserCursor = null;
let ERASER_RADIUS = 20;

function showEraserCursor(pointer) {
    if (!eraserCursor) {
        eraserCursor = new fabric.Circle({
            radius: ERASER_RADIUS,
            fill: 'rgba(255, 255, 255, 1)',
            stroke: 'red',
            strokeWidth: 1,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            originX: 'center',
            originY: 'center'
        });
        canvas.add(eraserCursor);
    }
    eraserCursor.set({ left: pointer.x, top: pointer.y, visible: true });
    eraserCursor.bringToFront();
    canvas.requestRenderAll();
}

function hideEraserCursor() {
    if (eraserCursor) {
        eraserCursor.visible = false;
        canvas.requestRenderAll();
    }
}

canvas.on('mouse:down', (o) => {
    const e = o.e;
    const pointer = canvas.getPointer(e);

    if (drawMode === 'textBox') {
        isDown = true;
        origX = pointer.x;
        origY = pointer.y;
        textBox = new fabric.Rect({
            left: origX,
            top: origY,
            width: 1,
            height: 1,
            fill: 'rgba(255, 105, 180, 0.3)',
            stroke: 'hotpink',
            strokeWidth: 1,
            strokeDashArray: [5, 5],
            selectable: false,
            evented: false,
            excludeFromExport: true,
        });
        canvas.add(textBox);
        return;
    }

    if (drawMode) {
        isDown = true;
        historyBatch = true;

        const width = parseInt(document.getElementById('brushWidth').value);
        if (drawMode === 'line') {
            line = new fabric.Line([pointer.x, pointer.y, pointer.x, pointer.y], {
                 stroke: getStrokeColor(),
                 strokeWidth: width,
                 strokeDashArray: getDashFromUIForWidth(width),
                 strokeLineCap: document.getElementById('strokeCap').value,
                 strokeUniform: true,
                 selectable: false,
                 evented: false,
                 layer: 'draw'
            });
            canvas.add(line);
            lastCreatedObject = line;
        }

        if (drawMode === 'circle') {
            origX = pointer.x;
            origY = pointer.y;
            circle = new fabric.Circle({
                left: origX,
                top: origY,
                originX: 'left',
                originY: 'top',
                radius: 1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(circle);
            lastCreatedObject = circle;
        }

        if (drawMode === 'rect') {
            origX = pointer.x;
            origY = pointer.y;
            rect = new fabric.Rect({
                left: origX,
                top: origY,
                width: 1,
                height: 1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(rect);
            lastCreatedObject = rect;
        }

        if (drawMode === 'triangle') {
            origX = pointer.x;
            origY = pointer.y;
            triangle = new fabric.Polygon([
                { x: 0, y: 0 },
                { x: 1, y: 1 },
                { x: -1, y: 1 }
            ], {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(triangle);
            lastCreatedObject = triangle;
        }

        if (drawMode === 'rightTriangle') {
            origX = pointer.x;
            origY = pointer.y;
            rightTriangle = new fabric.Polygon([
                { x: 0, y: 0 },
                { x: 1, y: 1 },
                { x: 0, y: 1 }
            ], {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(rightTriangle);
            lastCreatedObject = rightTriangle;
        }

        if (drawMode === 'ellipse') {
            origX = pointer.x;
            origY = pointer.y;
            ellipse = new fabric.Ellipse({
                left: origX,
                top: origY,
                rx: 1,
                ry: 1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(ellipse);
            lastCreatedObject = ellipse;
        }

        if (drawMode === 'star') {
            origX = pointer.x;
            origY = pointer.y;
            const points = [];
            const spikes = 5;
            for (let i = 0; i < spikes * 2; i++) {
                const radius = i % 2 === 0 ? 1 : 0.5;
                const angle = (i * Math.PI) / spikes;
                points.push({
                    x: Math.cos(angle) * radius,
                    y: Math.sin(angle) * radius
                });
            }
            star = new fabric.Polygon(points, {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(star);
            lastCreatedObject = star;
        }

        if (drawMode === 'heart') {
            origX = pointer.x;
            origY = pointer.y;
            const path = 'M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 14,5.08C15.09,3.81 16.76,3 18.5,3C21.58,3 24,5.41 24,8.5C24,12.27 18.6,15.36 13.45,20.03L12,21.35Z';
            heart = new fabric.Path(path, {
                left: origX,
                top: origY,
                scaleX: 0.1,
                scaleY: 0.1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(heart);
            lastCreatedObject = heart;
        }

        if (drawMode === 'arrow') {
            origX = pointer.x;
            origY = pointer.y;
            arrow = new fabric.Polygon([
                { x: 0, y: 5 },
                { x: 15, y: 5 },
                { x: 15, y: 0 },
                { x: 20, y: 7.5 },
                { x: 15, y: 15 },
                { x: 15, y: 10 },
                { x: 0, y: 10 }
            ], {
                left: origX,
                top: origY,
                scaleX: 0.1,
                scaleY: 0.1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(arrow);
            lastCreatedObject = arrow;
        }

        if (drawMode === 'speechBubble') {
            origX = pointer.x;
            origY = pointer.y;
            speechBubble = new fabric.Polygon([
                { x: 0, y: 0 },
                { x: 100, y: 0 },
                { x: 100, y: 70 },
                { x: 20, y: 70 },
                { x: 10, y: 90 },
                { x: 15, y: 70 },
                { x: 0, y: 70 }
            ], {
                left: origX,
                top: origY,
                scaleX: 0.1,
                scaleY: 0.1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(speechBubble);
            lastCreatedObject = speechBubble;
        }

        if (drawMode === 'roundedSpeechBubble') {
            origX = pointer.x;
            origY = pointer.y;
            const path = 'M4,5 C4,3.89543 4.89543,3 6,3 H18 C19.1046,3 20,3.89543 20,5 V15 C20,16.1046 19.1046,17 18,17 H6L4,19V5Z';
            roundedSpeechBubble = new fabric.Path(path, {
                left: origX,
                top: origY,
                scaleX: 0.1,
                scaleY: 0.1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(roundedSpeechBubble);
            lastCreatedObject = roundedSpeechBubble;
        }

        if (drawMode === 'roundedRect') {
            origX = pointer.x;
            origY = pointer.y;
            roundedRect = new fabric.Rect({
                left: origX,
                top: origY,
                width: 1,
                height: 1,
                rx: 10,
                ry: 10,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(roundedRect);
            lastCreatedObject = roundedRect;
        }

        if (drawMode === 'arrowRight') {
            origX = pointer.x;
            origY = pointer.y;
            const path = 'M 0,10 Q 10,10 10,0 M 10,0 L 5,5 M 10,0 L 15,5';
            plusShape = new fabric.Path(path, {
                left: origX,
                top: origY,
                scaleX: 1,
                scaleY: 1,
                fill: 'transparent',
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(plusShape);
            lastCreatedObject = plusShape;
        }

        if (drawMode === 'hexagon') {
            origX = pointer.x;
            origY = pointer.y;
            const hexPoints = [];
            for (let i = 0; i < 6; i++) {
                const angle = (Math.PI / 3) * i - Math.PI / 6;
                hexPoints.push({
                    x: Math.cos(angle) * size,
                    y: Math.sin(angle) * size
                });
            }
            minusShape = new fabric.Polygon(hexPoints, {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(minusShape);
            lastCreatedObject = minusShape;
        }

        if (drawMode === 'cross') {
            origX = pointer.x;
            origY = pointer.y;
            const thinWidth = Math.max(1, width / 3);
            crossShape = new fabric.Group([
                new fabric.Line([0, 0, 20, 20], { stroke: getStrokeColor(), strokeWidth: thinWidth }),
                new fabric.Line([20, 0, 0, 20], { stroke: getStrokeColor(), strokeWidth: thinWidth })
            ], {
                left: origX,
                top: origY,
                scaleX: 0.1,
                scaleY: 0.1,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(crossShape);
            lastCreatedObject = crossShape;
        }

        return;
    }
});
// Kreslení tužkou
canvas.on('path:created', function (e) {
    e.path.set({
        layer: 'draw',
        erasable: true,
        selectable: false,
        evented: false,
        objectCaching: false
    });
});


canvas.on('mouse:move', (o) => {
    const e = o.e;
    const pointer = canvas.getPointer(e);
    
    if (drawMode === 'textBox' && isDown) {
        const width = pointer.x - origX;
        const height = pointer.y - origY;

        textBox.set({
            width: Math.abs(width),
            height: Math.abs(height),
            left: width < 0 ? pointer.x : origX,
            top: height < 0 ? pointer.y : origY
        });
        canvas.requestRenderAll();
        return;
    }

    // Zobrazení kurzoru gumy
    if (drawMode === 'eraser') {
        showEraserCursor(pointer);
        canvas.defaultCursor = 'none';
        canvas.hoverCursor = 'none';
    }
    
    // Guma - mazání při tažení
    if (drawMode === 'eraser' && isDown) {
        eraseAtPoint(pointer);
        return;
    }

    //  KRESLENÍ
    if (drawMode && isDown) {
        if (drawMode === 'line') {
            line.set({ x2: pointer.x, y2: pointer.y });
        }

        if (drawMode === 'circle') {
            const radius = Math.max(
                Math.abs(pointer.x - origX),
                Math.abs(pointer.y - origY)
            ) / 2;

            circle.set({
                radius,
                left: Math.min(pointer.x, origX),
                top: Math.min(pointer.y, origY)
            });
        }

        if (drawMode === 'rect' && isDown) {
    const width = pointer.x - origX;
    const height = pointer.y - origY;

    rect.set({
        width: Math.abs(width),
        height: Math.abs(height),
        left: width < 0 ? pointer.x : origX,
        top: height < 0 ? pointer.y : origY
    });

    canvas.requestRenderAll();
}

        if (drawMode === 'triangle' && isDown) {
            const width = pointer.x - origX;
            const height = pointer.y - origY;
            if (triangle) {
                canvas.remove(triangle);
            }
            triangle = new fabric.Polygon([
                { x: width / 2, y: 0 },
                { x: width, y: height },
                { x: 0, y: height }
            ], {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: parseInt(document.getElementById('brushWidth').value),
                strokeDashArray: getDashFromUIForWidth(parseInt(document.getElementById('brushWidth').value)),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(triangle);
            lastCreatedObject = triangle;
        }

        if (drawMode === 'rightTriangle' && isDown) {
            const width = pointer.x - origX;
            const height = pointer.y - origY;
            if (rightTriangle) {
                canvas.remove(rightTriangle);
            }
            rightTriangle = new fabric.Polygon([
                { x: 0, y: 0 },
                { x: width, y: height },
                { x: 0, y: height }
            ], {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: parseInt(document.getElementById('brushWidth').value),
                strokeDashArray: getDashFromUIForWidth(parseInt(document.getElementById('brushWidth').value)),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(rightTriangle);
            lastCreatedObject = rightTriangle;
        }

        if (drawMode === 'ellipse' && isDown) {
            const rx = Math.abs(pointer.x - origX) / 2;
            const ry = Math.abs(pointer.y - origY) / 2;
            ellipse.set({
                rx,
                ry,
                left: Math.min(pointer.x, origX),
                top: Math.min(pointer.y, origY)
            });
        }

        if (drawMode === 'star' && isDown) {
            const width = Math.abs(pointer.x - origX);
            const height = Math.abs(pointer.y - origY);
            const size = Math.max(width, height);
            if (star) {
                canvas.remove(star);
            }
            const points = [];
            const spikes = 5;
            for (let i = 0; i < spikes * 2; i++) {
                const radius = i % 2 === 0 ? size : size / 2;
                const angle = (i * Math.PI) / spikes - Math.PI / 2;
                points.push({
                    x: Math.cos(angle) * radius,
                    y: Math.sin(angle) * radius
                });
            }
            star = new fabric.Polygon(points, {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: parseInt(document.getElementById('brushWidth').value),
                strokeDashArray: getDashFromUIForWidth(parseInt(document.getElementById('brushWidth').value)),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(star);
            lastCreatedObject = star;
        }

        if (drawMode === 'heart' && isDown) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 50;
            heart.set({
                scaleX: scale,
                scaleY: scale
            });
        }

        if (drawMode === 'arrow' && isDown) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            arrow.set({
                scaleX: scale,
                scaleY: scale
            });
        }

        if (drawMode === 'speechBubble' && isDown) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 50;
            speechBubble.set({
                scaleX: scale,
                scaleY: scale
            });
        }

        if (drawMode === 'roundedSpeechBubble' && isDown) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            roundedSpeechBubble.set({
                scaleX: scale,
                scaleY: scale
            });
        }

        if (drawMode === 'roundedRect' && isDown) {
            const width = pointer.x - origX;
            const height = pointer.y - origY;
            roundedRect.set({
                width: Math.abs(width),
                height: Math.abs(height),
                left: width < 0 ? pointer.x : origX,
                top: height < 0 ? pointer.y : origY
            });
        }

        if (drawMode === 'arrowRight' && isDown) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 15;
            plusShape.set({
                scaleX: scale,
                scaleY: scale
            });
        }

        if (drawMode === 'hexagon' && isDown) {
            const size = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY));
            if (minusShape) {
                canvas.remove(minusShape);
            }
            const hexPoints = [];
            for (let i = 0; i < 6; i++) {
                const angle = (Math.PI / 3) * i - Math.PI / 6;
                hexPoints.push({
                    x: Math.cos(angle) * size,
                    y: Math.sin(angle) * size
                });
            }
            minusShape = new fabric.Polygon(hexPoints, {
                left: origX,
                top: origY,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: parseInt(document.getElementById('brushWidth').value),
                strokeDashArray: getDashFromUIForWidth(parseInt(document.getElementById('brushWidth').value)),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(minusShape);
            lastCreatedObject = minusShape;
        }

        if (drawMode === 'cross' && isDown) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            crossShape.set({
                scaleX: scale,
                scaleY: scale
            });
        }

        canvas.requestRenderAll();
        return;
    }

    //  PAN
    if (isPanning) {
        const vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - lastPosX;
        vpt[5] += e.clientY - lastPosY;
        canvas.requestRenderAll();

        lastPosX = e.clientX;
        lastPosY = e.clientY;
    }
});

canvas.on('mouse:up', () => {
    if (drawMode === 'textBox' && isDown) {
        isDown = false;
        
        let textObjectData;

        // Check if it was a click (small drag)
        if (textBox.width < 10 || textBox.height < 10) {
            // It's a click, create a textbox with predefined size
            textObjectData = {
                left: origX,
                top: origY,
                width: 200, // Predefined width
            };
        } else {
            // It's a drag, use the dragged dimensions
            textObjectData = {
                left: textBox.left,
                top: textBox.top,
                width: textBox.width,
            };
        }

        canvas.remove(textBox); // remove the temporary rectangle
        textBox = null;

        const text = new fabric.Textbox(document.getElementById('textInput').value || 'Text', {
            ...textObjectData,
            fontSize: parseInt(document.getElementById('textSize').value),
            fill: document.getElementById('textColor').value,
            fontFamily: document.getElementById('textFont').value,
            textAlign: currentTextAlign,
            fontWeight: textStyles.bold ? 'bold' : 'normal',
            fontStyle: textStyles.italic ? 'italic' : 'normal',
            underline: textStyles.underline,
            linethrough: textStyles.linethrough,
            layer: 'text',
            erasable: false,
        });
        canvas.add(text);
        
        setTextEditingMode('select');
        canvas.setActiveObject(text);
        canvas.requestRenderAll();
        
        saveHistoryState('text-draw');
        return;
    }

    if (drawMode && isDown) {
        // Použít spolehlivou referenci na právě vytvořený objekt
        const objToSelect = lastCreatedObject;
        
        if (objToSelect) {
            objToSelect.set({
                selectable: true,
                evented: true
            });
            
            // Automaticky přepnout na kurzor a vybrat objekt
            canvas.isDrawingMode = false;
            drawMode = null;
            canvas.selection = true;
            lockImage(false);
            setActiveTool(document.getElementById('drawSelectBtn'));
            
            canvas.getObjects().forEach(obj => {
                const isBlank = obj._element?.src?.includes('blank_');
                obj.selectable = !isBlank;
                obj.evented = !isBlank;
            });
            
            // Vybrat nakreslený objekt
            setTimeout(() => {
                canvas.setActiveObject(objToSelect);
                canvas.requestRenderAll();
            }, 0);
        }
        
        // Vynulovat všechny reference
        line = null;
        circle = null;
        rect = null;
        triangle = null;
        rightTriangle = null;
        ellipse = null;
        star = null;
        heart = null;
        arrow = null;
        speechBubble = null;
        roundedSpeechBubble = null;
        roundedRect = null;
        plusShape = null;
        minusShape = null;
        crossShape = null;
        lastCreatedObject = null;
        isDown = false;
        historyBatch = false;
        saveHistoryState('draw-finished');
        canvas.requestRenderAll();
    }
});

/*
canvas.on("object:moving", function(e) {
    if (!e.target) return;
    keepInsideCanvas(e.target);
});

canvas.on('object:scaling', function(e) {
    const obj = e.target;
    if (!obj) return;

    const canvasWidth = canvas.getWidth();
    const canvasHeight = canvas.getHeight();
    const bound = obj.getBoundingRect(false); 

    let newWidth = obj.width * obj.scaleX;
    let newHeight = obj.height * obj.scaleY;

    if (newWidth > canvasWidth) {
        obj.scaleX = canvasWidth / obj.width;
    }
    if (newHeight > canvasHeight) {
        obj.scaleY = canvasHeight / obj.height;
    }

    if (obj === currentImage) {
        if (obj.width * obj.scaleX > originalImageWidth)
            obj.scaleX = originalImageWidth / obj.width;
        if (obj.height * obj.scaleY > originalImageHeight)
            obj.scaleY = originalImageHeight / obj.height;

    } else if (obj === cropRect) {
        if (obj.width * obj.scaleX > originalCropWidth)
            obj.scaleX = originalCropWidth / obj.width;
        if (obj.height * obj.scaleY > originalCropHeight)
            obj.scaleY = originalCropHeight / obj.height;
    }

    keepInsideCanvas(obj);

    obj.setCoords();
});

*/

canvas.on("object:rotating", function(e) {
    const obj = e.target;
    if (!obj) return;

    const maxW = originalImageWidth;
    const maxH = originalImageHeight;

    const realW = obj.getScaledWidth();
    const realH = obj.getScaledHeight();

    if (realW > maxW) obj.scaleX *= maxW / realW;
    if (realH > maxH) obj.scaleY *= maxH / realH;

    obj.setCoords();
    keepInsideCanvas(obj);
    updateImageSize();
});


// Načtení obrázku z URL poslané z indexu
const imageUrl = @json(request('path'));
if (imageUrl) loadImage(imageUrl);

// Funkce pro načtení obrázku do Fabric canvasu

let originalImageWidth, originalImageHeight;
let originalCropWidth, originalCropHeight;

function loadImage(url) {
    fabric.Image.fromURL(url, (img) => {
        if (currentImage) canvas.remove(currentImage);

        currentImage = img;
        img.layer = 'image';
        originalImageWidth = img.width;
        originalImageHeight = img.height;

     const isBlank = url.includes('blank_');
img.set({
    originX: 'center',
    originY: 'center',
    selectable: !isBlank, // pokud je blank, není selectovatelné
    evented: !isBlank,    // pokud je blank, nereaguje na události
    lockMovementX: isBlank,
    lockMovementY: isBlank,
    lockScalingX: isBlank,
    lockScalingY: isBlank,
    lockRotation: isBlank,
    hasRotatingPoint: !isBlank,
    cornerStyle: 'circle'
});
if (isBlank) img.sendToBack();

// Skrýt/zobrazit tlačítka pravítka a uzamykání podle typu obrázku
document.getElementById('toggleRulerBtn').style.display = isBlank ? 'none' : '';
document.getElementById('lockObjectBtn').style.display = isBlank ? 'none' : '';

// Vypnout pravítko pokud je aktivní a přepneme na šablonu
if (isBlank && rulerEnabled) {
    rulerEnabled = false;
    document.getElementById('toggleRulerBtn').classList.remove('active');
    removeRulers();
}

        canvas.add(img);


        fitImageToCanvas(img);
        fitObjectToViewport(img);
        // saveHistoryState('init'); // Zrušeno: neukládat stav hned po načtení obrázku
        updateImageSize();
    }, { crossOrigin: 'anonymous' });
}

// Přizpůsobení obrázku canvasu
function fitImageToCanvas(img) {
    img.set({ scaleX: 1, scaleY: 1 });
    canvas.setWidth(MAX_CANVAS_WIDTH);
    canvas.setHeight(MAX_CANVAS_HEIGHT);
    canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);

    img.left = canvas.width / 2;
    img.top = canvas.height / 2;
    img.setCoords();
    canvas.requestRenderAll();
}

// Zoom a centrování objektu
function fitObjectToViewport(obj) {
    if (!obj) return;

    canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
    canvas.setWidth(MAX_CANVAS_WIDTH);
    canvas.setHeight(MAX_CANVAS_HEIGHT);
    canvas.renderAll();

    const bbox = obj.getBoundingRect(true);
    const scaleX = (canvas.width - 40) / bbox.width;
    const scaleY = (canvas.height - 40) / bbox.height;
    const scale = Math.min(scaleX, scaleY, 1);

    const center = new fabric.Point(canvas.width / 2, canvas.height / 2);
    canvas.zoomToPoint(center, scale);

    const objCenter = obj.getCenterPoint();
    const zoom = canvas.getZoom();
    canvas.viewportTransform[4] = (canvas.width / 2) - (objCenter.x * zoom);
    canvas.viewportTransform[5] = (canvas.height / 2) - (objCenter.y * zoom);

    canvas.requestRenderAll();
}

// Resize / Crop
document.getElementById('toggleMode').addEventListener('click', () => {
    if (mode === 'resize') {
        mode = 'crop';
        canvas.selection = false;
        document.getElementById('toggleMode').textContent = 'Režim: Ořez';
        if (!currentImage) return;

        cropRect = new fabric.Rect({
            left: currentImage.left,
            top: currentImage.top,
            width: currentImage.width * currentImage.scaleX,
            height: currentImage.height * currentImage.scaleY,
            fill: 'rgba(0,0,0,0.3)',
            originX: 'center',
            originY: 'center',
            angle: currentImage.angle,
            selectable: true,
            hasBorders: true,
            cornerStyle: 'circle'
        });

        canvas.add(cropRect);
        canvas.setActiveObject(cropRect);
    } else {
        mode = 'resize';
        document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
        if (cropRect) canvas.remove(cropRect);
        canvas.selection = true;
    }
});

// Crop obrázku
document.getElementById('cropBtn').addEventListener('click', async () => {
    if (!currentImage || !cropRect) return;

    historyBatch = true;
    const img = currentImage;
    const rect = cropRect;

    // Získání hranice crop oblasti v souřadnicích canvasu
    const cropBounds = rect.getBoundingRect(true);
    const cropLeft = cropBounds.left;
    const cropTop = cropBounds.top;
    const cropVisualWidth = cropBounds.width;
    const cropVisualHeight = cropBounds.height;

    // Uložení všech objektů (kreslení + text)
    const drawObjectsToClone = canvas.getObjects().filter(obj => 
        (obj.layer === 'draw' || obj.layer === 'text') && obj !== eraserCursor
    );
    
    const clonePromises = drawObjectsToClone.map(obj => {
        return new Promise(resolve => {
            obj.clone(cloned => {
                // Přepočet pozice relativně k crop oblasti
                const relativeLeft = obj.left - cropLeft;
                const relativeTop = obj.top - cropTop;
                
                cloned.set({
                    left: relativeLeft,
                    top: relativeTop,
                    layer: obj.layer 
                });
                
                resolve(cloned);
            });
        });
    });
    
    const drawObjects = await Promise.all(clonePromises);

    const cropCenter = rect.getCenterPoint();
    const localPoint = img.toLocalPoint(cropCenter, 'center', 'center');

    const cropWidth = rect.width * rect.scaleX / img.scaleX;
    const cropHeight = rect.height * rect.scaleY / img.scaleY;

    const left = (localPoint.x / img.scaleX) + (img.width / 2) - (cropWidth / 2);
    const top = (localPoint.y / img.scaleY) + (img.height / 2) - (cropHeight / 2);

    const exportCanvas = document.createElement("canvas");
    exportCanvas.width = cropWidth;
    exportCanvas.height = cropHeight;
    const ctx = exportCanvas.getContext("2d");

    ctx.drawImage(
        img._element,      
        left, top,         
        cropWidth, cropHeight,
        0, 0,             
        cropWidth, cropHeight 
    );

    const url = exportCanvas.toDataURL("image/png");

    fabric.Image.fromURL(url, newImg => {
        canvas.clear();
        currentImage = newImg;

        newImg.set({
            originX: 'center',
            originY: 'center',
            selectable: true,
            hasRotatingPoint: true,
            cornerStyle: 'circle'
        });
        newImg.layer = 'image';

        canvas.add(newImg);
        
        canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
        fitImageToCanvas(newImg);
        fitObjectToViewport(newImg);
        updateImageSize();

        // Přepočet škálování pro kresby
        const newImgBounds = newImg.getBoundingRect(true);
        const scaleRatioX = newImgBounds.width / cropVisualWidth;
        const scaleRatioY = newImgBounds.height / cropVisualHeight;
        
        // Offset nového obrázku (jeho levý horní roh)
        const newImgLeft = newImgBounds.left;
        const newImgTop = newImgBounds.top;

        // Přidání kreseb zpět s novými pozicemi
        drawObjects.forEach(obj => {
            // Škálování pozice a velikosti podle nového obrázku
            const scaledLeft = obj.left * scaleRatioX + newImgLeft;
            const scaledTop = obj.top * scaleRatioY + newImgTop;
            
            obj.set({
                left: scaledLeft,
                top: scaledTop,
                scaleX: (obj.scaleX || 1) * scaleRatioX,
                scaleY: (obj.scaleY || 1) * scaleRatioY
            });
            
            obj.setCoords();
            canvas.add(obj);
        });

        canvas.requestRenderAll();

        cropRect = null;
        mode = 'resize';
        document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
        canvas.selection = true;

        historyBatch = false;
        saveHistoryState('crop');
    });
});


// Filtry / Jas / Kontrast / Sytost
let activeFilter = null;

function applyFilters() {
    if (!currentImage || !currentImage.filters) return;

    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    const filters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];

    if (activeFilter) filters.push(activeFilter);

    currentImage.filters = filters;
    currentImage.applyFilters();
    canvas.requestRenderAll();
}

document.querySelectorAll('.filter-thumb').forEach(thumb => {
    thumb.addEventListener('click', () => {
        document.querySelectorAll('.filter-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');

        const type = thumb.getAttribute('data-filter');
        switch(type){
            case 'grayscale': activeFilter = new fabric.Image.filters.Grayscale(); break;
            case 'sepia': activeFilter = new fabric.Image.filters.Sepia(); break;
            case 'invert': activeFilter = new fabric.Image.filters.Invert(); break;
            case 'blur': activeFilter = new fabric.Image.filters.Blur({ blur: 0.3 }); break;
            case 'sharpen': activeFilter = new fabric.Image.filters.Convolute({ matrix: [0, -1, 0, -1, 5, -1, 0, -1, 0] }); break;
            default: activeFilter = null; break;
        }

        applyFilters();
        scheduleFilterHistory();
    });
});

document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', applyFilters);
});

// Posuvníky
document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', () => { applyFilters(); scheduleFilterHistory(); });
});

// Drag / Pan
/*let isDragging=false, lastPosX, lastPosY;
canvas.on('mouse:down', opt => {
    const evt = opt.e;
    if (evt.altKey) {
        isDragging = true;
        canvas.selection = false;
        lastPosX = evt.clientX;
        lastPosY = evt.clientY;
    }
});
canvas.on('mouse:move', opt => {
    if (!isDragging) return;
    const e = opt.e;
    const vpt = canvas.viewportTransform;
    vpt[4] += e.clientX - lastPosX;
    vpt[5] += e.clientY - lastPosY;
    canvas.requestRenderAll();
    lastPosX = e.clientX;
    lastPosY = e.clientY;
});
canvas.on('mouse:up', () => {
    isDragging = false;
    canvas.selection = true;
});*/

// Update size label
function updateImageSize() {
    if (!currentImage) return;
    const width = Math.round(currentImage.width * currentImage.scaleX);
    const height = Math.round(currentImage.height * currentImage.scaleY);
    document.getElementById('imageSize').textContent = `Velikost: ${width} × ${height} px`;
}

canvas.on('object:modified', () => {
    if (!currentImage) return;
    const angle = currentImage.angle || 0;
    if (angle !== previousAngle) {
        previousAngle = angle;
        fitObjectToViewport(currentImage);
    }
    updateImageSize();
    updateRotationAngle();
});

function updateRotationAngle() {
    if (!currentImage) return;
    const angle = Math.round(currentImage.angle || 0);
    document.getElementById('rotationAngle').textContent = `Otočení: ${angle}°`;
}
// aby crop a img nešel mimo canvas
function keepInsideCanvas(obj) {
    const padding = 0;
    const canvasWidth = canvas.getWidth();
    const canvasHeight = canvas.getHeight();
    const bound = obj.getBoundingRect(true, true);

    let offsetX = 0;
    let offsetY = 0;

    // horizontální omezení
    if (bound.width <= canvasWidth) {
        if (bound.left < padding) offsetX = padding - bound.left;
        if (bound.left + bound.width > canvasWidth - padding)
            offsetX = (canvasWidth - padding) - (bound.left + bound.width);
    } else {
        // pokud je širší než canvas, drž střed uvnitř
        if (obj.left < canvasWidth/2) offsetX = (canvasWidth/2) - obj.left;
        if (obj.left > canvasWidth/2) offsetX = (canvasWidth/2) - obj.left;
    }

    // vertikální omezení
    if (bound.height <= canvasHeight) {
        if (bound.top < padding) offsetY = padding - bound.top;
        if (bound.top + bound.height > canvasHeight - padding)
            offsetY = (canvasHeight - padding) - (bound.top + bound.height);
    } else {
        // pokud je vyšší než canvas, drž střed uvnitř
        if (obj.top < canvasHeight/2) offsetY = (canvasHeight/2) - obj.top;
        if (obj.top > canvasHeight/2) offsetY = (canvasHeight/2) - obj.top;
    }

    obj.left += offsetX;
    obj.top += offsetY;
    obj.setCoords();
}

// download
document.getElementById('startDownloadBtn').addEventListener('click', () => {
    if (!currentImage) {
        alert("Nejdříve načtěte obrázek, který chcete exportovat!");
        return;
    }

    hideEraserCursor();

    const format = document.getElementById('exportFormat').value;
    const contentType = document.querySelector('input[name="exportContent"]:checked').value;
    const quality = 0.9;
    const mimeType = `image/${format === 'jpeg' ? 'jpeg' : format === 'webp' ? 'webp' : 'png'}`;
    let filenameInput = document.getElementById('exportFileName').value.trim();
    let filename = filenameInput ? filenameInput.replace(/\.[^.]+$/, '') + '.' + format : `export.${format}`;
    let dataURL;

    if (contentType === 'canvas') {
        if (!filenameInput) filename = `canvas_export.${format}`;
        dataURL = canvas.toDataURL({ format: format, quality: quality, multiplier: 1 });
    } else {
        if (!filenameInput) filename = `image_only.${format}`;
    const img = currentImage;

    // bounding box obrázku (včetně rotace)
    const bbox = img.getBoundingRect(true);

    const exportCanvas = document.createElement('canvas');
    exportCanvas.width = Math.ceil(bbox.width);
    exportCanvas.height = Math.ceil(bbox.height);
    const exportCtx = exportCanvas.getContext('2d');

    //  posun světa tak, aby levý horní roh bbox byl (0,0)
    exportCtx.save();
    exportCtx.translate(-bbox.left, -bbox.top);

    // vykreslení všech objektů kromě pozadí canvasu a kurzoru gumy
    canvas.getObjects().forEach(obj => {
        if (obj.visible === false) return;
        if (obj === eraserCursor) return; // Přeskočíme kurzor gumy

        exportCtx.save();
        obj.render(exportCtx);
        exportCtx.restore();
    });

    exportCtx.restore();

    dataURL = exportCanvas.toDataURL(mimeType, quality);
}
    
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = filename;
    link.click();
    
//Ukládní na server
   fetch('/save-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            image: dataURL,
            format: format
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Uloženo do uploads:', data.path);
    })
    .catch(error => console.error('Chyba při ukládání na server:', error));
});

function getFillColor() {
    return document.getElementById('fillTransparent').checked ? 'transparent' : document.getElementById('fillColor').value;
}

function getStrokeColor() {
    return document.getElementById('drawColor').value;
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        // vypnout kreslení
        drawMode = null;
        isDown = false;
        
        // Skrýt kurzor gumy
        hideEraserCursor();
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        canvas.isDrawingMode = false;

        // vypnout crop
        if (cropRect) {
            canvas.remove(cropRect);
            cropRect = null;
            mode = 'resize';
            document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
        }

        canvas.selection = true;
        
        // Odemknout obrázek při opuštění sekce kreslení (pokud není šablona)
        if (currentImage) {
            const isBlank = currentImage._element?.src?.includes('blank_');
            if (!isBlank) {
                currentImage.set({
                    lockMovementX: false,
                    lockMovementY: false,
                    lockScalingX: false,
                    lockScalingY: false,
                    lockRotation: false,
                    hasControls: true,
                    hasBorders: true,
                    selectable: true,
                    evented: true
                });
                // Resetovat stav tlačítka zámku
                document.getElementById('lockObjectBtn').classList.remove('active');
            }
        }

        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(btn.dataset.target).classList.remove('hidden');

        if (btn.dataset.target === 'panelDraw') {
    drawMode = null;
    canvas.selection = true;
    lockImage(false);

    setActiveTool(document.getElementById('drawSelectBtn'));

    canvas.getObjects().forEach(obj => {
        const isBlank = obj._element?.src?.includes('blank_');
        obj.selectable = !isBlank;
        obj.evented = !isBlank;
    });

    canvas.discardActiveObject();
    canvas.requestRenderAll();
}
    });
});

function enableDrawing(mode) {
    canvas.isDrawingMode = false;
    drawMode = mode;
    canvas.selection = false;
    canvas.discardActiveObject();

    // Zakázat výběr všech objektů během kreslení
    canvas.getObjects().forEach(obj => {
        const isBlank = obj._element?.src?.includes('blank_');
        obj.selectable = false;
        obj.evented = false;
    });

    if (currentImage) {
        const isBlank = currentImage._element?.src?.includes('blank_');
        currentImage.selectable = !isBlank;
        currentImage.evented = !isBlank;
    }
}

document.getElementById('drawLineBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('line');
    lockImage(true);
});
document.getElementById('drawCircleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('circle');
    lockImage(true);
});

document.getElementById('drawRectBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('rect');
    lockImage(true);
});
document.getElementById('drawTriangleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('triangle');
    lockImage(true);
});
document.getElementById('drawRightTriangleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('rightTriangle');
    lockImage(true);
});
document.getElementById('drawEllipseBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('ellipse');
    lockImage(true);
});
document.getElementById('drawStarBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('star');
    lockImage(true);
});
document.getElementById('drawHeartBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('heart');
    lockImage(true);
});
document.getElementById('drawArrowBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('arrow');
    lockImage(true);
});
document.getElementById('drawSpeechBubbleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('speechBubble');
    lockImage(true);
});
document.getElementById('drawRoundedSpeechBubbleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('roundedSpeechBubble');
    lockImage(true);
});
document.getElementById('drawRoundedRectBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('roundedRect');
    lockImage(true);
});
document.getElementById('drawArrowRightBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('arrowRight');
    lockImage(true);
});
document.getElementById('drawHexagonBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('hexagon');
    lockImage(true);
});
document.getElementById('drawCrossBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('cross');
    lockImage(true);
});
document.getElementById('drawSelectBtn').onclick = () => {
    canvas.isDrawingMode = false;
    drawMode = null;
    canvas.selection = true;
    lockImage(false);
    setActiveTool(document.getElementById('drawSelectBtn'));

    canvas.getObjects().forEach(obj => {
        const isBlank = obj._element?.src?.includes('blank_');
        obj.selectable = !isBlank;
        obj.evented = !isBlank;
    });

    canvas.discardActiveObject();
    canvas.requestRenderAll();
};
document.getElementById('drawBrushBtn').addEventListener('click', function () {
    setActiveTool(this);

    drawMode = null;
    canvas.selection = false;
    lockImage(true);

    canvas.isDrawingMode = true;
    canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
    canvas.freeDrawingBrush.color = getStrokeColor();
    canvas.freeDrawingBrush.width = parseInt(document.getElementById('brushWidth').value)
});

// Funkce pro mazání
function eraseAtPoint(pointer) {
    const eraserRadius = ERASER_RADIUS;
    const objects = canvas.getObjects().slice();

    for (let i = objects.length - 1; i >= 0; i--) {
        const obj = objects[i];

        if (!obj.erasable || obj.type !== 'path') continue;

        const pathData = obj.path;
        if (!pathData || pathData.length < 2) continue;

        // Získá transformační matici
        const matrix = obj.calcTransformMatrix();
        const offsetX = obj.pathOffset ? obj.pathOffset.x : 0;
        const offsetY = obj.pathOffset ? obj.pathOffset.y : 0;

        const eraseIndices = new Set();

        for (let j = 0; j < pathData.length; j++) {
            const seg = pathData[j];
            let px, py;

            if (seg[0] === 'M' || seg[0] === 'L') {
                px = seg[1];
                py = seg[2];
            } else if (seg[0] === 'Q') {
                px = seg[3];
                py = seg[4];
            } else if (seg[0] === 'C') {
                px = seg[5];
                py = seg[6];
            } else {
                continue;
            }

            // odečte offset a aplikuje matici
            const localPoint = new fabric.Point(px - offsetX, py - offsetY);
            const canvasPoint = fabric.util.transformPoint(localPoint, matrix);
            
            const dist = Math.hypot(pointer.x - canvasPoint.x, pointer.y - canvasPoint.y);

            if (dist <= eraserRadius) {
                // Maže body které jsou pod kurzorem
                eraseIndices.add(j);
            }
        }

        if (eraseIndices.size === 0) continue;

        canvas.remove(obj);

        if (eraseIndices.size >= pathData.length - 2) {
            canvas.requestRenderAll();
            continue;
        }

        const newPathData = [];
        let needNewMove = false;

        for (let j = 0; j < pathData.length; j++) {
            if (eraseIndices.has(j)) {
                needNewMove = true;
                continue;
            }

            const seg = [...pathData[j]];

            if (needNewMove && seg[0] !== 'M') {
                if (seg[0] === 'L') {
                    newPathData.push(['M', seg[1], seg[2]]);
                } else if (seg[0] === 'Q') {
                    newPathData.push(['M', seg[3], seg[4]]);
                } else if (seg[0] === 'C') {
                    newPathData.push(['M', seg[5], seg[6]]);
                }
                needNewMove = false;
                continue;
            }

            newPathData.push(seg);
        }

        if (newPathData.length >= 2) {
            try {
                const pathString = newPathData.map(seg => seg.join(' ')).join(' ');
                const newPath = new fabric.Path(pathString, {
                    stroke: obj.stroke,
                    strokeWidth: obj.strokeWidth,
                    fill: null,
                    strokeLineCap: 'round',
                    strokeLineJoin: 'round',
                    selectable: false,
                    evented: false,
                    erasable: true,
                    layer: 'draw',
                    objectCaching: false
                });
                canvas.add(newPath);
            } catch (e) {}
        }

        canvas.requestRenderAll();
    }
}

document.getElementById('drawEraserBtn').addEventListener('click', function () {
    setActiveTool(this);
    drawMode = 'eraser';
    canvas.isDrawingMode = false;
    canvas.selection = false;
    canvas.defaultCursor = 'none';
    canvas.hoverCursor = 'none';
    lockImage(true);
});

// Pravítko - toggle zobrazení rozměrů
let rulerEnabled = false;
let rulerLines = [];
let rulerTexts = [];

document.getElementById('toggleRulerBtn').addEventListener('click', function() {
    rulerEnabled = !rulerEnabled;
    this.classList.toggle('active', rulerEnabled);
    
    if (rulerEnabled) {
        drawRulers();
    } else {
        removeRulers();
    }
});

function drawRulers() {
    removeRulers();
    
    // Získat viewport transform pro správné umístění
    const vpt = canvas.viewportTransform;
    const zoom = canvas.getZoom();
    
    // Vypočítat viditelnou oblast
    const visibleLeft = -vpt[4] / zoom;
    const visibleTop = -vpt[5] / zoom;
    const visibleWidth = canvas.getWidth() / zoom;
    const visibleHeight = canvas.getHeight() / zoom;
    
    const step = 50; // každých 50px
    
    // Zaokrouhlit start na násobek stepu
    const startX = Math.floor(visibleLeft / step) * step;
    const startY = Math.floor(visibleTop / step) * step;
    
    // Horizontální pravítko (nahoře na viditelné oblasti)
    for (let x = startX; x <= visibleLeft + visibleWidth; x += step) {
        const line = new fabric.Line([x, visibleTop, x, visibleTop + 15 / zoom], {
            stroke: '#666',
            strokeWidth: 1 / zoom,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            isRuler: true
        });
        const text = new fabric.Text(Math.round(x).toString(), {
            left: x + 2 / zoom,
            top: visibleTop + 2 / zoom,
            fontSize: 10 / zoom,
            fill: '#666',
            selectable: false,
            evented: false,
            excludeFromExport: true,
            isRuler: true
        });
        canvas.add(line, text);
        rulerLines.push(line);
        rulerTexts.push(text);
    }
    
    // Vertikální pravítko (vlevo na viditelné oblasti)
    for (let y = startY; y <= visibleTop + visibleHeight; y += step) {
        const line = new fabric.Line([visibleLeft, y, visibleLeft + 15 / zoom, y], {
            stroke: '#666',
            strokeWidth: 1 / zoom,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            isRuler: true
        });
        const text = new fabric.Text(Math.round(y).toString(), {
            left: visibleLeft + 2 / zoom,
            top: y + 2 / zoom,
            fontSize: 10 / zoom,
            fill: '#666',
            selectable: false,
            evented: false,
            excludeFromExport: true,
            isRuler: true
        });
        canvas.add(line, text);
        rulerLines.push(line);
        rulerTexts.push(text);
    }
    
    canvas.requestRenderAll();
}

function removeRulers() {
    rulerLines.forEach(line => canvas.remove(line));
    rulerTexts.forEach(text => canvas.remove(text));
    rulerLines = [];
    rulerTexts = [];
    canvas.requestRenderAll();
}

// Zamknutí/odemknutí obrázku
document.getElementById('lockObjectBtn').addEventListener('click', function() {
    if (!currentImage) {
        alert('Není načten žádný obrázek');
        return;
    }
    
    // Šablony (blank) nelze odemknout
    const isBlank = currentImage._element?.src?.includes('blank_');
    if (isBlank) {
        alert('Šablonu nelze odemknout');
        return;
    }
    
    // Toggle lock stavu
    const isLocked = currentImage.lockMovementX && currentImage.lockMovementY;
    
    currentImage.set({
        lockMovementX: !isLocked,
        lockMovementY: !isLocked,
        lockScalingX: !isLocked,
        lockScalingY: !isLocked,
        lockRotation: !isLocked,
        hasControls: isLocked,
        hasBorders: isLocked,
        selectable: isLocked,
        evented: isLocked
    });
    
    // Vizualizace stavu tlačítka
    this.classList.toggle('active', !isLocked);
    
    canvas.discardActiveObject();
    canvas.requestRenderAll();
});

// Skrytí kurzoru gumy při změně nástroje
function setActiveTool(btn) {
    hideEraserCursor();
    canvas.defaultCursor = 'default';
    canvas.hoverCursor = 'move';
    
    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

//zamknutí obrázku
function lockImage(lock = true) {
    if (!currentImage) return;

    currentImage.set({
        selectable: !lock,
        evented: !lock,
        lockMovementX: lock,
        lockMovementY: lock,
        lockScalingX: lock,
        lockScalingY: lock,
        lockRotation: lock
    });
}
//odstranění objektu klávesou Delete/Backspace
document.addEventListener('keydown', (e) => {
    if (e.key === 'Delete' || e.key === 'Backspace') {
        const activeObj = canvas.getActiveObject();
        if (!activeObj) return;
        
        // Nikdy nemazat obrázek
        if (activeObj === currentImage) return;

        // Pokud je to text v editačním režimu, necháme Fabric.js zpracovat klávesu
        if ((activeObj.type === 'i-text' || activeObj.type === 'textbox') && activeObj.isEditing) {
            return; // Nemazat objekt, nechat smazat písmeno
        }

        const isBlank = activeObj._element?.src?.includes('blank_');
        if (!isBlank) {
            canvas.remove(activeObj);
        }

        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }
});

function updateLayersVisibility() {
    const showImage = document.querySelector('.layerImageCheck').checked;
    const showDraw = document.querySelector('.layerDrawCheck').checked;
    const showText = document.querySelector('.layerTextCheck').checked;

    canvas.getObjects().forEach(obj => {
        if (obj.layer === 'image') obj.visible = showImage;
        if (obj.layer === 'draw') obj.visible = showDraw;
        if (obj.layer === 'text') obj.visible = showText;
    });

    canvas.requestRenderAll();
}

// Synchronizace všech checkboxů vrstev
function syncLayerCheckboxes(layerClass, checked) {
    document.querySelectorAll(`.${layerClass}`).forEach(cb => cb.checked = checked);
    updateLayersVisibility();
}

document.querySelectorAll('.layerImageCheck').forEach(cb => {
    cb.addEventListener('change', (e) => syncLayerCheckboxes('layerImageCheck', e.target.checked));
});
document.querySelectorAll('.layerDrawCheck').forEach(cb => {
    cb.addEventListener('change', (e) => syncLayerCheckboxes('layerDrawCheck', e.target.checked));
});
document.querySelectorAll('.layerTextCheck').forEach(cb => {
    cb.addEventListener('change', (e) => syncLayerCheckboxes('layerTextCheck', e.target.checked));
});

// Synchronizace UI s vybraným objektem
canvas.on('selection:created', handleSelectionChange);
canvas.on('selection:updated', handleSelectionChange);
canvas.on('selection:cleared', handleSelectionChange);

function handleSelectionChange(e) {
    const activeObject = canvas.getActiveObject();

    if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox')) {
        // It's a text object, update the text controls
        updateTextControlsUI(activeObject);
    } else {
        // It's a drawing object or selection was cleared
        if (activeObject) {
            syncDrawingControls(activeObject);
        }
        // When selection is cleared, or it's not a text object, reset text controls
        resetTextControlsUI();
    }
}

function syncDrawingControls(obj) {
    if (!obj || obj === currentImage || obj === cropRect || obj === eraserCursor) return;

    // Synchronizace barvy obrysu
    if (obj.stroke) {
        document.getElementById('drawColor').value = obj.stroke;
    }

    // Synchronizace barvy výplně
    if (obj.fill === '' || obj.fill === null || obj.fill === 'transparent') {
        document.getElementById('fillTransparent').checked = true;
    } else if (obj.fill) {
        document.getElementById('fillColor').value = obj.fill;
        document.getElementById('fillTransparent').checked = false;
    }

    // Synchronizace šířky čáry
    if (obj.strokeWidth) {
        document.getElementById('brushWidth').value = obj.strokeWidth;
    }

    // Synchronizace stylu čáry
    const dash = obj.strokeDashArray;
    if (!dash || dash.length === 0) {
        document.getElementById('strokeStyle').value = 'solid';
    } else if (dash[0] > dash[1]) {
        document.getElementById('strokeStyle').value = 'dashed';
    } else {
        document.getElementById('strokeStyle').value = 'dotted';
    }

    // Synchronizace zakončení čáry
    if (obj.strokeLineCap) {
        document.getElementById('strokeCap').value = obj.strokeLineCap;
    }
}

document.getElementById('drawColor').addEventListener('input', () => {
    // Aktualizace aktivního objektu
    const activeObj = canvas.getActiveObject();
    if (activeObj && activeObj !== currentImage && activeObj !== cropRect && activeObj.stroke !== undefined) {
        activeObj.set({ stroke: getStrokeColor() });
        canvas.requestRenderAll();
    }

    if (
        canvas.isDrawingMode &&
        canvas.freeDrawingBrush instanceof fabric.PencilBrush
    ) {
        canvas.freeDrawingBrush.color = getStrokeColor();
    }
});

// Aktualizace barvy výplně aktivního objektu
document.getElementById('fillColor').addEventListener('input', () => {
    const activeObj = canvas.getActiveObject();
    if (activeObj && activeObj !== currentImage && activeObj !== cropRect && activeObj.fill !== undefined) {
        if (!document.getElementById('fillTransparent').checked) {
            activeObj.set({ fill: getFillColor() });
            canvas.requestRenderAll();
        }
    }
});

// Aktualizace průhledné výplně aktivního objektu
document.getElementById('fillTransparent').addEventListener('change', () => {
    const activeObj = canvas.getActiveObject();
    if (activeObj && activeObj !== currentImage && activeObj !== cropRect && activeObj.fill !== undefined) {
        activeObj.set({ fill: getFillColor() });
        canvas.requestRenderAll();
    }
});

// velikost tužky
document.getElementById('brushWidth').addEventListener('input', (e) => {
    const width = parseInt(e.target.value);

    // Aktualizace šířky čáry aktivního objektu
    const activeObj = canvas.getActiveObject();
    if (activeObj && activeObj !== currentImage && activeObj !== cropRect && activeObj.strokeWidth !== undefined) {
        activeObj.set({ 
            strokeWidth: width,
            strokeDashArray: getDashFromUIForWidth(width)
        });
        canvas.requestRenderAll();
    }

    if (
        canvas.isDrawingMode &&
        canvas.freeDrawingBrush instanceof fabric.PencilBrush
    ) {
        canvas.freeDrawingBrush.width = width;
    }
});
// velikost gumy
document.getElementById('eraserSize').addEventListener('input', (e) => {
    ERASER_RADIUS = parseInt(e.target.value);

    if (eraserCursor) {
        eraserCursor.set({
            radius: ERASER_RADIUS
        });
        canvas.requestRenderAll();
    }
});

// Úprava stylu čáry 
function applyToActiveObject(props) {
    const obj = canvas.getActiveObject();
    if (!obj || !obj.stroke) return;

    if (props.strokeDashArray) {
        const scale = Math.max(obj.scaleX || 1, obj.scaleY || 1);
        props.strokeDashArray = props.strokeDashArray.map(v => v / scale);
    }

    obj.set(props);
    obj.setCoords();
    canvas.requestRenderAll();
}

// Styl čáry
document.getElementById('strokeStyle').addEventListener('change', e => {
    const obj = canvas.getActiveObject();
    if (!obj || !obj.stroke) return;

    const width = obj.strokeWidth || 1;

    obj.set({
        strokeDashArray: getDashFromUIForWidth(width)
    });

    canvas.requestRenderAll();
});

// Zakončení čáry
document.getElementById('strokeCap').addEventListener('change', e => {
    applyToActiveObject({
        strokeLineCap: e.target.value
    });
});

function getDashFromUI() {
    const val = document.getElementById('strokeStyle').value;
    if (val === 'dotted') return [2, 6];
    return null;
}

function getDashFromUIForWidth(width) {
    const type = document.getElementById('strokeStyle').value;

    if (type === 'dashed') return [width * 2, width];
    if (type === 'dotted') return [width, width * 1.5];
    return null;
}
//Přidání textu
document.getElementById('addTextBtn').addEventListener('click', () => {
    const textValue = document.getElementById('textInput').value.trim() || 'Nový text';
    if (!textValue) return;

    const text = new fabric.IText(textValue, {
        left: canvas.width / 2,
        top: canvas.height / 2,
        width: 200, // Default width
        height: 100, // Default height
        fontFamily: 'Arial',
        fill: '#000000',
        fontSize: 32,
        layer: 'text',
        editable: true,
        textAlign: 'left',
        selectable: true,
        evented: true
    });
    canvas.add(text);
    canvas.setActiveObject(text);
    iText.enterEditing();
    iText.hiddenTextarea.focus();
    drawTextBoxMode = false;
    canvas.defaultCursor = 'default';
    // OPRAVA: povol výběr všem objektům
    canvas.getObjects().forEach(obj => {
        obj.selectable = true;
        obj.evented = true;
    });
    canvas.requestRenderAll();
    saveHistoryState('add-text');
});
// Úprava vlastností textu
function applyToActiveText(props) {
    const obj = canvas.getActiveObject();
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;

    obj.set(props);
    canvas.requestRenderAll();
    scheduleTextHistory(); // Uložit do historie
}

// Synchronizace textových ovladačů s aktivním objektem
function updateTextControlsUI(obj) {
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) {
        resetTextControlsUI();
        return;
    };

    document.getElementById('textInput').value = obj.text || '';
    document.getElementById('textSize').value = obj.fontSize;
    document.getElementById('textColor').value = obj.fill;
    
    setFontPickerValue(obj.fontFamily || 'Arial');

    textStyles.bold = obj.fontWeight === 'bold';
    textStyles.italic = obj.fontStyle === 'italic';
    textStyles.underline = obj.underline === true;
    textStyles.linethrough = obj.linethrough === true;
    updateStyleButtons();

    currentTextAlign = obj.textAlign || 'left';
    updateAlignButtons();
}

function resetTextControlsUI() {
    document.getElementById('textInput').value = '';
    document.getElementById('textSize').value = 32;
    document.getElementById('textColor').value = '#000000';
    setFontPickerValue('Arial');

    textStyles.bold = false;
    textStyles.italic = false;
    textStyles.underline = false;
    textStyles.linethrough = false;
    updateStyleButtons();
    
    currentTextAlign = 'left';
    updateAlignButtons();
}

document.getElementById('textInput').addEventListener('input', e => {
    applyToActiveText({ text: e.target.value });
});
document.getElementById('textSize').addEventListener('input', e => {
    applyToActiveText({ fontSize: parseInt(e.target.value) });
});
document.getElementById('textColor').addEventListener('input', e => {
    applyToActiveText({ fill: e.target.value });
});

// Zarovnání textu
let currentTextAlign = 'left';

function updateAlignButtons() {
    document.querySelectorAll('.text-align-btn').forEach(btn => btn.classList.remove('bg-pink-200'));
    const align = currentTextAlign.charAt(0).toUpperCase() + currentTextAlign.slice(1);
    const activeBtn = document.getElementById('align' + align);
    if(activeBtn) activeBtn.classList.add('bg-pink-200');
}

function setTextAlign(align) {
    currentTextAlign = align;
    updateAlignButtons();
    applyToActiveText({ textAlign: align });
}

document.getElementById('alignLeft').addEventListener('click', () => setTextAlign('left'));
document.getElementById('alignCenter').addEventListener('click', () => setTextAlign('center'));
document.getElementById('alignRight').addEventListener('click', () => setTextAlign('right'));

// Styly textu (tučné, kurzíva, podtržení, přeškrtnutí)
let textStyles = {
    bold: false,
    italic: false,
    underline: false,
    linethrough: false
};

function updateStyleButtons() {
    document.getElementById('textBold').classList.toggle('bg-pink-200', textStyles.bold);
    document.getElementById('textItalic').classList.toggle('bg-pink-200', textStyles.italic);
    document.getElementById('textUnderline').classList.toggle('bg-pink-200', textStyles.underline);
    document.getElementById('textLinethrough').classList.toggle('bg-pink-200', textStyles.linethrough);
}

function toggleTextStyle(style) {
    const activeObject = canvas.getActiveObject();
    
    let currentState = textStyles[style];
    if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox')) {
        switch(style) {
            case 'bold': currentState = activeObject.fontWeight === 'bold'; break;
            case 'italic': currentState = activeObject.fontStyle === 'italic'; break;
            case 'underline': currentState = activeObject.underline; break;
            case 'linethrough': currentState = activeObject.linethrough; break;
        }
    }

    const newState = !currentState;
    textStyles[style] = newState;
    
    updateStyleButtons();
    
    const props = {};
    if (style === 'bold') props.fontWeight = newState ? 'bold' : 'normal';
    if (style === 'italic') props.fontStyle = newState ? 'italic' : 'normal';
    if (style === 'underline') props.underline = newState;
    if (style === 'linethrough') props.linethrough = newState;
    
    applyToActiveText(props);
}

document.getElementById('textBold').addEventListener('click', () => toggleTextStyle('bold'));
document.getElementById('textItalic').addEventListener('click', () => toggleTextStyle('italic'));
document.getElementById('textUnderline').addEventListener('click', () => toggleTextStyle('underline'));
document.getElementById('textLinethrough').addEventListener('click', () => toggleTextStyle('linethrough'));


// Font Picker Dropdown
const fontCategories = {
    'Bezpatkové': ['Arial', 'Verdana', 'Tahoma', 'Trebuchet MS', 'Segoe UI', 'Open Sans', 'Roboto', 'Lato', 'Montserrat'],
    'Patkové': ['Times New Roman', 'Georgia', 'Palatino Linotype', 'Book Antiqua', 'Garamond', 'Playfair Display'],
    'Neproporcionální': ['Courier New', 'Consolas', 'Monaco', 'Lucida Console', 'Source Code Pro'],
    'Rukopisné': ['Comic Sans MS', 'Brush Script MT'],
    'Dekorativní': ['Impact', 'Oswald', 'Lobster']
};

const fontPickerBtn = document.getElementById('fontPickerBtn');
const fontPickerDropdown = document.getElementById('fontPickerDropdown');
const fontPickerArrow = document.getElementById('fontPickerArrow');
const fontPickerLabel = document.getElementById('fontPickerLabel');
const fontList = document.getElementById('fontList');
const fontSearch = document.getElementById('fontSearch');
const textFontInput = document.getElementById('textFont');

function renderFontList(filter = '') {
    fontList.innerHTML = '';
    const filterLower = filter.toLowerCase();
    
    for (const [category, fonts] of Object.entries(fontCategories)) {
        const filteredFonts = fonts.filter(f => f.toLowerCase().includes(filterLower));
        if (filteredFonts.length === 0) continue;
        
        const categoryEl = document.createElement('div');
        categoryEl.className = 'text-xs font-semibold text-gray-500 uppercase mt-2 mb-1 px-2';
        categoryEl.textContent = category;
        fontList.appendChild(categoryEl);
        
        filteredFonts.forEach(font => {
            const fontEl = document.createElement('div');
            fontEl.className = 'font-item px-3 py-2 rounded cursor-pointer hover:bg-pink-100 transition text-lg';
            fontEl.style.fontFamily = font;
            fontEl.textContent = font;
            fontEl.dataset.font = font;
            
            if (font === textFontInput.value) {
                fontEl.classList.add('bg-pink-200');
            }
            
            fontEl.addEventListener('click', () => selectFont(font));
            fontList.appendChild(fontEl);
        });
    }
}

function selectFont(font) {
    textFontInput.value = font;
    fontPickerLabel.textContent = font;
    fontPickerLabel.style.fontFamily = font;
    closeFontPicker();
    
    // Aktualizace aktivního textu
    applyToActiveText({ fontFamily: font });
    
    textFontInput.dispatchEvent(new Event('change'));
}

function setFontPickerValue(font) {
    textFontInput.value = font;
    fontPickerLabel.textContent = font;
    fontPickerLabel.style.fontFamily = font;
}

function openFontPicker() {
    renderFontList();
    fontSearch.value = '';
    fontPickerDropdown.classList.remove('hidden');
    fontPickerArrow.classList.add('rotate-180');
    fontSearch.focus();
}

function closeFontPicker() {
    fontPickerDropdown.classList.add('hidden');
    fontPickerArrow.classList.remove('rotate-180');
}

function toggleFontPicker() {
    if (fontPickerDropdown.classList.contains('hidden')) {
        openFontPicker();
    } else {
        closeFontPicker();
    }
}

fontPickerBtn.addEventListener('click', toggleFontPicker);

fontSearch.addEventListener('input', (e) => {
    renderFontList(e.target.value);
});

// Zavření při kliknutí mimo dropdown
document.addEventListener('click', (e) => {
    if (!fontPickerBtn.contains(e.target) && !fontPickerDropdown.contains(e.target)) {
        closeFontPicker();
    }
});

// ========== KONTEXTOVÉ MENU ==========
const contextMenu = document.getElementById('contextMenu');
let contextTarget = null;

// Zobrazení kontextového menu na pravé tlačítko
canvas.upperCanvasEl.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    
    const pointer = canvas.getPointer(e);
    const target = canvas.findTarget(e, false);
    
    if (target && target !== currentImage && !target._element?.src?.includes('blank_')) {
        contextTarget = target;
        canvas.setActiveObject(target);
        canvas.requestRenderAll();
        
        // Pozice menu
        const rect = canvas.upperCanvasEl.getBoundingClientRect();
        contextMenu.style.left = (e.clientX - rect.left + canvas.upperCanvasEl.offsetLeft) + 'px';
        contextMenu.style.top = (e.clientY - rect.top + canvas.upperCanvasEl.offsetTop) + 'px';
        contextMenu.classList.remove('hidden');
    } else {
        hideContextMenu();
    }
});

// Skrytí menu při kliknutí jinam
document.addEventListener('click', function(e) {
    if (!contextMenu.contains(e.target)) {
        hideContextMenu();
    }
});

// Skrytí při scrollu nebo klávese Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideContextMenu();
    }
});

function hideContextMenu() {
    contextMenu.classList.add('hidden');
    contextTarget = null;
}

// Clipboard pro kopírování/vkládání
let clipboard = null;

function copyObject() {
    const obj = canvas.getActiveObject();
    if (!obj || obj === currentImage) return;
    
    obj.clone(function(cloned) {
        clipboard = cloned;
    });
}

function pasteObject() {
    if (!clipboard) return;
    
    clipboard.clone(function(clonedObj) {
        canvas.discardActiveObject();
        
        clonedObj.set({
            left: clonedObj.left + 20,
            top: clonedObj.top + 20,
            evented: true,
            selectable: true
        });
        
        if (clonedObj.type === 'activeSelection') {
            clonedObj.canvas = canvas;
            clonedObj.forEachObject(function(obj) {
                canvas.add(obj);
            });
            clonedObj.setCoords();
        } else {
            canvas.add(clonedObj);
        }
        
        clipboard.top += 20;
        clipboard.left += 20;
        canvas.setActiveObject(clonedObj);
        canvas.requestRenderAll();
    });
}

// Klávesové zkratky Ctrl+C a Ctrl+V
document.addEventListener('keydown', function(e) {
    // Ctrl+C - kopírovat
    if (e.ctrlKey && e.key === 'c') {
        const obj = canvas.getActiveObject();
        if (obj && obj !== currentImage) {
            e.preventDefault();
            copyObject();
        }
    }
    
    // Ctrl+V - vložit
    if (e.ctrlKey && e.key === 'v') {
        if (clipboard) {
            e.preventDefault();
            pasteObject();
        }
    }
});

// Akce kontextového menu
document.querySelectorAll('.context-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!contextTarget) return;
        
        const action = this.dataset.action;
        
        switch(action) {
            case 'bringForward':
                canvas.bringForward(contextTarget);
                break;
            case 'sendBackward':
                canvas.sendBackwards(contextTarget);
                break;
            case 'bringToFront':
                canvas.bringToFront(contextTarget);
                break;
            case 'sendToBack':
                // Pošli objekt úplně na spodek (i pod obrázek)
                canvas.sendToBack(contextTarget);
                break;
            case 'delete':
                if (contextTarget !== currentImage) {
                    canvas.remove(contextTarget);
                }
                break;
            case 'copy':
                if (contextTarget !== currentImage) {
                    contextTarget.clone(function(cloned) {
                        clipboard = cloned;
                    });
                }
                break;
            case 'paste':
                pasteObject();
                break;
        }
        
        canvas.requestRenderAll();
        hideContextMenu();
    });
});

document.getElementById('undoBtn')?.addEventListener('click', undo);
document.getElementById('redoBtn')?.addEventListener('click', redo);

document.addEventListener('keydown', (e) => {
  const isMac = navigator.platform.toUpperCase().includes('MAC');
  const ctrl = isMac ? e.metaKey : e.ctrlKey;

  if (!ctrl) return;

  // když uživatel edituje text (IText) – neber mu Ctrl+Z
  const active = canvas.getActiveObject();
  if (active && active.type === 'i-text' && active.isEditing) return;

  if (e.key.toLowerCase() === 'z') {
    e.preventDefault();
    if (e.shiftKey) redo();
    else undo();
  }

  if (e.key.toLowerCase() === 'y') {
    e.preventDefault();
    redo();
  }
});

// Automatické ukládání historie
canvas.on('object:added', (e) => {
  if (HISTORY.isRestoring) return;
  if (!isHistoryObject(e.target)) return;
  saveHistoryState('added');
});

canvas.on('object:removed', (e) => {
  if (HISTORY.isRestoring) return;
  if (!isHistoryObject(e.target)) return;
  saveHistoryState('removed');
});

canvas.on('object:modified', (e) => {
  if (HISTORY.isRestoring) return;
  if (!isHistoryObject(e.target)) return;
  saveHistoryState('modified');
});

// Přidání dalšího obrázku
document.getElementById('addImageBtn').addEventListener('click', () => {
    document.getElementById('addImageInput').click();
});
document.getElementById('addImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(evt) {
        fabric.Image.fromURL(evt.target.result, function(img) {
            img.set({
                left: canvas.width / 2,
                top: canvas.height / 2,
                originX: 'center',
                originY: 'center',
                scaleX: 1,
                scaleY: 1,
                selectable: true,
                evented: true,
                layer: 'image',
            });
            canvas.add(img);
            canvas.setActiveObject(img);
            canvas.requestRenderAll();
            saveHistoryState('add-image');
        }, { crossOrigin: 'anonymous' });
    };
    reader.readAsDataURL(file);
    // Reset input pro možnost vložit stejný obrázek znovu
    e.target.value = '';
});

// Listeners for the new text tool buttons
function setTextEditingMode(mode) {
    if (mode === 'select') {
        drawMode = null;
        canvas.isDrawingMode = false;
        canvas.selection = true;
        if(currentImage) lockImage(false);
        setActiveTool(document.getElementById('textSelectBtn'));
        canvas.defaultCursor = 'default';
        // OPRAVA: povol výběr všem objektům
        canvas.getObjects().forEach(obj => {
            obj.selectable = true;
            obj.evented = true;
        });
        canvas.requestRenderAll();
    } else if (mode === 'draw') {
        drawMode = 'textBox';
        canvas.isDrawingMode = false;
        canvas.selection = false;
        if(currentImage) lockImage(true);
        setActiveTool(document.getElementById('drawTextBoxBtn'));
        canvas.defaultCursor = 'crosshair';
        canvas.getObjects().forEach(o => {
            o.selectable = false;
            o.evented = false;
        });
        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }
}

document.getElementById('textSelectBtn').addEventListener('click', () => setTextEditingMode('select'));
document.getElementById('drawTextBoxBtn').addEventListener('click', () => setTextEditingMode('draw'));
</script>

</x-layout>
