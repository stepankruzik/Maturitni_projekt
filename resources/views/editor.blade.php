<x-layout
    :hideNav="true"
    :heading="$toolConfig['h1']"
    :seo-title="$toolConfig['title']"
    :meta-description="$toolConfig['description']"
    :canonical-url="route('tool.show', ['tool' => $tool])"
    :json-ld="$structuredData"
>
    <div
        id="photo-editor"
        data-default-mode="{{ $toolConfig['default_mode'] }}"
        data-requires-ai-credits="{{ $toolConfig['requires_ai_credits'] ? 'true' : 'false' }}"
        data-image-path="{{ $imagePath ?? '' }}"
        class="min-h-screen bg-slate-900/95 px-3 py-3 text-white md:px-4"
    >
    <div class="mb-3 flex flex-wrap gap-2 rounded-2xl border border-white/10 bg-slate-800/70 p-2 shadow-lg backdrop-blur-md">
    <button id="undoBtn" class="rounded-xl bg-slate-700 px-3 py-1 text-white transition-all duration-150 hover:bg-slate-600 active:bg-slate-900 disabled:cursor-not-allowed disabled:opacity-40" disabled title="Zpět">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-undo2-icon lucide-undo-2 w-5 h-5 inline">
            <path d="M9 14 4 9l5-5"/>
            <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/>
        </svg>
    </button>
    <button id="redoBtn" class="rounded-xl bg-slate-700 px-3 py-1 text-white transition-all duration-150 hover:bg-slate-600 active:bg-slate-900 disabled:cursor-not-allowed disabled:opacity-40" disabled title="Dopředu">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-redo2-icon lucide-redo-2 w-5 h-5 inline" style="transform: scaleX(-1);">
            <path d="M9 14 4 9l5-5"/>
            <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/>
        </svg>
    </button>
</div>

    <div class="mb-4 flex flex-wrap gap-2 rounded-2xl border border-white/10 bg-slate-800/60 p-2 shadow-lg backdrop-blur-md">
        <button class="tab-btn px-3 py-1 bg-blue-500 text-white rounded" data-target="panelResize">Resize/Ořez</button>
        <button class="tab-btn px-3 py-1 bg-green-500 text-white rounded" data-target="panelFilters">Filtry</button>
        <button class="tab-btn px-3 py-1 bg-orange-500 text-white rounded" data-target="panelDownload">Export</button>
        <button class="tab-btn px-3 py-1 bg-emerald-600 text-white rounded" data-target="panelBackground">Odstranit pozadí</button>
        <button class="tab-btn px-3 py-1 bg-sky-600 text-white rounded" data-target="panelPassport">Průkazová fotka</button>
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
        <input type="file" id="addImageInput" accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,image/jpeg,image/png,image/gif,image/bmp,image/webp" class="hidden">
    </div>

    <div class="flex flex-col gap-4 lg:min-h-[calc(100vh-10rem)] lg:flex-row">
    <!-- Sidebar vlevo -->
    <div class="w-full shrink-0 overflow-y-auto rounded-2xl border border-white/30 bg-white/80 p-4 text-slate-900 shadow-2xl backdrop-blur-md lg:max-h-[calc(100vh-10rem)] lg:w-72 xl:w-80">
        <h2 class="text-lg font-semibold mb-4">Úpravy obrázku</h2>



        <p id="imageSize" class="text-gray-600 font-semibold mb-1"></p>
        <p id="rotationAngle" class="text-gray-600 font-semibold mb-3"></p>

    @include('editor.panels.resize')

    @include('editor.panels.filters')

    @include('editor.panels.background-removal')

    @include('editor.panels.passport-photo')

    @include('editor.panels.export')

   @include('editor.panels.draw')

@include('editor.panels.text')

</div>

    <!-- Canvas vpravo -->
    <div id="canvasWorkspace" class="relative min-h-[60vh] min-w-0 flex-1 overflow-hidden rounded-2xl border border-white/10 bg-[#1e1e1e] shadow-2xl lg:min-h-[calc(100vh-10rem)]">
        <!-- HUD pro měření čáry -->
        <div id="lineHUD" class="hidden fixed z-50 rounded-xl border border-emerald-300/30 bg-slate-950/92 px-3.5 py-2 text-sm font-mono font-semibold text-emerald-50 shadow-2xl pointer-events-none whitespace-nowrap backdrop-blur-md">
            <span id="lineHUDText">0 px | 0°</span>
        </div>
        <div class="pointer-events-none absolute bottom-3 left-3 z-40 rounded-full border border-white/10 bg-black/35 px-3 py-1.5 text-xs text-gray-200 shadow-lg backdrop-blur-md">
            Kolečko = zoom · Alt / střední tlačítko = posun
        </div>
        <!-- Plovoucí toolbar pro kreslení -->
        <div id="drawFloatingToolbar" class="hidden fixed bg-white/95 backdrop-blur-sm border border-gray-200 rounded-xl shadow-xl px-3 py-2.5 z-50 flex gap-3 items-center text-slate-900">
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
        <div id="textToolbar" class="hidden fixed bg-white/95 backdrop-blur-sm shadow-xl rounded-xl px-3 py-2 flex items-center gap-2 z-50 border border-gray-200 text-slate-900">
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
        <div id="textContextMenu" class="hidden absolute bg-white border border-gray-300 rounded-lg shadow-lg py-1 z-50 min-w-[150px] text-slate-900">
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
        <canvas id="canvas" class="block" style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;"></canvas>
        
        <!-- Kontextové menu -->
        <div id="contextMenu" class="hidden absolute bg-white border border-gray-300 rounded-lg shadow-lg py-1 z-50 min-w-[150px] text-slate-900">
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




    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/editor/app.js'])
</x-layout>