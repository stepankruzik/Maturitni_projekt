<x-layout :hideNav="true">
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
        <button id="addImageBtn" class="px-3 py-1 bg-cyan-500 text-white rounded hover:bg-cyan-600 transition flex items-center gap-1" title="Přidat obrázek">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            Obrázek
        </button>
        <input type="file" id="addImageInput" accept="image/*" class="hidden">
    </div>

    <div class="flex h-screen">
    <!-- Sidebar vlevo -->
    <div class="w-72 bg-gray-100 border-r border-gray-300 p-4 overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Úpravy obrázku</h2>



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
        <div class="mb-3">
            <p class="text-sm font-semibold text-gray-600 mb-2">Aplikovat filtry na vrstvy:</p>
            <div class="flex gap-3 flex-wrap">
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" id="filterLayerImage" checked class="filterLayerCheck">
                    Obrázek
                </label>
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" id="filterLayerDraw" class="filterLayerCheck">
                    Kresby
                </label>
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" id="filterLayerText" class="filterLayerCheck">
                    Text
                </label>
            </div>
        </div>
        <div id="filterPreview" class="grid grid-cols-2 gap-2 mt-2">
            <div class="filter-thumb-wrap" data-filter="original">
                <canvas class="filter-preview-canvas" width="100" height="75"></canvas>
                <span class="text-xs block text-center">Originál</span>
            </div>
            <div class="filter-thumb-wrap" data-filter="grayscale">
                <canvas class="filter-preview-canvas" width="100" height="75"></canvas>
                <span class="text-xs block text-center">Šedá</span>
            </div>
            <div class="filter-thumb-wrap" data-filter="sepia">
                <canvas class="filter-preview-canvas" width="100" height="75"></canvas>
                <span class="text-xs block text-center">Sépie</span>
            </div>
            <div class="filter-thumb-wrap" data-filter="invert">
                <canvas class="filter-preview-canvas" width="100" height="75"></canvas>
                <span class="text-xs block text-center">Inverze</span>
            </div>
            <div class="filter-thumb-wrap" data-filter="blur">
                <canvas class="filter-preview-canvas" width="100" height="75"></canvas>
                <span class="text-xs block text-center">Rozostření</span>
            </div>
            <div class="filter-thumb-wrap" data-filter="sharpen">
                <canvas class="filter-preview-canvas" width="100" height="75"></canvas>
                <span class="text-xs block text-center">Zaostření</span>
            </div>
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
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <polyline points="4 7 4 4 20 4 20 7"/>
                <line x1="9" x2="15" y1="20" y2="20"/>
                <line x1="12" x2="12" y1="4" y2="20"/>
            </svg>
        </button>
        <button id="copyFormatBtn" class="tool-btn" title="Kopírovat formát">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 mx-auto">
                <rect width="16" height="6" x="2" y="12" rx="2"/>
                <path d="M18 12v-2a4 4 0 0 0-4-4H8"/>
                <path d="M10 12v6"/>
                <path d="M6 12v6"/>
                <path d="M14 12v6"/>
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
        <!-- HUD pro měření čáry -->
        <div id="lineHUD" class="hidden fixed bg-gray-900 text-white text-sm px-3 py-1 rounded-lg shadow-lg z-50 pointer-events-none font-mono whitespace-nowrap">
            <span id="lineHUDText">0 px | 0°</span>
        </div>
        <!-- Plovoucí toolbar pro kreslení -->
        <div id="drawFloatingToolbar" class="hidden fixed bg-white/95 backdrop-blur-sm border border-gray-200 rounded-xl shadow-xl px-3 py-2.5 z-50 flex gap-3 items-center">
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-gray-600">Obrys</label>
                <input id="drawFloatingStrokeColor" type="color" class="h-7 w-9 p-0 rounded border border-gray-300 cursor-pointer hover:border-gray-400 transition">
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-gray-600">Výplň</label>
                <input id="drawFloatingFillColor" type="color" class="h-7 w-9 p-0 rounded border border-gray-300 cursor-pointer hover:border-gray-400 transition">
                <label class="flex items-center gap-1 text-xs text-gray-500">
                    <input id="drawFloatingTransparent" type="checkbox" class="rounded">
                    Žádná
                </label>
            </div>
            <div class="w-px h-6 bg-gray-200"></div>
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-gray-600">Šířka</label>
                <input id="drawFloatingStrokeWidth" type="number" min="1" max="100" value="3" class="w-14 text-xs px-2 py-1 rounded border border-gray-300 focus:border-blue-400 focus:ring-1 focus:ring-blue-200 transition">
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-gray-600">Styl</label>
                <select id="drawFloatingStrokeStyle" class="text-xs px-2 py-1 rounded border border-gray-300 focus:border-blue-400 hover:border-gray-400 transition cursor-pointer">
                    <option value="solid">Plná</option>
                    <option value="dashed">Čárkovaná</option>
                    <option value="dotted">Tečkovaná</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-gray-600">Konec</label>
                <select id="drawFloatingStrokeCap" class="text-xs px-2 py-1 rounded border border-gray-300 focus:border-blue-400 hover:border-gray-400 transition cursor-pointer">
                    <option value="butt">Rovný</option>
                    <option value="round">Kulatý</option>
                    <option value="square">Čtvercový</option>
                </select>
            </div>
        </div>
        <div id="textToolbar" class="hidden fixed bg-white/95 backdrop-blur-sm shadow-xl rounded-xl px-3 py-2 flex items-center gap-2 z-50 border border-gray-200">
            <!-- Styl textu B I U S -->
            <div class="flex gap-1 border-r border-gray-200 pr-2">
                <button id="tbBold" data-style="bold" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 font-bold text-sm transition" title="Tučné">B</button>
                <button id="tbItalic" data-style="italic" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 italic text-sm transition" title="Kurzíva">I</button>
                <button id="tbUnderline" data-style="underline" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 underline text-sm transition" title="Podtržené">U</button>
                <button id="tbLinethrough" data-style="linethrough" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 line-through text-sm transition" title="Přeškrtnuté">S</button>
            </div>
            <!-- Barva -->
            <div class="flex items-center gap-1 border-r border-gray-200 pr-2">
                <input type="color" id="textToolbarColor" class="w-7 h-7 rounded cursor-pointer border-0" title="Barva textu">
            </div>
            <!-- Font -->
            <div class="flex items-center gap-1 border-r border-gray-200 pr-2">
                <select id="tbFont" class="text-xs px-2 py-1 rounded border border-gray-300 focus:border-pink-400 cursor-pointer max-w-[120px]" title="Font">
                    <optgroup label="Bezpatkové">
                        <option value="Arial">Arial</option>
                        <option value="Verdana">Verdana</option>
                        <option value="Tahoma">Tahoma</option>
                        <option value="Trebuchet MS">Trebuchet MS</option>
                        <option value="Segoe UI">Segoe UI</option>
                        <option value="Open Sans">Open Sans</option>
                        <option value="Roboto">Roboto</option>
                        <option value="Lato">Lato</option>
                        <option value="Montserrat">Montserrat</option>
                    </optgroup>
                    <optgroup label="Patkové">
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Palatino Linotype">Palatino Linotype</option>
                        <option value="Book Antiqua">Book Antiqua</option>
                        <option value="Garamond">Garamond</option>
                        <option value="Playfair Display">Playfair Display</option>
                    </optgroup>
                    <optgroup label="Neproporcionální">
                        <option value="Courier New">Courier New</option>
                        <option value="Consolas">Consolas</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Lucida Console">Lucida Console</option>
                        <option value="Source Code Pro">Source Code Pro</option>
                    </optgroup>
                    <optgroup label="Rukopisné">
                        <option value="Comic Sans MS">Comic Sans MS</option>
                        <option value="Brush Script MT">Brush Script MT</option>
                    </optgroup>
                    <optgroup label="Dekorativní">
                        <option value="Impact">Impact</option>
                        <option value="Oswald">Oswald</option>
                        <option value="Lobster">Lobster</option>
                    </optgroup>
                </select>
            </div>
            <!-- Velikost -->
            <div class="flex items-center gap-1 border-r border-gray-200 pr-2">
                <button id="tbSizeMinus" class="w-6 h-6 rounded hover:bg-gray-100 active:bg-gray-200 text-sm transition" title="Zmenšit">−</button>
                <span id="tbSizeVal" class="text-xs w-6 text-center">32</span>
                <button id="tbSizePlus" class="w-6 h-6 rounded hover:bg-gray-100 active:bg-gray-200 text-sm transition" title="Zvětšit">+</button>
            </div>
            <!-- Zarovnání -->
            <div class="flex gap-1 border-r border-gray-200 pr-2">
                <button id="tbAlignLeft" data-align="left" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center" title="Zarovnat vlevo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="15" y2="12"/>
                        <line x1="3" y1="18" x2="18" y2="18"/>
                    </svg>
                </button>
                <button id="tbAlignCenter" data-align="center" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center" title="Na střed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="6" y1="12" x2="18" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
                <button id="tbAlignRight" data-align="right" class="w-7 h-7 rounded hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center" title="Zarovnat vpravo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="9" y1="12" x2="21" y2="12"/>
                        <line x1="6" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>
            <!-- Smazat -->
            <button id="deleteTextBtn" class="w-7 h-7 rounded hover:bg-red-100 active:bg-red-200 text-red-600 transition flex items-center justify-center" title="Smazat text">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/>
                </svg>
            </button>
        </div>
        <!-- Kontekstové menu pro text -->
        <div id="textContextMenu" class="hidden absolute bg-white border border-gray-300 rounded-lg shadow-lg py-1 z-50 min-w-[150px]">
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="bringForward">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 19V5M5 12l7-7 7 7"/>
                </svg>
                Dopředu
            </button>
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="sendBackward">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12l7 7 7-7"/>
                </svg>
                Dozadu
            </button>
            <div class="border-t border-gray-200 my-1"></div>
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="bringToFront">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 19V5M5 12l7-7 7 7"/>
                    <path d="M5 5h14"/>
                </svg>
                Na vrch
            </button>
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="sendToBack">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12l7 7 7-7"/>
                    <path d="M5 19h14"/>
                </svg>
                Na spodek
            </button>
            <div class="border-t border-gray-200 my-1"></div>
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="copy">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                </svg>
                Kopírovat
            </button>
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" data-action="paste">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                </svg>
                Vložit
            </button>
            <div class="border-t border-gray-200 my-1"></div>
            <button class="text-context-btn w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600 flex items-center gap-2" data-action="delete">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/>
                </svg>
                Smazat
            </button>
        </div>
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
        
        <!-- Toast notifikace -->
        <div id="toastContainer" class="fixed bottom-4 right-4 z-[9999] space-y-2"></div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
const canvas = new fabric.Canvas('canvas', {
    preserveObjectStacking: true  // Zachová pořadí vrstev při výběru objektu
});

