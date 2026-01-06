<x-layout>
    <x-slot:heading>Editor obrázku</x-slot:heading>
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

    <div class="flex gap-2 mb-4 justify-around">
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
    <div class="flex-1 flex justify-center items-center bg-gray-50">
        <canvas id="canvas" class="border border-gray-300 shadow-lg"></canvas>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
const canvas = new fabric.Canvas('canvas');
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
let line, circle, rect;
let origX, origY;

// Vizuální kurzor gumy
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

    if (drawMode) {
        isDown = true;

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
    if (drawMode && isDown) {
        // Po dokončení kreslení nastavit objekt jako selectable
        const lastObj = [line, circle, rect].find(obj => obj !== null);
        if (lastObj) {
            lastObj.set({
                selectable: true,
                evented: true
            });
        }
        line = null;
        circle = null;
        rect = null;
        isDown = false;
        canvas.discardActiveObject();
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

        canvas.add(img);


        fitImageToCanvas(img);
        fitObjectToViewport(img);
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

    const img = currentImage;
    const rect = cropRect;

    // Získání hranice crop oblasti v souřadnicích canvasu
    const cropBounds = rect.getBoundingRect(true);
    const cropLeft = cropBounds.left;
    const cropTop = cropBounds.top;
    const cropVisualWidth = cropBounds.width;
    const cropVisualHeight = cropBounds.height;

    // Uložení všech objektů 
    const drawObjectsToClone = canvas.getObjects().filter(obj => 
        obj.layer === 'draw' && obj !== eraserCursor
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
                    layer: 'draw'
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
    });
});

document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', applyFilters);
});

