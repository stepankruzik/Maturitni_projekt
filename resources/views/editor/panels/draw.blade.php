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

    <!-- KRESLÍCÍ NÁSTROJE -->
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

    <!-- Mřížka -->
    <button id="toggleGridBtn" class="tool-btn" title="Mřížka (pomůcka pro kreslení)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="3" y1="15" x2="21" y2="15"/>
            <line x1="9" y1="3" x2="9" y2="21"/>
            <line x1="15" y1="3" x2="15" y2="21"/>
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

    <!-- TVARY -->
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

    <!-- VRSTVY -->
    <div class="border-t pt-3 space-y-2">
        <p class="text-sm font-semibold text-gray-700 mb-2">Vrstvy</p>

        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
            <input type="checkbox" class="layerImageCheck w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" checked>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-gray-700">Obrázek</span>
        </label>

        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
            <input type="checkbox" class="layerDrawCheck w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" checked>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
            <span class="text-gray-700">Kreslení</span>
        </label>

        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
            <input type="checkbox" class="layerTextCheck w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" checked>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
            <span class="text-gray-700">Text</span>
        </label>
    </div>

</div>