// Toast notifikačního systému
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    // Stylování dle typu
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
    
    // Automatické skrytí
    if (duration > 0) {
        setTimeout(() => {
            toast.style.animation = 'fade-out 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// CSS animace (přidáno do style tagu)
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

// Globální proměnná pro cílový objekt kreslení
let drawContextTarget = null;

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
let line, circle, rect, triangle, rightTriangle, ellipse, star, heart, arrow, speechBubble, roundedSpeechBubble, roundedRect, curvedArrow, hexagon, cross;
let textBox = null;
let origX, origY;
let lastCreatedObject = null; // Spolehlivá reference na právě vytvořený objekt

let formatClipboard = null;
let isFormatPainterActive = false;

// Vizuální kurzor gumy
let snapIndicator = null;
let lengthIndicator = null;
let historyBatch = false;
let filterHistoryTimer = null;

const SNAP_RADIUS = 12;
let snapLineIsActive = false;
let snapCurrentLine = null;
let lineIsDown = false;

// Pomocná funkce: vypočítá koncové body čáry v canvas souřadnicích
function getLineEndpointCanvasPos(lineObj, which) {
    const p = lineObj.calcLinePoints();
    const matrix = lineObj.calcTransformMatrix();
    
    let localX, localY;
    if (which === 'p1') {
        localX = p.x1;
        localY = p.y1;
    } else {
        localX = p.x2;
        localY = p.y2;
    }
    
    const localPoint = new fabric.Point(localX, localY);
    return fabric.util.transformPoint(localPoint, matrix);
}

// Pomocná funkce: nastaví koncový bod čáry z canvas souřadnic
function setLineEndpointFromCanvasPos(lineObj, which, canvasPos) {
    const otherWhich = which === 'p1' ? 'p2' : 'p1';
    const otherPos = getLineEndpointCanvasPos(lineObj, otherWhich);
    
    let p1, p2;
    if (which === 'p1') {
        p1 = canvasPos;
        p2 = otherPos;
    } else {
        p1 = otherPos;
        p2 = canvasPos;
    }
    
    setLineByCanvasPoints(lineObj, p1, p2);
}

// Pomocná funkce: nastaví čáru podle dvou canvas bodů
function setLineByCanvasPoints(lineObj, p1, p2) {
    const centerX = (p1.x + p2.x) / 2;
    const centerY = (p1.y + p2.y) / 2;
    
    const halfWidth = (p2.x - p1.x) / 2;
    const halfHeight = (p2.y - p1.y) / 2;
    
    lineObj.set({
        x1: -halfWidth,
        y1: -halfHeight,
        x2: halfWidth,
        y2: halfHeight,
        left: centerX,
        top: centerY,
        originX: 'center',
        originY: 'center',
        angle: 0,
        scaleX: 1,
        scaleY: 1
    });
    
    lineObj.setCoords();
}

// Najde nejbližší snap bod (koncový bod existující čáry)
function findSnapPoint(pointer) {
    const objects = canvas.getObjects().filter(o => o.layer === 'draw' && o !== snapCurrentLine);
    let closest = null;
    let minDist = SNAP_RADIUS;

    for (const obj of objects) {
        let snapPoints = [];

        // Čáry - 2 koncové body
        if (obj.type === 'line') {
            for (const which of ['p1', 'p2']) {
                const pos = getLineEndpointCanvasPos(obj, which);
                snapPoints.push(pos);
            }
        }
        // Kruhy - 4 body (nahoře, vpravo, dole, vlevo)
        else if (obj.type === 'circle') {
            const center = obj.getCenterPoint();
            const radius = obj.radius * obj.scaleX;
            const angle = obj.angle * Math.PI / 180;

            for (let i = 0; i < 4; i++) {
                const a = angle + (i * Math.PI / 2);
                snapPoints.push({
                    x: center.x + radius * Math.cos(a),
                    y: center.y + radius * Math.sin(a)
                });
            }
        }
        // Obdélníky - 4 rohy
        else if (obj.type === 'rect') {
            const matrix = obj.calcTransformMatrix();
            const w = obj.width / 2;
            const h = obj.height / 2;

            for (const [lx, ly] of [[-w, -h], [w, -h], [w, h], [-w, h]]) {
                const point = fabric.util.transformPoint({ x: lx, y: ly }, matrix);
                snapPoints.push(point);
            }
        }
        // Elipsy - 4 body
        else if (obj.type === 'ellipse') {
            const center = obj.getCenterPoint();
            const rx = obj.rx * obj.scaleX;
            const ry = obj.ry * obj.scaleY;
            const angle = obj.angle * Math.PI / 180;

            for (let i = 0; i < 4; i++) {
                const a = angle + (i * Math.PI / 2);
                snapPoints.push({
                    x: center.x + rx * Math.cos(a),
                    y: center.y + ry * Math.sin(a)
                });
            }
        }
        // Polygony - všechny vrcholy
        else if (obj.type === 'polygon') {
            const matrix = obj.calcTransformMatrix();
            for (const point of obj.points) {
                const transformed = fabric.util.transformPoint(point, matrix);
                snapPoints.push(transformed);
            }
        }
        // Path objekty - 4 body z bounding boxu
        else if (obj.type === 'path') {
            const matrix = obj.calcTransformMatrix();
            const bounds = obj.getBoundingRect(false, true);
            const w = bounds.width / 2;
            const h = bounds.height / 2;

            for (const [lx, ly] of [[0, -h], [w, 0], [0, h], [-w, 0]]) {
                const point = fabric.util.transformPoint({ x: lx, y: ly }, matrix);
                snapPoints.push(point);
            }
        }
        // Group objekty - 4 body z bounding boxu
        else if (obj.type === 'group') {
            const matrix = obj.calcTransformMatrix();
            const w = obj.width / 2;
            const h = obj.height / 2;

            for (const [lx, ly] of [[0, -h], [w, 0], [0, h], [-w, 0]]) {
                const point = fabric.util.transformPoint({ x: lx, y: ly }, matrix);
                snapPoints.push(point);
            }
        }

        // Najdi nejbližší bod
        for (const pos of snapPoints) {
            const dist = Math.hypot(pointer.x - pos.x, pointer.y - pos.y);
            if (dist < minDist) {
                minDist = dist;
                closest = { x: pos.x, y: pos.y, obj: obj };
            }
        }
    }

    return closest;
}

// Zobrazí snap indikátor (zelený kroužek)
function showSnapIndicator(pos) {
    if (!snapIndicator) {
        snapIndicator = new fabric.Circle({
            radius: 6,
            fill: 'rgba(34, 197, 94, 0.4)',
            stroke: '#22c55e',
            strokeWidth: 2,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            originX: 'center',
            originY: 'center'
        });
        canvas.add(snapIndicator);
    }
    
    snapIndicator.set({ left: pos.x, top: pos.y, visible: true });
    snapIndicator.bringToFront();
}

// Skryje snap indikátor
function hideSnapIndicator() {
    if (snapIndicator) {
        snapIndicator.visible = false;
    }
}

// Zobrazí HUD s délkou a úhlem
function showLineHUD(p1, p2, mouseEvent) {
    const hudEl = document.getElementById('lineHUD');
    const textEl = document.getElementById('lineHUDText');
    
    const dx = p2.x - p1.x;
    const dy = p2.y - p1.y;
    const length = Math.round(Math.hypot(dx, dy));
    const angleRad = Math.atan2(dy, dx);
    let angleDeg = Math.round(angleRad * 180 / Math.PI);
    if (angleDeg < 0) angleDeg += 360;
    
    textEl.textContent = `${length} px | ${angleDeg}°`;
    
    hudEl.style.left = (mouseEvent.clientX + 15) + 'px';
    hudEl.style.top = (mouseEvent.clientY + 15) + 'px';
    hudEl.classList.remove('hidden');
}

// Skryje HUD
function hideLineHUD() {
    document.getElementById('lineHUD').classList.add('hidden');
}

// Vytvoří vlastní controls pro koncové body čáry
function enableLineEndpointsControls(lineObj) {
    lineObj.controls = {};
    
    // P1 control
    lineObj.controls.p1 = new fabric.Control({
        positionHandler: function(dim, finalMatrix, fabricObject) {
            const p = fabricObject.calcLinePoints();
            const point = new fabric.Point(p.x1, p.y1);
            return fabric.util.transformPoint(point, finalMatrix);
        },
        actionHandler: function(eventData, transform, x, y) {
            const target = transform.target;
            const newPos = { x, y };
            setLineEndpointFromCanvasPos(target, 'p1', newPos);
            return true;
        },
        actionName: 'moveP1',
        cursorStyle: 'crosshair',
        render: function(ctx, left, top, styleOverride, fabricObject) {
            ctx.save();
            ctx.translate(left, top);
            ctx.beginPath();
            ctx.arc(0, 0, 6, 0, Math.PI * 2);
            ctx.fillStyle = '#3b82f6';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.restore();
        }
    });
    
    // P2 control
    lineObj.controls.p2 = new fabric.Control({
        positionHandler: function(dim, finalMatrix, fabricObject) {
            const p = fabricObject.calcLinePoints();
            const point = new fabric.Point(p.x2, p.y2);
            return fabric.util.transformPoint(point, finalMatrix);
        },
        actionHandler: function(eventData, transform, x, y) {
            const target = transform.target;
            const newPos = { x, y };
            setLineEndpointFromCanvasPos(target, 'p2', newPos);
            return true;
        },
        actionName: 'moveP2',
        cursorStyle: 'crosshair',
        render: function(ctx, left, top, styleOverride, fabricObject) {
            ctx.save();
            ctx.translate(left, top);
            ctx.beginPath();
            ctx.arc(0, 0, 6, 0, Math.PI * 2);
            ctx.fillStyle = '#3b82f6';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.restore();
        }
    });
    
    // Zakázat standardní transformace
    lineObj.lockScalingX = true;
    lineObj.lockScalingY = true;
    lineObj.lockRotation = true;
    lineObj.hasRotatingPoint = false;
    lineObj.hasBorders = false;
}

// Sledování aktuálně editované čáry (pro endpoint controls)
let currentEditingLine = null;

// Vypne endpoint controls a obnoví standardní controls
function disableLineEndpointsControls(lineObj) {
    if (!lineObj || lineObj.type !== 'line') return;
    
    // Obnovit standardní controls
    lineObj.controls = fabric.Line.prototype.controls;
    lineObj.lockScalingX = false;
    lineObj.lockScalingY = false;
    lineObj.lockRotation = false;
    lineObj.hasRotatingPoint = true;
    lineObj.hasBorders = true;
    lineObj.setCoords();
}

// Vypne endpoint controls na všech čárách
function disableAllLineEndpoints() {
    canvas.getObjects('line').forEach(line => {
        disableLineEndpointsControls(line);
    });
    currentEditingLine = null;
}

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

let snapLineStartPoint = null;

function snapLineMouseDown(e) {
    if (!snapLineIsActive) return;
    
    const pointer = canvas.getPointer(e.e);
    const snap = findSnapPoint(pointer);
    
    snapLineStartPoint = snap ? { x: snap.x, y: snap.y } : { x: pointer.x, y: pointer.y };
    
    const width = parseInt(document.getElementById('brushWidth').value);
    
    snapCurrentLine = new fabric.Line([0, 0, 0, 0], {
        stroke: getStrokeColor(),
        strokeWidth: width,
        strokeDashArray: getDashFromUIForWidth(width),
        strokeLineCap: document.getElementById('strokeCap').value,
        strokeUniform: true,
        originX: 'center',
        originY: 'center',
        selectable: false,
        evented: false,
        layer: 'draw'
    });
    
    setLineByCanvasPoints(snapCurrentLine, snapLineStartPoint, snapLineStartPoint);
    canvas.add(snapCurrentLine);
    
    lineIsDown = true;
    historyBatch = true;
}

function snapLineMouseMove(e) {
    if (!snapLineIsActive) return;
    
    const pointer = canvas.getPointer(e.e);
    const snap = findSnapPoint(pointer);
    
    if (snap) {
        showSnapIndicator(snap);
    } else {
        hideSnapIndicator();
    }
    
    if (!lineIsDown || !snapCurrentLine) return;
    
    const endPoint = snap ? { x: snap.x, y: snap.y } : { x: pointer.x, y: pointer.y };
    
    setLineByCanvasPoints(snapCurrentLine, snapLineStartPoint, endPoint);
    
    showLineHUD(snapLineStartPoint, endPoint, e.e);
    
    canvas.requestRenderAll();
}

function snapLineMouseUp(e) {
    if (!snapLineIsActive || !lineIsDown) return;
    
    const pointer = canvas.getPointer(e.e);
    const snap = findSnapPoint(pointer);
    const endPoint = snap ? { x: snap.x, y: snap.y } : { x: pointer.x, y: pointer.y };
    
    // Zkontroluj zda je čára dostatečně dlouhá
    const dist = Math.hypot(endPoint.x - snapLineStartPoint.x, endPoint.y - snapLineStartPoint.y);
    
    if (dist < 5) {
        canvas.remove(snapCurrentLine);
    } else {
        setLineByCanvasPoints(snapCurrentLine, snapLineStartPoint, endPoint);
        
        // Během aktivního nástroje nechej čáru nevybíratelnou
        // (aby šlo kreslit další čáry a napojovat se)
        snapCurrentLine.set({
            selectable: false,
            evented: false
        });
        // Endpoint controls se aktivují až na double-click
    }
    
    hideSnapIndicator();
    hideLineHUD();
    
    lineIsDown = false;
    historyBatch = false;
    snapCurrentLine = null;
    snapLineStartPoint = null;
    
    saveHistoryState('draw-line');
    canvas.requestRenderAll();
}

function activateSnapLineTool() {
    // Deaktivuj jiné režimy
    deactivateSnapLineTool();
    
    snapLineIsActive = true;
    drawMode = null;
    canvas.isDrawingMode = false;
    canvas.selection = false;
    
    lockImage(true);
    
    canvas.defaultCursor = 'crosshair';
    canvas.hoverCursor = 'crosshair';
    
    // Zakázat výběr objektů během kreslení
    canvas.getObjects().forEach(obj => {
        if (obj !== snapIndicator) {
            obj.selectable = false;
            obj.evented = false;
        }
    });
    
    canvas.discardActiveObject();
    canvas.requestRenderAll();
    updateDrawFloatingToolbarVisibility();
}

function deactivateSnapLineTool() {
    if (!snapLineIsActive) return;
    
    snapLineIsActive = false;
    
    hideSnapIndicator();
    hideLineHUD();
    
    if (snapCurrentLine && lineIsDown) {
        canvas.remove(snapCurrentLine);
    }
    
    // Obnov vybíratelnost všech čar
    canvas.getObjects().forEach(obj => {
        if (obj.type === 'line' && obj.layer === 'draw') {
            obj.selectable = true;
            obj.evented = true;
        }
    });
    
    snapCurrentLine = null;
    snapLineStartPoint = null;
    lineIsDown = false;
    historyBatch = false;
    updateDrawFloatingToolbarVisibility();
}

function lockImage(locked) {
    if (currentImage) {
        currentImage.selectable = !locked;
        currentImage.evented = !locked;
    }
}

function setActiveTool(button) {
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white');
    });
    if (button) {
        button.classList.add('bg-blue-500', 'text-white');
    }
}

function setDrawMode(newMode, button) {
    drawMode = newMode;
    canvas.isDrawingMode = (newMode === 'brush');
    
    // Při přepnutí nástroje vypnout endpoint controls na čárách
    disableAllLineEndpoints();
    
    if (newMode !== 'eraser') {
        hideEraserCursor();
    }
    
    if (newMode === 'brush') {
        canvas.freeDrawingBrush.width = parseInt(document.getElementById('brushWidth').value, 10);
        canvas.freeDrawingBrush.color = document.getElementById('drawColor').value;
    } else if (newMode === 'eraser') {
        ERASER_RADIUS = parseInt(document.getElementById('eraserSize').value, 10);
        if(eraserCursor) eraserCursor.set('radius', ERASER_RADIUS);
    }

    if (!newMode || newMode === 'select' || newMode === 'textSelect') {
        const drawToolbar = document.getElementById('drawFloatingToolbar');
        if (drawToolbar) drawToolbar.classList.add('hidden');
    }

    if (newMode && newMode !== 'select' && newMode !== 'textSelect') {
        canvas.selection = false;
        lockImage(true);
        canvas.discardActiveObject();
        canvas.defaultCursor = 'crosshair';
        canvas.hoverCursor = 'crosshair';
        // Při kreslení VŠECHNY objekty neselectable
        canvas.getObjects().forEach(obj => {
            obj.selectable = false;
            obj.evented = false;
        });
    } else {
        canvas.selection = true;
        lockImage(false);
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        canvas.getObjects().forEach(obj => {
            const isBlank = obj._element?.src?.includes('blank_');
            let canSelect = false;
            if (newMode === 'select') {
                canSelect = !isBlank && (obj.layer === 'draw' || obj.layer === 'image');
            } else if (newMode === 'textSelect') {
                canSelect = obj.layer === 'text';
            }
            obj.selectable = canSelect;
            obj.evented = canSelect;
        });
    }

    setActiveTool(button);
    updateDrawFloatingToolbarVisibility();
}

// Aktualizace viditelnosti a hodnot plovoucího toolbaru
function updateDrawFloatingToolbarVisibility() {
    const el = document.getElementById('drawFloatingToolbar');
    if (!el) return;
    
    const activeObj = canvas.getActiveObject();
    const hasDrawSelection = activeObj && (activeObj.layer === 'draw' || 
        (activeObj.type === 'activeSelection' && activeObj.getObjects().some(o => o.layer === 'draw')));
    const shouldShow = (drawMode !== null && drawMode !== 'select' && drawMode !== 'textSelect') || 
                       canvas.isDrawingMode === true || 
                       hasDrawSelection;
    
    if (shouldShow) {
        const rect = canvas.upperCanvasEl.getBoundingClientRect();
        el.style.left = (rect.left + 12) + 'px';
        el.style.top = (rect.top + 12) + 'px';
        el.classList.remove('hidden');

        if (activeObj && activeObj.layer === 'draw') {
            drawContextTarget = activeObj;  // Nastav cílový objekt
            if (activeObj.stroke) document.getElementById('drawFloatingStrokeColor').value = activeObj.stroke;
            if (activeObj.fill && activeObj.fill !== 'transparent') {
                document.getElementById('drawFloatingFillColor').value = activeObj.fill;
                document.getElementById('drawFloatingTransparent').checked = false;
            } else {
                document.getElementById('drawFloatingTransparent').checked = true;
            }
            if (activeObj.strokeWidth) document.getElementById('drawFloatingStrokeWidth').value = activeObj.strokeWidth;
            if (activeObj.strokeLineCap) document.getElementById('drawFloatingStrokeCap').value = activeObj.strokeLineCap;
            // Sync dash style
            const dash = activeObj.strokeDashArray;
            if (!dash || dash.length === 0) {
                document.getElementById('drawFloatingStrokeStyle').value = 'solid';
            } else if (dash[0] > dash[1]) {
                document.getElementById('drawFloatingStrokeStyle').value = 'dashed';
            } else {
                document.getElementById('drawFloatingStrokeStyle').value = 'dotted';
            }
        } else if (activeObj && activeObj.type === 'activeSelection') {
            // Pro multi-selection nastav target jako activeSelection
            drawContextTarget = activeObj;
            // Použij hodnoty z prvního draw objektu ve výběru
            const firstDraw = activeObj.getObjects().find(o => o.layer === 'draw');
            if (firstDraw) {
                if (firstDraw.stroke) document.getElementById('drawFloatingStrokeColor').value = firstDraw.stroke;
                if (firstDraw.fill && firstDraw.fill !== 'transparent') {
                    document.getElementById('drawFloatingFillColor').value = firstDraw.fill;
                    document.getElementById('drawFloatingTransparent').checked = false;
                } else {
                    document.getElementById('drawFloatingTransparent').checked = true;
                }
                if (firstDraw.strokeWidth) document.getElementById('drawFloatingStrokeWidth').value = firstDraw.strokeWidth;
                if (firstDraw.strokeLineCap) document.getElementById('drawFloatingStrokeCap').value = firstDraw.strokeLineCap;
                const dash = firstDraw.strokeDashArray;
                if (!dash || dash.length === 0) {
                    document.getElementById('drawFloatingStrokeStyle').value = 'solid';
                } else if (dash[0] > dash[1]) {
                    document.getElementById('drawFloatingStrokeStyle').value = 'dashed';
                } else {
                    document.getElementById('drawFloatingStrokeStyle').value = 'dotted';
                }
            }
        } else {
            const strokeColor = document.getElementById('drawColor')?.value || '#000000';
            const fillColor = document.getElementById('fillColor')?.value || 'transparent';
            document.getElementById('drawFloatingStrokeColor').value = strokeColor;
            document.getElementById('drawFloatingFillColor').value = fillColor === 'transparent' ? '#ffffff' : fillColor;
            document.getElementById('drawFloatingStrokeWidth').value = document.getElementById('brushWidth')?.value || 3;
            document.getElementById('drawFloatingStrokeStyle').value = document.getElementById('strokeStyle')?.value || 'solid';
            document.getElementById('drawFloatingStrokeCap').value = document.getElementById('strokeCap')?.value || 'butt';
            document.getElementById('drawFloatingTransparent').checked = (fillColor === 'transparent');
            drawContextTarget = null;  // Není vybraný konkrétní objekt
        }
    } else {
        el.classList.add('hidden');
        drawContextTarget = null;  // Reset při skrytí
    }
}

// Globální funkce pro aplikaci vlastností na kreslený objekt
function applyToDrawTarget(props) {
    const target = drawContextTarget || canvas.getActiveObject();
    if (!target) return;
    
    function applyPropsRecursive(obj) {
        if (!obj) return;
        obj.set(props);
        obj.dirty = true;
        if (obj._objects && obj._objects.length) {
            obj._objects.forEach(child => applyPropsRecursive(child));
        }
    }
    
    if (target.type === 'activeSelection') {
        target.getObjects().forEach(obj => {
            if (obj.layer === 'draw' || obj.type === 'path' || obj.type === 'group') {
                applyPropsRecursive(obj);
            }
        });
    } else {
        applyPropsRecursive(target);
    }
    
    if (target.setCoords) target.setCoords();
    canvas.requestRenderAll();
    saveHistoryState('draw-style');
}

document.addEventListener('DOMContentLoaded', () => {
    const sc = document.getElementById('drawFloatingStrokeColor');
    if (sc) sc.addEventListener('input', (e) => {
        const v = e.target.value;
        const drawColorEl = document.getElementById('drawColor');
        if (drawColorEl) drawColorEl.value = v;
        if (canvas.freeDrawingBrush) canvas.freeDrawingBrush.color = v;
        applyToDrawTarget({ stroke: v });
    });

    const fw = document.getElementById('drawFloatingStrokeWidth');
    if (fw) fw.addEventListener('input', (e) => {
        const v = parseInt(e.target.value, 10) || 1;
        const brushWidthEl = document.getElementById('brushWidth');
        if (brushWidthEl) brushWidthEl.value = v;
        if (canvas.freeDrawingBrush) canvas.freeDrawingBrush.width = v;
        applyToDrawTarget({ strokeWidth: v });
    });

    const styleEl = document.getElementById('drawFloatingStrokeStyle');
    if (styleEl) styleEl.addEventListener('change', (e) => {
        const v = e.target.value;
        document.getElementById('strokeStyle').value = v;
        const target = drawContextTarget || canvas.getActiveObject();
        if (target) {
            let dash = null;
            const widthVal = target.strokeWidth || 1;
            if (v === 'dashed') dash = [widthVal * 2, widthVal];
            if (v === 'dotted') dash = [widthVal, widthVal * 1.5];
            applyToDrawTarget({ strokeDashArray: dash });
        }
    });

    const capEl = document.getElementById('drawFloatingStrokeCap');
    if (capEl) capEl.addEventListener('change', (e) => {
        const v = e.target.value;
        document.getElementById('strokeCap').value = v;
        applyToDrawTarget({ strokeLineCap: v });
    });
    
    const fillEl = document.getElementById('drawFloatingFillColor');
    if (fillEl) fillEl.addEventListener('input', (e) => {
        const v = e.target.value;
        document.getElementById('fillColor').value = v;
        document.getElementById('fillTransparent').checked = false;
        applyToDrawTarget({ fill: v });
    });
    
    const transEl = document.getElementById('drawFloatingTransparent');
    if (transEl) transEl.addEventListener('change', (e) => {
        const checked = e.target.checked;
        document.getElementById('fillTransparent').checked = checked;
        const fillVal = checked ? 'transparent' : (document.getElementById('drawFloatingFillColor')?.value || '#ffffff');
        applyToDrawTarget({ fill: fillVal });
    });
});

canvas.on('mouse:down', (o) => {
    if (isFormatPainterActive && o.target) {
        const target = o.target;
        if (target && formatClipboard) {
            const propsToApply = {};
            for (const key in formatClipboard) {
                if (target.hasOwnProperty(key) && formatClipboard[key] !== undefined) {
                    propsToApply[key] = formatClipboard[key];
                }
            }
            target.set(propsToApply);

            if (target.type === 'i-text' || target.type === 'textbox') {
                 updateTextControlsUI(target);
            }
            if (target.layer === 'draw') {
                syncDrawingControls(target);
            }

            canvas.requestRenderAll();
            saveHistoryState('format-paste');
        }
        
        isFormatPainterActive = false;
        formatClipboard = null;
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        document.getElementById('copyFormatBtn').classList.remove('bg-green-500', 'text-white');
        
        // Zastav zpracování dalšího kreslení
        o.e.stopPropagation();
        o.e.preventDefault();
        return;
    }

    const e = o.e;
    const pointer = canvas.getPointer(e);

    if (snapLineIsActive) {
        snapLineMouseDown(o);
        return;
    }

    if (drawMode === 'eraser') {
        isDown = true;
        historyBatch = true;
        eraseAtPoint(pointer);
        return;
    }

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

    // Kreslení - ale ne když je režim 'select' nebo 'textSelect'
    if (drawMode && drawMode !== 'select' && drawMode !== 'textSelect') {
        isDown = true;
        historyBatch = true;

        // Snapping při začátku kreslení
        const snap = findSnapPoint(pointer);
        if (snap) {
            origX = snap.x;
            origY = snap.y;
            showSnapIndicator(snap);
        } else {
            origX = pointer.x;
            origY = pointer.y;
        }

        const width = parseInt(document.getElementById('brushWidth').value);

        if (drawMode === 'circle') {
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
            triangle = new fabric.Polygon([
                { x: 0, y: 0 }, { x: 1, y: 1 }, { x: 0, y: 1 }
            ], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(triangle);
            lastCreatedObject = triangle;
        }

        if (drawMode === 'rightTriangle') {
             rightTriangle = new fabric.Polygon([
                { x: 0, y: 0 }, { x: 1, y: 1 }, { x: 0, y: 1 }
            ], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(rightTriangle);
            lastCreatedObject = rightTriangle;
        }

        if (drawMode === 'ellipse') {
            ellipse = new fabric.Ellipse({
                left: origX, top: origY, rx: 1, ry: 1, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(ellipse);
            lastCreatedObject = ellipse;
        }

        if (drawMode === 'star') {
            star = new fabric.Polygon([], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(star);
            lastCreatedObject = star;
        }

        if (drawMode === 'heart') {
            const path = 'M -10 -2.5 a 5.5 5.5 0 0 1 9.591 -3.676 a 0.56 0.56 0 0 0 0.818 0 A 5.49 5.49 0 0 1 10 -2.5 c 0 2.29 -1.5 4 -3 5.5 l -5.492 5.313 a 2 2 0 0 1 -3 0.019 L -7 3 c -1.5 -1.5 -3 -3.2 -3 -5.5';
            heart = new fabric.Path(path, {
                left: origX,
                top: origY,
                originX: 'center',
                originY: 'center',
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeUniform: true,
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(heart);
            lastCreatedObject = heart;
        }

        if (drawMode === 'arrow') {
            arrow = new fabric.Polygon([
                { x: 0, y: 5 }, { x: 15, y: 5 }, { x: 15, y: 0 }, { x: 20, y: 7.5 }, { x: 15, y: 15 }, { x: 15, y: 10 }, { x: 0, y: 10 }
            ], {
                left: origX, top: origY, scaleX: 0.1, scaleY: 0.1, fill: getFillColor(), stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(arrow);
            lastCreatedObject = arrow;
        }

        if (drawMode === 'speechBubble') {
            speechBubble = new fabric.Polygon([
                { x: 0, y: 0 }, { x: 100, y: 0 }, { x: 100, y: 70 }, { x: 20, y: 70 }, { x: 10, y: 90 }, { x: 15, y: 70 }, { x: 0, y: 70 }
            ], {
                left: origX, top: origY, scaleX: 0.1, scaleY: 0.1, fill: getFillColor(), stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(speechBubble);
            lastCreatedObject = speechBubble;
        }

        if (drawMode === 'roundedSpeechBubble') {
            const path = 'M4,5 C4,3.89543 4.89543,3 6,3 H18 C19.1046,3 20,3.89543 20,5 V15 C20,16.1046 19.1046,17 18,17 H6L4,19V5Z';
            roundedSpeechBubble = new fabric.Path(path, {
                left: origX, top: origY, scaleX: 0.1, scaleY: 0.1, fill: getFillColor(), stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(roundedSpeechBubble);
            lastCreatedObject = roundedSpeechBubble;
        }

        if (drawMode === 'roundedRect') {
            roundedRect = new fabric.Rect({
                left: origX, top: origY, width: 1, height: 1, rx: 10, ry: 10, fill: getFillColor(),
                stroke: getStrokeColor(), strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true, strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(roundedRect);
            lastCreatedObject = roundedRect;
        }

        if (drawMode === 'arrowRight') {
            const path = 'M 0,10 Q 10,10 10,0 M 10,0 L 5,5 M 10,0 L 15,5';
            curvedArrow = new fabric.Path(path, {
                left: origX, top: origY, scaleX: 1, scaleY: 1, fill: 'transparent', stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(curvedArrow);
            lastCreatedObject = curvedArrow;
        }

        if (drawMode === 'hexagon') {
            hexagon = new fabric.Polygon([], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(hexagon);
            lastCreatedObject = hexagon;
        }

        if (drawMode === 'cross') {
            const thinWidth = Math.max(0.5, width * 0.05); // Základní šířka čáry
            cross = new fabric.Group([
                new fabric.Line([0, 0, 100, 100], { stroke: getStrokeColor(), strokeWidth: thinWidth, strokeLineCap: 'round' }),
                new fabric.Line([100, 0, 0, 100], { stroke: getStrokeColor(), strokeWidth: thinWidth, strokeLineCap: 'round' })
            ], {
                left: origX,
                top: origY,
                scaleX: 0.01,
                scaleY: 0.01,
                selectable: false,
                evented: false,
                layer: 'draw',
                strokeUniform: true
            });
            canvas.add(cross);
            lastCreatedObject = cross;
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

    if (snapLineIsActive) {
        snapLineMouseMove(o);
        return;
    }

    // Zobrazit snap indikátor u všech nástrojů (ale ne v režimu select/textSelect)
    if (drawMode && drawMode !== 'eraser' && drawMode !== 'textBox' && drawMode !== 'select' && drawMode !== 'textSelect') {
        const snap = findSnapPoint(pointer);
        if (snap) {
            showSnapIndicator(snap);
        } else {
            hideSnapIndicator();
        }
    }

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

    if (drawMode === 'eraser') {
        showEraserCursor(pointer);
        canvas.defaultCursor = 'none';
        canvas.hoverCursor = 'none';
    }
    
    if (drawMode === 'eraser' && isDown) {
        eraseAtPoint(pointer);
        return;
    }

    if (drawMode && isDown) {
        if (drawMode === 'circle') {
            const radius = Math.sqrt(Math.pow(origX - pointer.x, 2) + Math.pow(origY - pointer.y, 2));
            circle.set({ radius });
        }
        
        if (drawMode === 'rect') {
            const width = pointer.x - origX;
            const height = pointer.y - origY;
            rect.set({
                width: Math.abs(width), height: Math.abs(height),
                left: width < 0 ? pointer.x : origX,
                top: height < 0 ? pointer.y : origY
            });
        }
        
        if (drawMode === 'triangle') {
            const w = pointer.x - origX;
            const h = pointer.y - origY;
            const absW = Math.abs(w);
            const absH = Math.abs(h);
            const left = w < 0 ? pointer.x : origX;
            const top = h < 0 ? pointer.y : origY;
            triangle.set({
                points: [ { x: absW / 2, y: 0 }, { x: absW, y: absH }, { x: 0, y: absH } ],
                left: left,
                top: top
            });
            triangle._setPositionDimensions({});
            triangle.setCoords();
        }

        if (drawMode === 'rightTriangle') {
            const w = pointer.x - origX;
            const h = pointer.y - origY;
            const absW = Math.abs(w);
            const absH = Math.abs(h);
            const left = w < 0 ? pointer.x : origX;
            const top = h < 0 ? pointer.y : origY;
            rightTriangle.set({
                points: [ { x: 0, y: 0 }, { x: absW, y: absH }, { x: 0, y: absH } ],
                left: left,
                top: top
            });
            rightTriangle._setPositionDimensions({});
            rightTriangle.setCoords();
        }

        if (drawMode === 'ellipse') {
            const rx = Math.abs(pointer.x - origX) / 2;
            const ry = Math.abs(pointer.y - origY) / 2;
            ellipse.set({
                rx, ry,
                left: Math.min(pointer.x, origX),
                top: Math.min(pointer.y, origY)
            });
        }

        if (drawMode === 'star') {
            const size = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY));
            const points = [];
            const spikes = 5;
            const centerX = size / 2;
            const centerY = size / 2;
            for (let i = 0; i < spikes * 2; i++) {
                const radius = i % 2 === 0 ? size / 2 : size / 4;
                const angle = (i * Math.PI) / spikes - Math.PI / 2;
                points.push({ x: centerX + Math.cos(angle) * radius, y: centerY + Math.sin(angle) * radius });
            }
            star.set({ points });
            star._setPositionDimensions({});
            star.setCoords();
        }

        if (drawMode === 'heart' && heart) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 22;
            heart.set({ scaleX: scale, scaleY: scale });
            canvas.requestRenderAll();
        }
        
        if (drawMode === 'arrow') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            arrow.set({ scaleX: scale, scaleY: scale });
        }

        if (drawMode === 'speechBubble') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 100;
            speechBubble.set({ scaleX: scale, scaleY: scale });
        }

        if (drawMode === 'roundedSpeechBubble') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            roundedSpeechBubble.set({ scaleX: scale, scaleY: scale });
        }

        if (drawMode === 'roundedRect') {
            const width = pointer.x - origX;
            const height = pointer.y - origY;
            roundedRect.set({
                width: Math.abs(width), height: Math.abs(height),
                left: width < 0 ? pointer.x : origX,
                top: height < 0 ? pointer.y : origY
            });
        }
        
        if (drawMode === 'arrowRight') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 15;
            curvedArrow.set({ scaleX: scale, scaleY: scale });
        }
        
        if (drawMode === 'hexagon') {
            const size = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 2;
            const hexPoints = [];
            const centerX = size;
            const centerY = size;
            for (let i = 0; i < 6; i++) {
                const angle = (Math.PI / 3) * i - Math.PI / 6;
                hexPoints.push({ x: centerX + Math.cos(angle) * size, y: centerY + Math.sin(angle) * size });
            }
            hexagon.set({ points: hexPoints });
            hexagon._setPositionDimensions({});
            hexagon.setCoords();
        }

        if (drawMode === 'cross') {
            const w = Math.abs(pointer.x - origX);
            const h = Math.abs(pointer.y - origY);
            const size = Math.max(w, h);
            if (size > 1) {
                // Škálování od 0.01 (1 pixel) do size/100
                const scale = size / 100;
                cross.set({
                    scaleX: scale,
                    scaleY: scale
                });
                cross.setCoords();
            }
        }

        canvas.requestRenderAll();
        return;
    }

    if (isPanning) {
        const vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - lastPosX;
        vpt[5] += e.clientY - lastPosY;
        canvas.requestRenderAll();

        lastPosX = e.clientX;
        lastPosY = e.clientY;
    }
});

canvas.on('mouse:up', (o) => {
    if (snapLineIsActive) {
        snapLineMouseUp(o);
        return;
    }

    if (drawMode === 'textBox' && isDown) {
        isDown = false;
        
        let textObjectData;

        if (textBox.width < 10 || textBox.height < 10) {
            textObjectData = {
                left: origX,
                top: origY,
                width: 200, 
            };
        } else {
            textObjectData = {
                left: textBox.left,
                top: textBox.top,
                width: textBox.width,
            };
        }

        canvas.remove(textBox); 
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
        
        text.on('editing:entered', () => {
            if (text.hiddenTextarea) {
                text.hiddenTextarea.focus();
            }
            // synchronizuj UI při pohybu kurzoru nebo změně výběru
            text.on('selection:changed', () => syncTextToolbarFromObject(text));
        });

        text.on('editing:exited', () => {
            hideTextToolbar();
        });
        
        canvas.setActiveObject(text);
        text.enterEditing();
        text.selectAll(); // Select all text
        canvas.requestRenderAll();
        
        saveHistoryState('text-draw');
        return;
    }

    // Dokončení kreslení - ale ne v režimu 'select' nebo 'textSelect'
    if (drawMode && drawMode !== 'select' && drawMode !== 'textSelect' && isDown) {
        const objToSelect = lastCreatedObject;
        
        if (objToSelect) {
            // NEBUDEME nastavovat selectable: true - objekty zůstanou nevybíratelné při kreslení
            // objToSelect.set({
            //     selectable: true,
            //     evented: true
            // });

            // Změnit origin na center pro všechny tvary (kromě čar)
            if (objToSelect.type !== 'line') {
                const center = objToSelect.getCenterPoint();
                objToSelect.set({
                    originX: 'center',
                    originY: 'center',
                    left: center.x,
                    top: center.y
                });
                objToSelect.setCoords();
            }

            // NEBUDEME přepínat na select - zachováme aktivní nástroj
            // setDrawMode('select', document.getElementById('drawSelectBtn'));

            // NEBUDEME označovat objekt - rovnou kreslíme další
            // setTimeout(() => {
            //     canvas.setActiveObject(objToSelect);
            //     canvas.requestRenderAll();
            // }, 50);
        }
        
        line = circle = rect = triangle = rightTriangle = ellipse = star = heart = arrow = speechBubble = roundedSpeechBubble = roundedRect = curvedArrow = hexagon = cross = null;
        lastCreatedObject = null;
        isDown = false;
        historyBatch = false;
        hideSnapIndicator(); // Skryj snap indikátor po dokončení kreslení
        saveHistoryState('draw-finished');
        canvas.requestRenderAll();
    }
});

// Double-click na čáru aktivuje endpoint controls
canvas.on('mouse:dblclick', (o) => {
    const target = o.target;
    
    // Pouze pro čáry v draw layer
    if (target && target.type === 'line' && target.layer === 'draw') {
        // Vypnout endpoint controls na předchozí editované čáře
        if (currentEditingLine && currentEditingLine !== target) {
            disableLineEndpointsControls(currentEditingLine);
        }
        
        // Aktivovat endpoint controls na této čáře
        enableLineEndpointsControls(target);
        currentEditingLine = target;
        
        canvas.setActiveObject(target);
        canvas.requestRenderAll();
    }
});


document.getElementById('drawSelectBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('select', document.getElementById('drawSelectBtn'));
});
document.getElementById('drawLineBtn').addEventListener('click', () => {
    activateSnapLineTool();
    setActiveTool(document.getElementById('drawLineBtn'));
});
document.getElementById('drawCircleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('circle', document.getElementById('drawCircleBtn'));
});
document.getElementById('drawRectBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('rect', document.getElementById('drawRectBtn'));
});
document.getElementById('drawTriangleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('triangle', document.getElementById('drawTriangleBtn'));
});
document.getElementById('drawRightTriangleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('rightTriangle', document.getElementById('drawRightTriangleBtn'));
});
document.getElementById('drawEllipseBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('ellipse', document.getElementById('drawEllipseBtn'));
});
document.getElementById('drawStarBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('star', document.getElementById('drawStarBtn'));
});
document.getElementById('drawHeartBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('heart', document.getElementById('drawHeartBtn'));
});
document.getElementById('drawArrowBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('arrow', document.getElementById('drawArrowBtn'));
});
document.getElementById('drawSpeechBubbleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('speechBubble', document.getElementById('drawSpeechBubbleBtn'));
});
document.getElementById('drawRoundedSpeechBubbleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('roundedSpeechBubble', document.getElementById('drawRoundedSpeechBubbleBtn'));
});
document.getElementById('drawRoundedRectBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('roundedRect', document.getElementById('drawRoundedRectBtn'));
});
document.getElementById('drawArrowRightBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('arrowRight', document.getElementById('drawArrowRightBtn'));
});
document.getElementById('drawHexagonBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('hexagon', document.getElementById('drawHexagonBtn'));
});
document.getElementById('drawCrossBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('cross', document.getElementById('drawCrossBtn'));
});
document.getElementById('drawBrushBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('brush', document.getElementById('drawBrushBtn'));
});
document.getElementById('drawEraserBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('eraser', document.getElementById('drawEraserBtn'));
    canvas.defaultCursor = 'none';
    canvas.hoverCursor = 'none';
});