// Posuvníky
document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', applyFilters);
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
    let filename = `export.${format}`;
    let dataURL;

    if (contentType === 'canvas') {
        filename = `canvas_export.${format}`;
        dataURL = canvas.toDataURL({ format: format, quality: quality, multiplier: 1 });

    } 
 else { 
    filename = `image_only.${format}`;
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
    return document.getElementById('fillTransparent').checked ? '' : document.getElementById('fillColor').value;
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

        // Pokud je to text v editačním režimu, necháme Fabric.js zpracovat klávesu
        if (activeObj.type === 'i-text' && activeObj.isEditing) {
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
canvas.on('selection:created', syncUIWithSelection);
canvas.on('selection:updated', syncUIWithSelection);
canvas.on('selection:cleared', () => {
    // Při zrušení výběru necháme UI beze změny
});

function syncUIWithSelection() {
    const obj = canvas.getActiveObject();
    if (!obj || obj === currentImage || obj === cropRect || obj === eraserCursor) return;

    // Synchronizace barvy obrysu
    if (obj.stroke) {
        document.getElementById('drawColor').value = obj.stroke;
    }

    // Synchronizace barvy výplně
    if (obj.fill && obj.fill !== '') {
        document.getElementById('fillColor').value = obj.fill;
        document.getElementById('fillTransparent').checked = false;
    } else if (obj.fill === '' || obj.fill === null || obj.fill === 'transparent') {
        document.getElementById('fillTransparent').checked = true;
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

    // Synchronizace vlastností textu (IText)
    if (obj.type === 'i-text') {
        if (obj.fontSize) {
            document.getElementById('textSize').value = obj.fontSize;
        }
        if (obj.fill) {
            document.getElementById('textColor').value = obj.fill;
        }
        if (obj.fontFamily) {
            document.getElementById('textFont').value = obj.fontFamily;
            // Aktualizace font pickeru
            if (typeof setFontPickerValue === 'function') {
                setFontPickerValue(obj.fontFamily);
            }
        }
        
        // Synchronizace zarovnání textu
        const align = obj.textAlign || 'left';
        document.querySelectorAll('.text-align-btn').forEach(btn => btn.classList.remove('bg-pink-200'));
        const alignBtn = document.getElementById('align' + align.charAt(0).toUpperCase() + align.slice(1));
        if (alignBtn) alignBtn.classList.add('bg-pink-200');
        
        // Synchronizace stylů textu
        const isBold = obj.fontWeight === 'bold';
        const isItalic = obj.fontStyle === 'italic';
        const isUnderline = obj.underline === true;
        const isLinethrough = obj.linethrough === true;
        
        document.getElementById('textBold').classList.toggle('bg-pink-200', isBold);
        document.getElementById('textItalic').classList.toggle('bg-pink-200', isItalic);
        document.getElementById('textUnderline').classList.toggle('bg-pink-200', isUnderline);
        document.getElementById('textLinethrough').classList.toggle('bg-pink-200', isLinethrough);
        
        // Aktualizace globálních proměnných stylů
        if (typeof textStyles !== 'undefined') {
            textStyles.bold = isBold;
            textStyles.italic = isItalic;
            textStyles.underline = isUnderline;
            textStyles.linethrough = isLinethrough;
        }
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
    const textValue = document.getElementById('textInput').value.trim();
    if (!textValue) return;

    // Získání aktuálního zarovnání
    let currentAlign = 'left';
    if (document.getElementById('alignCenter').classList.contains('bg-pink-200')) currentAlign = 'center';
    if (document.getElementById('alignRight').classList.contains('bg-pink-200')) currentAlign = 'right';

    // Získání aktuálních stylů
    const isBold = document.getElementById('textBold').classList.contains('bg-pink-200');
    const isItalic = document.getElementById('textItalic').classList.contains('bg-pink-200');
    const isUnderline = document.getElementById('textUnderline').classList.contains('bg-pink-200');
    const isLinethrough = document.getElementById('textLinethrough').classList.contains('bg-pink-200');

    const text = new fabric.IText(textValue, {
        left: canvas.width / 2,
        top: canvas.height / 2,
        originX: 'center',
        originY: 'center',
        fill: document.getElementById('textColor').value,
        fontSize: parseInt(document.getElementById('textSize').value),
        fontFamily: document.getElementById('textFont').value,
        textAlign: currentAlign,
        fontWeight: isBold ? 'bold' : 'normal',
        fontStyle: isItalic ? 'italic' : 'normal',
        underline: isUnderline,
        linethrough: isLinethrough,
        selectable: true,
        evented: true,
        layer: 'text'
    });

    canvas.add(text);
    canvas.setActiveObject(text);
    canvas.requestRenderAll();
});
// Úprava vlastností textu
function applyToActiveText(props) {
    const obj = canvas.getActiveObject();
    if (!obj || obj.type !== 'i-text') return;

    obj.set(props);
    canvas.requestRenderAll();
}

document.getElementById('textSize').addEventListener('input', e => {
    applyToActiveText({ fontSize: parseInt(e.target.value) });
});

document.getElementById('textColor').addEventListener('input', e => {
    applyToActiveText({ fill: e.target.value });
});

// Zarovnání textu
let currentTextAlign = 'left';

function setTextAlign(align) {
    currentTextAlign = align;
    
    // Aktualizace UI tlačítek
    document.querySelectorAll('.text-align-btn').forEach(btn => btn.classList.remove('bg-pink-200'));
    document.getElementById('align' + align.charAt(0).toUpperCase() + align.slice(1)).classList.add('bg-pink-200');
    
    // Aplikace na aktivní text
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

function toggleTextStyle(style) {
    textStyles[style] = !textStyles[style];
    
    const btnMap = {
        bold: 'textBold',
        italic: 'textItalic',
        underline: 'textUnderline',
        linethrough: 'textLinethrough'
    };
    
    const btn = document.getElementById(btnMap[style]);
    if (textStyles[style]) {
        btn.classList.add('bg-pink-200');
    } else {
        btn.classList.remove('bg-pink-200');
    }
    
    // Aplikace na aktivní text
    const obj = canvas.getActiveObject();
    if (obj && obj.type === 'i-text') {
        if (style === 'bold') {
            obj.set('fontWeight', textStyles.bold ? 'bold' : 'normal');
        } else if (style === 'italic') {
            obj.set('fontStyle', textStyles.italic ? 'italic' : 'normal');
        } else if (style === 'underline') {
            obj.set('underline', textStyles.underline);
        } else if (style === 'linethrough') {
            obj.set('linethrough', textStyles.linethrough);
        }
        canvas.requestRenderAll();
    }
}

document.getElementById('textBold').addEventListener('click', () => toggleTextStyle('bold'));
document.getElementById('textItalic').addEventListener('click', () => toggleTextStyle('italic'));
document.getElementById('textUnderline').addEventListener('click', () => toggleTextStyle('underline'));
document.getElementById('textLinethrough').addEventListener('click', () => toggleTextStyle('linethrough'));

// Font Picker Dropdown
const fontCategories = {
    'Bezpatkové': ['Arial', 'Helvetica', 'Verdana', 'Tahoma', 'Trebuchet MS', 'Segoe UI', 'Open Sans', 'Roboto', 'Lato', 'Montserrat'],
    'Patkové': ['Times New Roman', 'Georgia', 'Palatino Linotype', 'Book Antiqua', 'Garamond', 'Playfair Display'],
    'Neproporcionální': ['Courier New', 'Consolas', 'Monaco', 'Lucida Console', 'Source Code Pro'],
    'Rukopisné': ['Comic Sans MS', 'Brush Script MT', 'Pacifico', 'Dancing Script', 'Caveat'],
    'Dekorativní': ['Impact', 'Anton', 'Bebas Neue', 'Oswald', 'Lobster']
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



</script>

</x-layout>
