# Editor fotek

Webová aplikace pro úpravu obrázků přímo v prohlížeči.

## Co aplikace umí
- Nahrání obrázku přes drag and drop nebo výběr souboru.
- Vytvoření prázdného plátna (vlastní rozměr nebo připravené šablony).
- Úpravy obrázku: ořez, změna velikosti, otočení a převrácení.
- Filtry a korekce: jas, kontrast, sytost, rychlé filtry.
- Kreslení do obrázku: štětec, guma a různé tvary.
- Vkládání a formátování textu.
- Přibližování/oddalování plátna, posun plochy a historie kroků (undo/redo).
- Export výsledku do PNG, JPEG nebo WebP.

## Požadavky
- PHP 8.2+
- Composer
- Node.js 18+ a npm

## Instalace
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Spuštění vývoje
```bash
composer run dev
```

Tento příkaz spustí:
- Laravel server (`artisan serve`) s limitem uploadu 20 MB,
- queue listener,
- Vite dev server.

## Build pro produkci
```bash
npm run build
```

## Základní použití
1. Otevři aplikaci na domovské stránce.
2. Nahraj obrázek nebo vytvoř prázdné plátno.
3. Proveď úpravy v editoru.
4. Exportuj výsledek do PNG, JPEG nebo WebP.

## Poznámka
Nahrané i uložené soubory se ukládají do složky `public/uploads`.

## Autor
**Štěpán Kružík** – [kruziks.06@spst.eu](mailto:kruziks.06@spst.eu)