// Update brush/eraser settings when changed
document.getElementById('brushWidth').addEventListener('input', (e) => {
    if (drawMode === 'brush') {
        canvas.freeDrawingBrush.width = parseInt(e.target.value, 10);
    }
});
document.getElementById('drawColor').addEventListener('input', (e) => {
    if (drawMode === 'brush') {
        canvas.freeDrawingBrush.color = e.target.value;
    }
});
document.getElementById('eraserSize').addEventListener('input', (e) => {
    if (drawMode === 'eraser') {
        ERASER_RADIUS = parseInt(e.target.value, 10);
        if(eraserCursor) eraserCursor.set('radius', ERASER_RADIUS);
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

// Pomocná funkce pro aplikaci vlastností na objekt včetně skupin
function applyStrokePropsToObject(obj, props) {
    if (!obj) return;
    
    // Pro skupiny aplikuj na všechny děti
    if (obj.type === 'group' && obj._objects) {
        obj._objects.forEach(child => applyStrokePropsToObject(child, props));
        return;
    }
    
    // Zkontroluj že objekt podporuje stroke
    if (obj.stroke === undefined && obj.strokeWidth === undefined) return;
    
    obj.set(props);
}

// Pomocná funkce pro aplikaci stroke vlastností na výběr (včetně activeSelection a groups)
function applyStrokePropsToSelection(props) {
    const activeObj = canvas.getActiveObject();
    if (!activeObj) return false;
    
    if (activeObj.type === 'activeSelection') {
        activeObj.getObjects().forEach(obj => applyStrokePropsToObject(obj, props));
    } else {
        applyStrokePropsToObject(activeObj, props);
    }
    
    canvas.requestRenderAll();
    return true;
}

// Styl čáry
document.getElementById('strokeStyle').addEventListener('change', e => {
    const activeObj = canvas.getActiveObject();
    if (!activeObj) return;
    
    // Funkce pro získání dash array podle šířky objektu
    const applyDash = (obj) => {
        if (!obj || obj.stroke === undefined) return;
        const width = obj.strokeWidth || 1;
        obj.set({ strokeDashArray: getDashFromUIForWidth(width) });
    };
    
    if (activeObj.type === 'activeSelection') {
        activeObj.getObjects().forEach(obj => {
            if (obj.type === 'group' && obj._objects) {
                obj._objects.forEach(applyDash);
            } else {
                applyDash(obj);
            }
        });
    } else if (activeObj.type === 'group' && activeObj._objects) {
        activeObj._objects.forEach(applyDash);
    } else {
        applyDash(activeObj);
    }
    
    canvas.requestRenderAll();
});

// Zakončení čáry
document.getElementById('strokeCap').addEventListener('change', e => {
    const capValue = e.target.value;
    applyStrokePropsToSelection({ strokeLineCap: capValue });
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
        img.layer = 'background'; // Main loaded image is background layer
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
img.sendToBack(); // Always send background to back

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
        updateImageSize();
        
        // Generovat náhledy filtrů po načtení obrázku
        setTimeout(() => generateFilterPreviews(), 100);
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
    const filterImage = document.getElementById('filterLayerImage')?.checked ?? true;
    const filterDraw = document.getElementById('filterLayerDraw')?.checked ?? false;
    const filterText = document.getElementById('filterLayerText')?.checked ?? false;
    
    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    const baseFilters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];

    if (activeFilter) baseFilters.push(activeFilter);
    
    // Aplikovat filtry na obrázky (včetně přidaných)
    canvas.getObjects().forEach(obj => {
        if (obj === currentImage || obj.layer === 'image') {
            if (filterImage && obj.type === 'image') {
                obj.filters = [...baseFilters];
                obj.applyFilters();
            } else if (obj.type === 'image') {
                obj.filters = [];
                obj.applyFilters();
            }
        }
    });
    
    // Aplikovat/resetovat filtry na kresby
    canvas.getObjects().forEach(obj => {
        if (obj.layer === 'draw') {
            if (filterDraw) {
                applyFiltersToObject(obj, baseFilters, brightness, contrast, saturation);
            } else {
                resetObjectColors(obj);
            }
        }
    });
    
    // Aplikovat/resetovat filtry na text
    canvas.getObjects().forEach(obj => {
        if (obj.layer === 'text' || obj.type === 'i-text' || obj.type === 'textbox') {
            if (filterText) {
                applyFiltersToTextObject(obj, baseFilters, brightness, contrast, saturation);
            } else {
                resetTextObjectColors(obj);
            }
        }
    });
    
    canvas.requestRenderAll();
}

// Resetuje barvy objektu na původní
function resetObjectColors(obj) {
    if (!obj) return;
    if (obj._originalStroke) {
        obj.set({ stroke: obj._originalStroke });
        delete obj._originalStroke;
    }
    if (obj._originalFill) {
        obj.set({ fill: obj._originalFill });
        delete obj._originalFill;
    }
}

// Pomocná funkce pro aplikaci filtrů na jednotlivé objekty
function applyFiltersToObject(obj, filters, brightness = 0, contrast = 0, saturation = 0) {
    if (!obj) return;
    
    // Pro jednoduchost měníme pouze barvu podle filtrů
    const hasGrayscale = filters.some(f => f && f.type === 'Grayscale');
    const hasSepia = filters.some(f => f && f.type === 'Sepia');
    const hasInvert = filters.some(f => f && f.type === 'Invert');
    
    // Uložit původní barvu pokud není uložena
    if (!obj._originalStroke && obj.stroke) obj._originalStroke = obj.stroke;
    if (!obj._originalFill && obj.fill) obj._originalFill = obj.fill;
    
    // Funkce pro aplikaci všech filtrů na barvu
    const applyAllFilters = (color) => {
        if (!color || color === 'transparent') return color;
        let rgb = hexToRgb(color);
        if (!rgb) return color;
        
        // Brightness (-1 to 1, přidává/ubírá hodnotu)
        if (brightness !== 0) {
            const b = Math.round(brightness * 255);
            rgb.r = Math.max(0, Math.min(255, rgb.r + b));
            rgb.g = Math.max(0, Math.min(255, rgb.g + b));
            rgb.b = Math.max(0, Math.min(255, rgb.b + b));
        }
        
        // Contrast (-1 to 1)
        if (contrast !== 0) {
            const factor = (259 * (contrast * 255 + 255)) / (255 * (259 - contrast * 255));
            rgb.r = Math.max(0, Math.min(255, Math.round(factor * (rgb.r - 128) + 128)));
            rgb.g = Math.max(0, Math.min(255, Math.round(factor * (rgb.g - 128) + 128)));
            rgb.b = Math.max(0, Math.min(255, Math.round(factor * (rgb.b - 128) + 128)));
        }
        
        // Saturation (-1 to 1)
        if (saturation !== 0) {
            const gray = 0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b;
            const sat = 1 + saturation;
            rgb.r = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.r - gray))));
            rgb.g = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.g - gray))));
            rgb.b = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.b - gray))));
        }
        
        // Grayscale
        if (hasGrayscale) {
            const gray = Math.round(0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
            rgb = { r: gray, g: gray, b: gray };
        }
        
        // Sepia
        if (hasSepia) {
            const r = Math.min(255, Math.round(0.393 * rgb.r + 0.769 * rgb.g + 0.189 * rgb.b));
            const g = Math.min(255, Math.round(0.349 * rgb.r + 0.686 * rgb.g + 0.168 * rgb.b));
            const b = Math.min(255, Math.round(0.272 * rgb.r + 0.534 * rgb.g + 0.131 * rgb.b));
            rgb = { r, g, b };
        }
        
        // Invert
        if (hasInvert) {
            rgb = { r: 255 - rgb.r, g: 255 - rgb.g, b: 255 - rgb.b };
        }
        
        return rgbToHex(rgb.r, rgb.g, rgb.b);
    };
    
    // Aplikovat filtry na stroke a fill
    if (obj._originalStroke) {
        obj.set({ stroke: applyAllFilters(obj._originalStroke) });
    }
    if (obj._originalFill && obj._originalFill !== 'transparent') {
        obj.set({ fill: applyAllFilters(obj._originalFill) });
    }
}

