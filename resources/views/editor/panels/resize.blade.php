<div id="panelResize" class="tab-panel">
        <!-- Ořez -->
        <details open class="mb-4 bg-white rounded-lg shadow p-4">
            <summary class="cursor-pointer font-bold text-lg text-blue-700 mb-3">
                Ořez
            </summary>
            <div class="space-y-2">
                <button id="toggleMode" class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span>Režim: Změnit velikost</span>
                </button>
                <button id="cropBtn" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/>
                    </svg>
                    <span>Oříznout</span>
                </button>
            </div>
        </details>

        <!-- Transformace -->
        <details open class="mb-4 bg-white rounded-lg shadow p-4">
            <summary class="cursor-pointer font-bold text-lg text-blue-700 mb-3">
                Transformace
            </summary>

            <!-- Otočení -->
            <div class="mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-2">Otočení:</p>
                <div class="grid grid-cols-2 gap-2">
                    <button id="rotate90CW" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition flex items-center justify-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        90° doprava
                    </button>
                    <button id="rotate90CCW" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition flex items-center justify-center gap-1 text-sm">
                        <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        90° doleva
                    </button>
                    <button id="rotate180" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition flex items-center justify-center gap-1 text-sm col-span-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        180°
                    </button>
                </div>
            </div>

            <!-- Převrácení -->
            <div class="mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-2">Převrácení:</p>
                <div class="grid grid-cols-2 gap-2">
                    <button id="flipHorizontal" class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition flex items-center justify-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Horizontálně
                    </button>
                    <button id="flipVertical" class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition flex items-center justify-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Vertikálně
                    </button>
                </div>
            </div>

            <!-- Vlastní úhel -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vlastní úhel:</label>
                <div class="flex gap-2 items-center">
                    <input type="number" id="customAngle" placeholder="0" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm" min="-360" max="360" step="1">
                    <button id="applyCustomAngle" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition text-sm">
                        Použít
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Zadej úhel v stupních (-360 až 360)</p>
            </div>
        </details>
    </div>