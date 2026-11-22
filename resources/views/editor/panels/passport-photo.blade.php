<div id="panelPassport" class="tab-panel hidden">
    <details open class="mb-4 bg-white rounded-lg shadow p-4">
        <summary class="cursor-pointer font-bold text-lg text-sky-700">
            Průkazová fotografie 35 × 45 mm
        </summary>

        <div class="mt-3 space-y-4">
            <p class="text-sm text-gray-600">
                Rozměr fotografie je nastaven na 413 × 531 px při 300 DPI. Vodicí vrstva se při exportu automaticky odstraní.
            </p>

            <button id="applyPassportSizeBtn" type="button" class="w-full rounded-lg bg-sky-600 px-4 py-2 font-semibold text-white transition hover:bg-sky-700">
                Nastavit formát 35 × 45 mm
            </button>

            <button id="preparePassportBackgroundBtn" type="button" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">
                Upravit pozadí pro doklady
            </button>

            <button id="createPassportSheetBtn" type="button" class="w-full rounded-lg bg-slate-800 px-4 py-3 font-semibold text-white transition hover:bg-slate-900 disabled:cursor-not-allowed disabled:opacity-50">
                Vytvořit tiskový arch 10 × 15 cm (4 fotky)
            </button>

            <p id="passportStatus" class="hidden rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-sm text-sky-800" role="status" aria-live="polite"></p>
            <p id="passportError" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert"></p>
        </div>
    </details>
</div>