// Speciální funkce pro aplikaci filtrů na textové objekty
function applyFiltersToTextObject(obj, filters, brightness = 0, contrast = 0, saturation = 0) {
    if (!obj) return;
    
    const hasGrayscale = filters.some(f => f && f.type === 'Grayscale');
    const hasSepia = filters.some(f => f && f.type === 'Sepia');
    const hasInvert = filters.some(f => f && f.type === 'Invert');
    
    // Uložit původní barvu textu
    if (!obj._originalFill && obj.fill) obj._originalFill = obj.fill;
    
    const applyAllFilters = (color) => {
        if (!color || color === 'transparent') return color;
        let rgb = hexToRgb(color);
        if (!rgb) return color;
        
        if (brightness !== 0) {
            const b = Math.round(brightness * 255);
            rgb.r = Math.max(0, Math.min(255, rgb.r + b));
            rgb.g = Math.max(0, Math.min(255, rgb.g + b));
            rgb.b = Math.max(0, Math.min(255, rgb.b + b));
        }
        
        if (contrast !== 0) {
            const factor = (259 * (contrast * 255 + 255)) / (255 * (259 - contrast * 255));
            rgb.r = Math.max(0, Math.min(255, Math.round(factor * (rgb.r - 128) + 128)));
            rgb.g = Math.max(0, Math.min(255, Math.round(factor * (rgb.g - 128) + 128)));
            rgb.b = Math.max(0, Math.min(255, Math.round(factor * (rgb.b - 128) + 128)));
        }
        
        if (saturation !== 0) {
            const gray = 0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b;
            const sat = 1 + saturation;
            rgb.r = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.r - gray))));
            rgb.g = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.g - gray))));
            rgb.b = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.b - gray))));
        }
        
        if (hasGrayscale) {
            const gray = Math.round(0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
            rgb = { r: gray, g: gray, b: gray };
        }
        
        if (hasSepia) {
            const r = Math.min(255, Math.round(0.393 * rgb.r + 0.769 * rgb.g + 0.189 * rgb.b));
            const g = Math.min(255, Math.round(0.349 * rgb.r + 0.686 * rgb.g + 0.168 * rgb.b));
            const b = Math.min(255, Math.round(0.272 * rgb.r + 0.534 * rgb.g + 0.131 * rgb.b));
            rgb = { r, g, b };
        }
        
        if (hasInvert) {
            rgb = { r: 255 - rgb.r, g: 255 - rgb.g, b: 255 - rgb.b };
        }
        
        return rgbToHex(rgb.r, rgb.g, rgb.b);
    };
    
    // Aplikovat pouze na fill (barva textu)
    if (obj._originalFill) {
        obj.set({ fill: applyAllFilters(obj._originalFill) });
        obj.dirty = true;
    }
}

