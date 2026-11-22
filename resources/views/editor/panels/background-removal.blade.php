<div id="panelBackground" class="tab-panel hidden">
    <details open class="mb-4 bg-white rounded-lg shadow p-4">
        <summary class="cursor-pointer font-bold text-lg text-emerald-700">
            AI odstranění pozadí
        </summary>

        <div class="mt-3 space-y-4">
            <p class="text-sm text-gray-600">
                Zpracování probíhá přímo v tomto prohlížeči. Obrázek se nenahrává na server.
            </p>

            <button id="removeBackgroundBtn" type="button" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">
                Odstranit pozadí pomocí AI
            </button>

            <div id="backgroundRemovalProgressWrap" class="hidden space-y-2">
                <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                    <div id="backgroundRemovalProgress" class="h-full w-0 rounded-full bg-emerald-500 transition-all"></div>
                </div>
                <p id="backgroundRemovalStatus" class="text-sm text-gray-700" role="status" aria-live="polite"></p>
            </div>

            <div id="backgroundRemovalResult" class="hidden space-y-3">
                <p class="text-sm font-semibold text-gray-700">Pozadí výsledku</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" data-background-color="transparent" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium hover:border-emerald-500">Průhledné (PNG)</button>
                    <button type="button" data-background-color="#ffffff" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium hover:border-emerald-500">Bílé (pro e-shop)</button>
                    <button type="button" data-background-color="#000000" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium hover:border-emerald-500">Černé</button>
                    <label class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium">
                        <span>Vlastní</span>
                        <input id="backgroundColorPicker" type="color" value="#ffffff" class="h-7 w-10 cursor-pointer border-0 bg-transparent">
                    </label>
                </div>

                <button id="downloadBackgroundResultBtn" type="button" class="w-full rounded-lg bg-slate-800 px-4 py-2 font-semibold text-white transition hover:bg-slate-900">
                    Stáhnout výsledek jako PNG
                </button>
            </div>

            <p id="backgroundRemovalError" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert"></p>
        </div>
    </details>
</div>
