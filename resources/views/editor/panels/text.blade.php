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