// Reset barev textového objektu
function resetTextObjectColors(obj) {
    if (!obj) return;
    if (obj._originalFill) {
        obj.set({ fill: obj._originalFill });
        delete obj._originalFill;
        obj.dirty = true;
    }
}

// Pomocné funkce pro konverzi barev
function toGrayscale(color) {
    const rgb = hexToRgb(color);
    if (!rgb) return color;
    const gray = Math.round(0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
    return rgbToHex(gray, gray, gray);
}

function invertColor(color) {
    const rgb = hexToRgb(color);
    if (!rgb) return color;
    return rgbToHex(255 - rgb.r, 255 - rgb.g, 255 - rgb.b);
}

function toSepia(color) {
    const rgb = hexToRgb(color);
    if (!rgb) return color;
    const r = Math.min(255, Math.round(0.393 * rgb.r + 0.769 * rgb.g + 0.189 * rgb.b));
    const g = Math.min(255, Math.round(0.349 * rgb.r + 0.686 * rgb.g + 0.168 * rgb.b));
    const b = Math.min(255, Math.round(0.272 * rgb.r + 0.534 * rgb.g + 0.131 * rgb.b));
    return rgbToHex(r, g, b);
}

function hexToRgb(hex) {
    if (!hex || typeof hex !== 'string') return null;
    
    // Handle rgb()/rgba() format
    const rgbMatch = hex.match(/^rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
    if (rgbMatch) {
        return {
            r: parseInt(rgbMatch[1], 10),
            g: parseInt(rgbMatch[2], 10),
            b: parseInt(rgbMatch[3], 10)
        };
    }
    
    // Handle named colors
    const namedColors = {
        'black': '#000000', 'white': '#ffffff', 'red': '#ff0000', 'green': '#008000',
        'blue': '#0000ff', 'yellow': '#ffff00', 'cyan': '#00ffff', 'magenta': '#ff00ff',
        'gray': '#808080', 'grey': '#808080', 'orange': '#ffa500', 'pink': '#ffc0cb',
        'purple': '#800080', 'brown': '#a52a2a', 'navy': '#000080', 'teal': '#008080'
    };
    if (namedColors[hex.toLowerCase()]) {
        hex = namedColors[hex.toLowerCase()];
    }
    
    hex = hex.replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    if (hex.length !== 6) return null;
    return {
        r: parseInt(hex.substr(0, 2), 16),
        g: parseInt(hex.substr(2, 2), 16),
        b: parseInt(hex.substr(4, 2), 16)
    };
}

function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
}

// Generování dynamických náhledů filtrů z aktuálního obrázku
function generateFilterPreviews() {
    if (!currentImage || !currentImage._element) return;
    
    const sourceEl = currentImage._element;
    const previewHeight = 60;
    const aspectRatio = sourceEl.naturalWidth / sourceEl.naturalHeight;
    const previewWidth = Math.round(previewHeight * aspectRatio);
    
    const filterTypes = {
        'original': null,
        'grayscale': new fabric.Image.filters.Grayscale(),
        'sepia': new fabric.Image.filters.Sepia(),
        'invert': new fabric.Image.filters.Invert(),
        'blur': new fabric.Image.filters.Blur({ blur: 0.3 }),
        'sharpen': new fabric.Image.filters.Convolute({ matrix: [0, -1, 0, -1, 5, -1, 0, -1, 0] })
    };
    
    document.querySelectorAll('.filter-thumb-wrap').forEach(wrap => {
        const filterName = wrap.getAttribute('data-filter');
        const previewCanvas = wrap.querySelector('.filter-preview-canvas');
        if (!previewCanvas) return;
        
        previewCanvas.width = previewWidth;
        previewCanvas.height = previewHeight;
        
        // Vytvořit dočasný fabric canvas pro náhled
        const tempFabricImg = new fabric.Image(sourceEl, {
            left: 0,
            top: 0,
            scaleX: previewWidth / sourceEl.naturalWidth,
            scaleY: previewHeight / sourceEl.naturalHeight
        });
        
        if (filterTypes[filterName]) {
            tempFabricImg.filters = [filterTypes[filterName]];
            tempFabricImg.applyFilters();
        }
        
        const ctx = previewCanvas.getContext('2d');
        ctx.clearRect(0, 0, previewWidth, previewHeight);
        
        // Render na canvas
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = previewWidth;
        tempCanvas.height = previewHeight;
        const tempCtx = tempCanvas.getContext('2d');
        tempFabricImg.render(tempCtx);
        ctx.drawImage(tempCanvas, 0, 0);
    });
}

// Kliknutí na náhled filtru
document.querySelectorAll('.filter-thumb-wrap').forEach(wrap => {
    wrap.addEventListener('click', () => {
        document.querySelectorAll('.filter-thumb-wrap').forEach(w => w.classList.remove('ring-2', 'ring-blue-500'));
        wrap.classList.add('ring-2', 'ring-blue-500');

        const type = wrap.getAttribute('data-filter');
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

// Checkboxy pro vrstvy filtrů
document.querySelectorAll('.filterLayerCheck').forEach(checkbox => {
    checkbox.addEventListener('change', applyFilters);
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

// Helper: clamp value between min and max
function clamp(v, min, max) {
    return Math.min(Math.max(v, min), max);
}

// Helper: fit object into canvas with padding (for overlay images)
function fitObjectIntoCanvas(obj, padding = 20, allowUpscale = false) {
    if (!obj) return;

    const canvasW = canvas.width;
    const canvasH = canvas.height;

    // Get object dimensions
    const objW = obj.width * obj.scaleX;
    const objH = obj.height * obj.scaleY;

    // Calculate scale to fit within canvas with padding
    const maxW = canvasW - padding * 2;
    const maxH = canvasH - padding * 2;

    let scale = Math.min(maxW / objW, maxH / objH);

    // Don't upscale if not allowed
    if (!allowUpscale && scale > 1) {
        scale = 1;
    }

    // Apply uniform scale
    obj.set({
        scaleX: obj.scaleX * scale,
        scaleY: obj.scaleY * scale,
        left: canvasW / 2,
        top: canvasH / 2,
        originX: 'center',
        originY: 'center'
    });

    obj.setCoords();
}

// Helper: check if ANY checkbox with given class is checked (for layer visibility)
function anyChecked(selectorClass) {
    const checkboxes = document.querySelectorAll(`.${selectorClass}`);
    for (let cb of checkboxes) {
        if (cb.checked) return true;
    }
    return false;
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        // vypnout kreslení
        drawMode = null;
        isDown = false;
        
        // Deaktivovat snap-line nástroj
        deactivateSnapLineTool();
        
        // Vypnout endpoint controls na všech čárách
        disableAllLineEndpoints();
        
        // Skrýt kurzor gumy
        hideEraserCursor();
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        canvas.isDrawingMode = false;
        
        const drawToolbar = document.getElementById('drawFloatingToolbar');
        if (drawToolbar) drawToolbar.classList.add('hidden');
        
        // Skrýt text toolbar
        const textTb = document.getElementById('textToolbar');
        if (textTb) textTb.classList.add('hidden');
        
        // Skrýt kontextová menu
        const ctxMenu = document.getElementById('contextMenu');
        if (ctxMenu) ctxMenu.classList.add('hidden');
        const textCtxMenu = document.getElementById('textContextMenu');
        if (textCtxMenu) textCtxMenu.classList.add('hidden');

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
    activateSnapLineTool();
    setActiveTool(this);
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
    deactivateSnapLineTool();
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
    deactivateSnapLineTool();
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
                    // Zachovat původní styl čáry
                    strokeLineCap: obj.strokeLineCap || 'round',
                    strokeLineJoin: obj.strokeLineJoin || 'round',
                    strokeDashArray: obj.strokeDashArray || null,
                    strokeUniform: obj.strokeUniform !== undefined ? obj.strokeUniform : true,
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
    // Use anyChecked to handle multiple checkboxes in different panels
    const showImage = anyChecked('layerImageCheck');
    const showDraw = anyChecked('layerDrawCheck');
    const showText = anyChecked('layerTextCheck');

    canvas.getObjects().forEach(obj => {
        // Both 'background' and 'image' layers controlled by layerImageCheck
        if (obj.layer === 'background' || obj.layer === 'image') obj.visible = showImage;
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

// Double-click on overlay image to set as base image
canvas.on('mouse:dblclick', function(opt) {
    const target = opt.target;

    // Only works on overlay images (not already background)
    if (!target || target.type !== 'image' || target.layer !== 'image') return;

    // Swap with currentImage
    if (currentImage && currentImage !== target) {
        // Set old background as overlay
        currentImage.layer = 'image';
        currentImage.set({
            selectable: true,
            evented: true
        });
    }

    // Set clicked overlay as new background
    target.layer = 'background';
    currentImage = target;

    // Send to back
    currentImage.sendToBack();

    // Update UI
    updateImageSize();
    updateRotationAngle();
    canvas.requestRenderAll();

    showToast('Obrázek nastaven jako základní', 'success');
});

function handleSelectionChange(e) {
    const activeObject = canvas.getActiveObject();
    
    // Vypnout endpoint controls pokud se vybere jiný objekt než čára
    if (currentEditingLine && activeObject !== currentEditingLine) {
        disableLineEndpointsControls(currentEditingLine);
        currentEditingLine = null;
    }

    if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox')) {
        updateTextControlsUI(activeObject);
        hideDrawFloatingToolbar();
    } else {
        if (activeObject) {
            syncDrawingControls(activeObject);
            if (activeObject.layer === 'draw' || 
                (activeObject.type === 'activeSelection' && activeObject.getObjects().some(o => o.layer === 'draw'))) {
                updateDrawFloatingToolbarVisibility();
            }
        } else {
            if (!drawMode || drawMode === 'select' || drawMode === 'textSelect') {
                hideDrawFloatingToolbar();
            }
        }
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
    const color = document.getElementById('drawColor').value;
    
    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.stroke !== undefined) obj.set({ stroke: color });
                if (obj.type === 'group' && obj._objects) {
                    obj._objects.forEach(child => {
                        if (child.stroke !== undefined) child.set({ stroke: color });
                    });
                }
            });
        } else {
            if (activeObj.stroke !== undefined) activeObj.set({ stroke: color });
            if (activeObj.type === 'group' && activeObj._objects) {
                activeObj._objects.forEach(child => {
                    if (child.stroke !== undefined) child.set({ stroke: color });
                });
            }
        }
        canvas.requestRenderAll();
    }

    if (
        canvas.isDrawingMode &&
        canvas.freeDrawingBrush instanceof fabric.PencilBrush
    ) {
        canvas.freeDrawingBrush.color = color;
    }
});

// Aktualizace barvy výplně aktivního objektu
document.getElementById('fillColor').addEventListener('input', () => {
    const fillColor = document.getElementById('fillColor').value;
    const isTransparent = document.getElementById('fillTransparent').checked;
    const fillVal = isTransparent ? 'transparent' : fillColor;
    
    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.fill !== undefined) obj.set({ fill: fillVal });
            });
        } else {
            if (activeObj.fill !== undefined) activeObj.set({ fill: fillVal });
        }
        canvas.requestRenderAll();
    }
});

