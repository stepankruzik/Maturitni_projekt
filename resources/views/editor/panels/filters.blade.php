<div id="panelFilters" class="tab-panel hidden">
        <div class="mb-4">
            <p class="text-sm font-semibold text-gray-700 mb-3">Aplikovat filtry na vrstvy:</p>
            <div class="flex gap-2 flex-wrap">
                <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors border border-gray-200">
                    <input type="checkbox" id="filterLayerImage" checked class="filterLayerCheck w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Obrázek</span>
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors border border-gray-200">
                    <input type="checkbox" id="filterLayerDraw" class="filterLayerCheck w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Kresby</span>
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors border border-gray-200">
                    <input type="checkbox" id="filterLayerText" class="filterLayerCheck w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Text</span>
                </label>
            </div>
        </div>
        <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm">
            <div class="mb-3">
                <h3 class="text-sm font-bold text-gray-800">Rychlé filtry</h3>
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
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm">
            <div class="mb-3">
                <h3 class="text-sm font-bold text-gray-800">Ruční vyladění</h3>
            </div>
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Jas
                    <input type="range" id="brightness" min="-1" max="1" step="0.1" value="0" class="mt-1 w-full">
                </label>
                <label class="block text-sm font-medium text-gray-700">Kontrast
                    <input type="range" id="contrast" min="-1" max="1" step="0.1" value="0" class="mt-1 w-full">
                </label>
                <label class="block text-sm font-medium text-gray-700">Sytost
                    <input type="range" id="saturation" min="-1" max="1" step="0.1" value="0" class="mt-1 w-full">
                </label>
            </div>
        </div>
    </div>