// Aktualizace průhledné výplně aktivního objektu
document.getElementById('fillTransparent').addEventListener('change', () => {
    const isTransparent = document.getElementById('fillTransparent').checked;
    const fillVal = isTransparent ? 'transparent' : document.getElementById('fillColor').value;
    
    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.fill !== undefined) obj.set({ fill: fillVal });
            });
        } else {
            if (activeObj.fill !== undefined) activeObj.set({ fill: fillVal });
        }
        canvas.requestRenderAll();
    }
});

// velikost tužky
document.getElementById('brushWidth').addEventListener('input', (e) => {
    const width = parseInt(e.target.value);

    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.strokeWidth !== undefined) {
                    obj.set({ strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width) });
                }
                if (obj.type === 'group' && obj._objects) {
                    obj._objects.forEach(child => {
                        if (child.strokeWidth !== undefined) child.set({ strokeWidth: width });
                    });
                }
            });
        } else {
            if (activeObj.strokeWidth !== undefined) {
                activeObj.set({ strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width) });
            }
            if (activeObj.type === 'group' && activeObj._objects) {
                activeObj._objects.forEach(child => {
                    if (child.strokeWidth !== undefined) child.set({ strokeWidth: width });
                });
            }
        }
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

// Úprava vlastností textu
function applyToActiveText(props) {
    const obj = canvas.getActiveObject();
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;

    // Klíče, které Fabric.js podporuje pro selection styles
    const selectionKeys = ['fontWeight', 'fontStyle', 'underline', 'linethrough', 'fill', 'fontFamily', 'fontSize'];
    const selectionProps = {};
    let hasSelectionProp = false;
    Object.keys(props).forEach(key => {
        if (selectionKeys.includes(key)) {
            selectionProps[key] = props[key];
            hasSelectionProp = true;
        }
    });

    // Pokud je textový objekt v editačním módu a má aktivní výběr, aplikuj styl jen na výběr
    if ((obj.type === 'i-text' || obj.type === 'textbox') && obj.isEditing && obj.selectionStart !== obj.selectionEnd && hasSelectionProp) {
        obj.setSelectionStyles(selectionProps, obj.selectionStart, obj.selectionEnd);
        obj.text = obj.text; // force re-render
        obj.setCoords();
        canvas.requestRenderAll();
        scheduleTextHistory();
        return;
    }
    // Jinak aplikuj na celý textbox
    obj.set(props);
    obj.setCoords();
    canvas.requestRenderAll();
    scheduleTextHistory();
}

// Synchronizace textových ovladačů s aktivním objektem
function updateTextControlsUI(obj) {
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) {
        resetTextControlsUI();
        return;
    }

    document.getElementById('textInput').value = obj.text || '';
    document.getElementById('textSize').value = obj.fontSize;
    document.getElementById('textColor').value = obj.fill;
    setFontPickerValue(obj.fontFamily || 'Arial');

    // Pokud je textový objekt v editačním módu a má aktivní výběr, načti styl z výběru
    if ((obj.type === 'i-text' || obj.type === 'textbox') && obj.isEditing && obj.selectionStart !== obj.selectionEnd) {
        const styles = obj.getSelectionStyles(obj.selectionStart, obj.selectionEnd)[0] || {};
        textStyles.bold = styles.fontWeight === 'bold';
        textStyles.italic = styles.fontStyle === 'italic';
        textStyles.underline = styles.underline === true;
        textStyles.linethrough = styles.linethrough === true;
        document.getElementById('textSize').value = styles.fontSize || obj.fontSize;
        document.getElementById('textColor').value = styles.fill || obj.fill;
        setFontPickerValue(styles.fontFamily || obj.fontFamily || 'Arial');
    } else {
        textStyles.bold = obj.fontWeight === 'bold';
        textStyles.italic = obj.fontStyle === 'italic';
        textStyles.underline = obj.underline === true;
        textStyles.linethrough = obj.linethrough === true;
    }
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
    const obj = canvas.getActiveObject();
    if (obj) applyStyleToSelectionOrAll(obj, { fontSize: parseInt(e.target.value) });
});
document.getElementById('textColor').addEventListener('input', e => {
    const obj = canvas.getActiveObject();
    if (obj) applyStyleToSelectionOrAll(obj, { fill: e.target.value });
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

// Aplikuje styly na vybraný text nebo celý objekt
function applyStyleToSelectionOrAll(obj, styleProps) {
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;

    const start = obj.selectionStart;
    const end = obj.selectionEnd;

    // Pokud je v editačním módu a má výběr, aplikuj jen na výběr
    if (obj.isEditing && typeof start !== 'undefined' && typeof end !== 'undefined' && start !== end) {
        obj.setSelectionStyles(styleProps, start, end);
    } else {
        // Jinak aplikuj na celý text
        obj.set(styleProps);
    }

    obj.dirty = true;
    canvas.requestRenderAll();
    scheduleTextHistory();
}

function toggleTextStyle(style) {
    const activeObject = canvas.getActiveObject();
    let currentState = textStyles[style];
    // Pokud je textový objekt v editačním módu a má aktivní výběr, zjisti styl z výběru
    if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox') && activeObject.isEditing && activeObject.selectionStart !== activeObject.selectionEnd) {
        const styles = activeObject.getSelectionStyles(activeObject.selectionStart, activeObject.selectionEnd)[0] || {};
        if (style === 'bold') currentState = styles.fontWeight === 'bold';
        if (style === 'italic') currentState = styles.fontStyle === 'italic';
        if (style === 'underline') currentState = styles.underline === true;
        if (style === 'linethrough') currentState = styles.linethrough === true;
    } else if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox')) {
        if (style === 'bold') currentState = activeObject.fontWeight === 'bold';
        if (style === 'italic') currentState = activeObject.fontStyle === 'italic';
        if (style === 'underline') currentState = activeObject.underline === true;
        if (style === 'linethrough') currentState = activeObject.linethrough === true;
    }

    const newState = !currentState;
    textStyles[style] = newState;
    updateStyleButtons();
    const props = {};
    if (style === 'bold') props.fontWeight = newState ? 'bold' : 'normal';
    if (style === 'italic') props.fontStyle = newState ? 'italic' : 'normal';
    if (style === 'underline') props.underline = newState;
    if (style === 'linethrough') props.linethrough = newState;
    const obj = canvas.getActiveObject();
    if (obj) applyStyleToSelectionOrAll(obj, props);
    scheduleTextHistory();
    syncTextToolbarFromObject(obj);
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

        const rect = canvas.upperCanvasEl.getBoundingClientRect();

        if (target.type === 'i-text' || target.type === 'textbox') {
            const menu = document.getElementById('textContextMenu');
            // Nejprve zobrazit pro změření výšky
            menu.style.visibility = 'hidden';
            menu.classList.remove('hidden');
            const menuHeight = menu.offsetHeight;
            menu.style.visibility = '';
            
            // Pozice - menu se otevírá nahoru (nad kurzor)
            const posX = e.clientX - rect.left + canvas.upperCanvasEl.offsetLeft;
            const posY = e.clientY - rect.top + canvas.upperCanvasEl.offsetTop - menuHeight;
            
            menu.style.left = posX + 'px';
            menu.style.top = Math.max(0, posY) + 'px';
        } 
        else if (target.layer === 'draw' || target.layer === 'image' || (target.type === 'activeSelection' && target.getObjects().some(o => o.layer === 'draw' || o.layer === 'image'))) {
            contextMenu.style.left = (e.clientX - rect.left + canvas.upperCanvasEl.offsetLeft) + 'px';
            contextMenu.style.top = (e.clientY - rect.top + canvas.upperCanvasEl.offsetTop) + 'px';
            contextMenu.classList.remove('hidden');
            // Sync hodnoty pro styl čáry
            try { syncDrawContextMenuFromObject(target); } catch (err) {}
        }
    } else {
        hideContextMenu();
    }
});

// Skrytí menu při kliknutí jinam
document.addEventListener('click', function(e) {
    const textCtxMenu = document.getElementById('textContextMenu');
    if (!contextMenu.contains(e.target) && (!textCtxMenu || !textCtxMenu.contains(e.target))) {
        hideContextMenu();
    }
});

// Skrytí při scrollu nebo klávese Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideContextMenu();
        if (isFormatPainterActive) {
            isFormatPainterActive = false;
            formatClipboard = null;
            canvas.defaultCursor = 'default';
            canvas.hoverCursor = 'move';
            document.getElementById('copyFormatBtn').classList.remove('bg-green-500', 'text-white');
        }
    }
});

function hideContextMenu() {
    contextMenu.classList.add('hidden');
    const t = document.getElementById('textContextMenu');
    if (t) t.classList.add('hidden');
    contextTarget = null;
}

function syncTextContextMenuFromObject(obj) {
    // Text context menu now only has layer actions, no need to sync styling
}

function syncDrawContextMenuFromObject(obj) {
    if (!obj) return;
    try {
        // Pro multi-selection použij první draw objekt
        let targetObj = obj;
        if (obj.type === 'activeSelection') {
            targetObj = obj.getObjects().find(o => o.layer === 'draw') || obj.getObjects()[0];
        }
        
        const strokeColorEl = document.getElementById('ctxDrawStrokeColor');
        const fillColorEl = document.getElementById('ctxDrawFillColor');
        const fillTransparentEl = document.getElementById('ctxDrawFillTransparent');
        const strokeWidthEl = document.getElementById('ctxDrawStrokeWidth');
        const strokeStyleEl = document.getElementById('ctxDrawStrokeStyle');
        
        if (strokeColorEl && targetObj.stroke) strokeColorEl.value = targetObj.stroke;
        if (fillColorEl) {
            if (targetObj.fill && targetObj.fill !== 'transparent') {
                fillColorEl.value = targetObj.fill;
                if (fillTransparentEl) fillTransparentEl.checked = false;
            } else {
                if (fillTransparentEl) fillTransparentEl.checked = true;
            }
        }
        if (strokeWidthEl) strokeWidthEl.value = targetObj.strokeWidth || 1;
        if (strokeStyleEl) {
            const dash = targetObj.strokeDashArray;
            if (!dash || dash.length === 0) {
                strokeStyleEl.value = 'solid';
            } else if (dash[0] > dash[1]) {
                strokeStyleEl.value = 'dashed';
            } else {
                strokeStyleEl.value = 'dotted';
            }
        }
    } catch (err) {}
}

function showDrawFloatingAt(clientX, clientY, targetObj) {
    const el = document.getElementById('drawFloatingToolbar');
    if (!el) return;
    drawContextTarget = targetObj;
    const rect = canvas.upperCanvasEl.getBoundingClientRect();
    el.style.left = (clientX - rect.left + canvas.upperCanvasEl.offsetLeft) + 'px';
    el.style.top = (clientY - rect.top + canvas.upperCanvasEl.offsetTop) + 'px';
    el.classList.remove('hidden');

    try {
        const stroke = targetObj.stroke || '#000000';
        const fill = targetObj.fill || 'transparent';
        const width = targetObj.strokeWidth || 1;
        const dash = targetObj.strokeDashArray || null;
        const cap = targetObj.strokeLineCap || 'butt';

        document.getElementById('drawFloatingStrokeColor').value = stroke;
        document.getElementById('drawFloatingFillColor').value = (fill === 'transparent' ? '#ffffff' : fill);
        document.getElementById('drawFloatingStrokeWidth').value = width;
        if (dash && dash.length) {
            const w = parseFloat(width) || 1;
            if (dash[0] === w*2) document.getElementById('drawFloatingStrokeStyle').value = 'dashed';
            else document.getElementById('drawFloatingStrokeStyle').value = 'dotted';
        } else {
            document.getElementById('drawFloatingStrokeStyle').value = 'solid';
        }
        document.getElementById('drawFloatingStrokeCap').value = cap;
        document.getElementById('drawFloatingTransparent').checked = (fill === 'transparent');
    } catch (err) {}
}

function hideDrawFloatingToolbar() {
    const el = document.getElementById('drawFloatingToolbar');
    if (!el) return;
    el.classList.add('hidden');
    drawContextTarget = null;
}

const textContextMenu = document.getElementById('textContextMenu');
if (textContextMenu) {
    // Event handlery pro textové kontextové menu (layer actions)
    document.querySelectorAll('.text-context-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = canvas.getActiveObject();
            if (!target) return;
            
            const action = this.dataset.action;
            
            switch(action) {
                case 'bringForward':
                    canvas.bringForward(target);
                    break;
                case 'sendBackward':
                    canvas.sendBackwards(target);
                    break;
                case 'bringToFront':
                    canvas.bringToFront(target);
                    break;
                case 'sendToBack':
                    canvas.sendToBack(target);
                    break;
                case 'delete':
                    canvas.remove(target);
                    break;
                case 'copy':
                    target.clone(function(cloned) {
                        cloned.layer = target.layer;
                        cloned.erasable = target.erasable;
                        cloned._originalStroke = target._originalStroke;
                        cloned._originalFill = target._originalFill;
                        clipboard = cloned;
                    }, HISTORY.extraProps);
                    break;
                case 'paste':
                    pasteObject();
                    break;
            }
            
            canvas.requestRenderAll();
            hideContextMenu();
        });
    });
}

// Event handler pro font picker v text toolbaru
document.getElementById('tbFont')?.addEventListener('change', (e) => {
    const obj = canvas.getActiveObject();
    if (!obj) return;
    applyToActiveText({ fontFamily: e.target.value });
    setFontPickerValue(e.target.value);
    scheduleTextHistory();
    syncTextToolbarFromObject(obj);
});

// Clipboard pro kopírování/vkládání
let clipboard = null;

function copyObject() {
    const obj = canvas.getActiveObject();
    if (!obj || obj === currentImage) return;
    
    obj.clone(function(cloned) {
        // Preserve custom properties
        cloned.layer = obj.layer;
        cloned.erasable = obj.erasable;
        cloned._originalStroke = obj._originalStroke;
        cloned._originalFill = obj._originalFill;
        clipboard = cloned;
    }, HISTORY.extraProps);
}

function pasteObject() {
    if (!clipboard) return;
    
    clipboard.clone(function(clonedObj) {
        canvas.discardActiveObject();
        
        // Preserve custom properties from clipboard
        clonedObj.layer = clipboard.layer;
        clonedObj.erasable = clipboard.erasable;
        clonedObj._originalStroke = clipboard._originalStroke;
        clonedObj._originalFill = clipboard._originalFill;
        
        clonedObj.set({
            left: clonedObj.left + 20,
            top: clonedObj.top + 20,
            evented: true,
            selectable: true
        });
        
        if (clonedObj.type === 'activeSelection') {
            clonedObj.canvas = canvas;
            clonedObj.forEachObject(function(obj, i) {
                // Copy layer from original objects if available
                if (clipboard._objects && clipboard._objects[i]) {
                    obj.layer = clipboard._objects[i].layer;
                    obj.erasable = clipboard._objects[i].erasable;
                    obj._originalStroke = clipboard._objects[i]._originalStroke;
                    obj._originalFill = clipboard._objects[i]._originalFill;
                }
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
        saveHistoryState('paste');
        // Reapply filters to include pasted object
        applyFilters();
    }, HISTORY.extraProps);
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
                        cloned.layer = contextTarget.layer;
                        cloned.erasable = contextTarget.erasable;
                        cloned._originalStroke = contextTarget._originalStroke;
                        cloned._originalFill = contextTarget._originalFill;
                        clipboard = cloned;
                    }, HISTORY.extraProps);
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

// Event handlery pro styl čáry v kontextovém menu
document.getElementById('ctxDrawStrokeColor')?.addEventListener('input', (e) => {
    if (!contextTarget) return;
    applyToContextDrawTarget({ stroke: e.target.value });
});

document.getElementById('ctxDrawFillColor')?.addEventListener('input', (e) => {
    if (!contextTarget) return;
    document.getElementById('ctxDrawFillTransparent').checked = false;
    applyToContextDrawTarget({ fill: e.target.value });
});

document.getElementById('ctxDrawFillTransparent')?.addEventListener('change', (e) => {
    if (!contextTarget) return;
    const fillVal = e.target.checked ? 'transparent' : (document.getElementById('ctxDrawFillColor')?.value || '#ffffff');
    applyToContextDrawTarget({ fill: fillVal });
});

document.getElementById('ctxDrawStrokeWidth')?.addEventListener('input', (e) => {
    if (!contextTarget) return;
    applyToContextDrawTarget({ strokeWidth: parseInt(e.target.value, 10) || 1 });
});

document.getElementById('ctxDrawStrokeStyle')?.addEventListener('change', (e) => {
    if (!contextTarget) return;
    const v = e.target.value;
    const widthVal = parseInt(document.getElementById('ctxDrawStrokeWidth')?.value, 10) || 1;
    let dash = null;
    if (v === 'dashed') dash = [widthVal * 2, widthVal];
    if (v === 'dotted') dash = [widthVal, widthVal * 1.5];
    applyToContextDrawTarget({ strokeDashArray: dash });
});

function applyToContextDrawTarget(props) {
    if (!contextTarget) return;
    
    function applyPropsRecursive(obj) {
        if (!obj) return;
        obj.set(props);
        obj.dirty = true;
        if (obj._objects && obj._objects.length) {
            obj._objects.forEach(child => applyPropsRecursive(child));
        }
    }
    
    if (contextTarget.type === 'activeSelection') {
        contextTarget.getObjects().forEach(obj => {
            if (obj.layer === 'draw' || obj.type === 'path' || obj.type === 'group' || obj.type === 'line') {
                applyPropsRecursive(obj);
            }
        });
    } else {
        applyPropsRecursive(contextTarget);
    }
    
    if (contextTarget.setCoords) contextTarget.setCoords();
    canvas.requestRenderAll();
    saveHistoryState('draw-style');
}

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
  
  // Po přesunutí čáry resetuj její transformace aby zůstala stabilní
  const obj = e.target;
  if (obj && obj.type === 'line' && obj.layer === 'draw') {
    // Získej aktuální koncové body v canvas souřadnicích
    const p1 = getLineEndpointCanvasPos(obj, 'p1');
    const p2 = getLineEndpointCanvasPos(obj, 'p2');
    // Nastav čáru znovu s resetovanými transformacemi
    setLineByCanvasPoints(obj, p1, p2);
  }
  
  saveHistoryState('modified');
});

// Přidání dalšího obrázku
document.getElementById('addImageBtn').addEventListener('click', () => {
    document.getElementById('addImageInput').click();
});
document.getElementById('addImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validace velikosti souboru (max 50 MB)
    const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50 MB
    if (file.size > MAX_FILE_SIZE) {
        showToast(`Soubor je příliš velký (${(file.size / 1024 / 1024).toFixed(1)} MB). Maximum je 50 MB.`, 'error', 5000);
        e.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(evt) {
        // Vytvoř dočasný Image element pro zjištění rozlišení
        const img = new Image();
        img.onload = function() {
            // Validace rozlišení (max 4K: 4000x2250)
            const MAX_WIDTH = 4000;  // Přesně 4K
            const MAX_HEIGHT = 2250; // Přesně 4K

            if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT) {
                showToast(
                    `Rozlišení je příliš velké (${img.width}×${img.height}). Maximum je 4K (4000×2250).`,
                    'error',
                    5000
                );
                e.target.value = '';
                return;
            }

            // Všechny validace prošly - načti obrázek jako overlay
            fabric.Image.fromURL(evt.target.result, function(fabricImg) {
                // Set as overlay image (not background)
                fabricImg.set({
                    originX: 'center',
                    originY: 'center',
                    scaleX: 1,
                    scaleY: 1,
                    selectable: true,
                    evented: true,
                    cornerStyle: 'circle',
                    hasRotatingPoint: true,
                    layer: 'image', // Overlay layer
                });

                // Fit into canvas with padding (don't upscale)
                fitObjectIntoCanvas(fabricImg, 20, false);

                canvas.add(fabricImg);
                canvas.setActiveObject(fabricImg);
                canvas.requestRenderAll();
                saveHistoryState('add-image');

                // Zobraz úspěšné oznámení
                showToast(`Obrázek vložen (${img.width}×${img.height})`, 'success');
            }, { crossOrigin: 'anonymous' });
        };
        img.onerror = function() {
            showToast('Nelze načíst obrázek. Zkuste jiný soubor.', 'error');
            e.target.value = '';
        };
        img.src = evt.target.result;
    };
    reader.onerror = function() {
        showToast('Chyba při čtení souboru. Zkuste znovu.', 'error');
        e.target.value = '';
    };
    reader.readAsDataURL(file);
    // Reset input pro možnost vložit stejný obrázek znovu
    e.target.value = '';
});

// Režimy úpravy textu: výběr vs. kreslení
function setTextEditingMode(mode) {
    if (mode === 'select') {
        drawMode = null;
        canvas.isDrawingMode = false;
        canvas.selection = true;
        if(currentImage) lockImage(false);
        setActiveTool(document.getElementById('textSelectBtn'));
        canvas.defaultCursor = 'default';
        canvas.getObjects().forEach(obj => {
            obj.selectable = true;
            obj.evented = true;
        });
        canvas.requestRenderAll();

        const drawToolbar = document.getElementById('drawFloatingToolbar');
        if (drawToolbar) drawToolbar.classList.add('hidden');
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

document.getElementById('copyFormatBtn').addEventListener('click', () => {
    const activeObject = canvas.getActiveObject();
    if (!activeObject) return;

    if (isFormatPainterActive) {
        // Cancel the mode
        isFormatPainterActive = false;
        formatClipboard = null;
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        document.getElementById('copyFormatBtn').classList.remove('bg-green-500', 'text-white');
        return;
    }

    formatClipboard = {
        
        fill: activeObject.fill,
        stroke: activeObject.stroke,
        strokeWidth: activeObject.strokeWidth,
        strokeDashArray: activeObject.strokeDashArray,
        strokeLineCap: activeObject.strokeLineCap,

        
        fontSize: activeObject.fontSize,
        fontFamily: activeObject.fontFamily,
        fontWeight: activeObject.fontWeight,
        fontStyle: activeObject.fontStyle,
        underline: activeObject.underline,
        linethrough: activeObject.linethrough,
        textAlign: activeObject.textAlign,
        fill: activeObject.fill,
    };
    
    isFormatPainterActive = true;
    canvas.defaultCursor = 'copy'; 
    canvas.hoverCursor = 'copy';
    document.getElementById('copyFormatBtn').classList.add('bg-green-500', 'text-white');
});

// TEXT TOOLBAR
const textToolbar = document.getElementById('textToolbar');

function showTextToolbar(textObj) {
  const rect = textObj.getBoundingRect(true);
  const canvasRect = canvas.upperCanvasEl.getBoundingClientRect();

  textToolbar.style.left = canvasRect.left + rect.left + rect.width / 2 - 140 + 'px';
  textToolbar.style.top  = canvasRect.top + rect.top - 40 + 'px';

  textToolbar.classList.remove('hidden');
    syncTextToolbarFromObject(textObj);
    hideDrawFloatingToolbar();
}

function hideTextToolbar() {
  textToolbar.classList.add('hidden');
}

function getFirstSelectionStyleValue(obj, prop) {
    if (!obj) return null;
    if (obj.isEditing) {
        const start = obj.selectionStart;
        const end = obj.selectionEnd;
        if (typeof start !== 'undefined' && start !== end) {
            const styles = obj.getSelectionStyles(start, end);
            if (!styles) return null;
            if (Array.isArray(styles)) return styles[0] ? styles[0][prop] : null;
            const key = Object.keys(styles)[0];
            return styles[key] ? styles[key][prop] : null;
        }
    }
    return obj[prop];
}

function syncTextToolbarFromObject(obj) {
    if (!obj) return;
    const isIText = obj.type === 'i-text' || obj.type === 'textbox';
    if (!isIText) return;

    // Bold
    const weight = getFirstSelectionStyleValue(obj, 'fontWeight') || obj.fontWeight;
    document.getElementById('textBold').classList.toggle('bg-gray-200', weight === 'bold');
    document.getElementById('tbBold')?.classList.toggle('bg-gray-200', weight === 'bold');
    // Italic
    const style = getFirstSelectionStyleValue(obj, 'fontStyle') || obj.fontStyle;
    document.getElementById('textItalic').classList.toggle('bg-gray-200', style === 'italic');
    document.getElementById('tbItalic')?.classList.toggle('bg-gray-200', style === 'italic');
    // Underline
    const underline = getFirstSelectionStyleValue(obj, 'underline');
    document.getElementById('textUnderline').classList.toggle('bg-gray-200', !!underline);
    document.getElementById('tbUnderline')?.classList.toggle('bg-gray-200', !!underline);
    // Linethrough
    const linethrough = getFirstSelectionStyleValue(obj, 'linethrough');
    document.getElementById('textLinethrough').classList.toggle('bg-gray-200', !!linethrough);
    document.getElementById('tbLinethrough')?.classList.toggle('bg-gray-200', !!linethrough);
    // Color
    const color = getFirstSelectionStyleValue(obj, 'fill') || obj.fill || '#000000';
    document.getElementById('textToolbarColor').value = color;
    // Font size
    const fontSize = obj.fontSize || 32;
    document.getElementById('textSize').value = fontSize;
    const tbSizeVal = document.getElementById('tbSizeVal');
    if (tbSizeVal) tbSizeVal.textContent = fontSize;
    // Font family
    const fontFamily = obj.fontFamily || 'Arial';
    const tbFontEl = document.getElementById('tbFont');
    if (tbFontEl) tbFontEl.value = fontFamily;
    setFontPickerValue(fontFamily);
}

// Event handlery pro floating text toolbar
document.addEventListener('DOMContentLoaded', () => {
    const tbBold = document.getElementById('tbBold');
    const tbItalic = document.getElementById('tbItalic');
    const tbUnderline = document.getElementById('tbUnderline');
    const tbLinethrough = document.getElementById('tbLinethrough');
    const tbSizeMinus = document.getElementById('tbSizeMinus');
    const tbSizePlus = document.getElementById('tbSizePlus');
    const tbAlignLeft = document.getElementById('tbAlignLeft');
    const tbAlignCenter = document.getElementById('tbAlignCenter');
    const tbAlignRight = document.getElementById('tbAlignRight');
    
    if (tbBold) tbBold.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('bold');
    });
    if (tbItalic) tbItalic.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('italic');
    });
    if (tbUnderline) tbUnderline.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('underline');
    });
    if (tbLinethrough) tbLinethrough.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('linethrough');
    });
    
    if (tbSizeMinus) tbSizeMinus.addEventListener('click', (e) => {
        e.preventDefault();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        const newSize = Math.max(8, (obj.fontSize || 32) - 2);
        obj.set({ fontSize: newSize });
        canvas.requestRenderAll();
        syncTextToolbarFromObject(obj);
        scheduleTextHistory();
    });
    
    if (tbSizePlus) tbSizePlus.addEventListener('click', (e) => {
        e.preventDefault();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        const newSize = Math.min(200, (obj.fontSize || 32) + 2);
        obj.set({ fontSize: newSize });
        canvas.requestRenderAll();
        syncTextToolbarFromObject(obj);
        scheduleTextHistory();
    });
    
    // Align handlers v DOMContentLoaded
    if (tbAlignLeft) tbAlignLeft.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        obj.set({ textAlign: 'left' });
        obj.dirty = true;
        canvas.requestRenderAll();
        scheduleTextHistory();
    });
    
    if (tbAlignCenter) tbAlignCenter.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        obj.set({ textAlign: 'center' });
        obj.dirty = true;
        canvas.requestRenderAll();
        scheduleTextHistory();
    });
    
    if (tbAlignRight) tbAlignRight.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        obj.set({ textAlign: 'right' });
        obj.dirty = true;
        canvas.requestRenderAll();
        scheduleTextHistory();
    });
    
});

canvas.on('selection:created', () => {
  const obj = canvas.getActiveObject();
  if (obj && (obj.type === 'i-text' || obj.type === 'textbox')) {
    showTextToolbar(obj);
  } else {
    hideTextToolbar();
  }
});

canvas.on('selection:updated', () => {
  const obj = canvas.getActiveObject();
  if (obj && (obj.type === 'i-text' || obj.type === 'textbox')) {
    showTextToolbar(obj);
  } else {
    hideTextToolbar();
  }
});

canvas.on('selection:cleared', hideTextToolbar);

canvas.on('object:moving', () => {
  const obj = canvas.getActiveObject();
  if (obj && (obj.type === 'i-text' || obj.type === 'textbox')) {
    showTextToolbar(obj);
  }
});

document.getElementById('textToolbarColor').oninput = (e) => {
    const obj = canvas.getActiveObject();
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
    applyStyleToSelectionOrAll(obj, { fill: e.target.value });
    scheduleTextHistory();
    syncTextToolbarFromObject(obj);
};

document.getElementById('deleteTextBtn').onclick = () => {
    const obj = canvas.getActiveObject();
    if (obj) {
        canvas.remove(obj);
        hideTextToolbar();
    }
};

window.addEventListener('beforeunload', function (e) { // alert při opuštění stránky
  e.preventDefault();
  e.returnValue = '';
});
</script>

</x-layout>
