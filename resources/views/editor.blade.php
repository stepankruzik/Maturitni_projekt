<x-layout>
    <x-slot:heading>Editor obrázku</x-slot:heading>

    <div class="flex flex-col items-center space-y-4">
        <p id="imageSize" class="text-gray-600 font-semibold"></p>
        <p id="rotationAngle" class="text-gray-600 font-semibold"></p>

        <!-- Canvas -->
        <canvas id="canvas" class="border border-gray-300 shadow-lg"></canvas>

        <!-- Tlačítka -->
        <div class="flex gap-2">
            <button id="toggleMode" class="px-4 py-2 bg-yellow-500 text-white rounded">Režim: Změnit velikost</button>
            <button id="cropBtn" class="px-4 py-2 bg-purple-500 text-white rounded">Oříznout</button>
            <button id="grayscale" class="px-4 py-2 bg-gray-500 text-white rounded">Odstíny šedi</button>
            <button id="download" class="px-4 py-2 bg-green-500 text-white rounded">Stáhnout</button>
        </div>

        <!-- filtry -->
        <div id="filterPreview" class="flex gap-2 overflow-x-auto p-2 bg-gray-100 rounded mt-4">
            <div class="filter-thumb" data-filter="original">Originál</div>
            <div class="filter-thumb" data-filter="grayscale">Šedý</div>
            <div class="filter-thumb" data-filter="sepia">Sepia</div>
            <div class="filter-thumb" data-filter="invert">Invert</div>
            <div class="filter-thumb" data-filter="blur">Blur</div>
            <div class="filter-thumb" data-filter="sharpen">Ostřit</div>
        </div>

        <!-- Jas / Kontrast / Sytost -->
        <div class="flex gap-4 items-center mt-4">
            <div class="flex items-center gap-2">
                <label>Jas:</label>
                <input type="range" id="brightness" min="-1" max="1" step="0.1" value="0">
                <span id="brightnessVal" class="text-sm text-gray-600 w-12 text-right">0%</span>
            </div>
            <div class="flex items-center gap-2">
                <label>Kontrast:</label>
                <input type="range" id="contrast" min="-1" max="1" step="0.1" value="0">
                <span id="contrastVal" class="text-sm text-gray-600 w-12 text-right">0%</span>
            </div>
            <div class="flex items-center gap-2">
                <label>Sytost:</label>
                <input type="range" id="saturation" min="-1" max="1" step="0.1" value="0">
                <span id="saturationVal" class="text-sm text-gray-600 w-12 text-right">0%</span>
            </div>
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

// Načtení obrázku z URL poslané z indexu
const imageUrl = @json(request('path'));
if (imageUrl) loadImage(imageUrl);

// Funkce pro načtení obrázku do Fabric canvasu
function loadImage(url) {
    fabric.Image.fromURL(url, (img) => {
        if (currentImage) canvas.remove(currentImage);

        currentImage = img;
        img.set({
            originX: 'center',
            originY: 'center',
            selectable: true,
            hasRotatingPoint: true,
            cornerStyle: 'circle'
        });
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

// Toggle resize / crop režimu
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
document.getElementById('cropBtn').addEventListener('click', () => {
    if (mode !== 'crop' || !cropRect || !currentImage) return;

    const tempCanvas = document.createElement('canvas');
    const ctx = tempCanvas.getContext('2d');

    const imgEl = currentImage._element;
    const width = imgEl.naturalWidth;
    const height = imgEl.naturalHeight;

    tempCanvas.width = width;
    tempCanvas.height = height;

    ctx.save();
    ctx.translate(width/2, height/2);
    ctx.rotate(currentImage.angle * Math.PI / 180);
    ctx.scale(currentImage.scaleX, currentImage.scaleY);
    ctx.drawImage(imgEl, -imgEl.width/2, -imgEl.height/2);
    ctx.restore();

    const rect = cropRect.getBoundingRect(true);
    const canvasCenter = { x: canvas.width / 2, y: canvas.height / 2 };

    const cropX = rect.left - (canvasCenter.x - width/2);
    const cropY = rect.top - (canvasCenter.y - height/2);

    const croppedCanvas = document.createElement('canvas');
    croppedCanvas.width = rect.width;
    croppedCanvas.height = rect.height;

    const croppedCtx = croppedCanvas.getContext('2d');
    croppedCtx.drawImage(tempCanvas, cropX, cropY, rect.width, rect.height, 0, 0, rect.width, rect.height);

    const croppedData = croppedCanvas.toDataURL('image/png');

    fabric.Image.fromURL(croppedData, (img) => {
        canvas.clear();
        currentImage = img;
        img.set({
            originX: 'center',
            originY: 'center',
            selectable: true,
            hasRotatingPoint: true,
            cornerStyle: 'circle'
        });
        canvas.add(img);
        fitImageToCanvas(img);
        fitObjectToViewport(img);
        cropRect = null;
        mode = 'resize';
        document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
    });
});

// Download
document.getElementById('download').addEventListener('click', () => {
    if (!currentImage) return;

    const imgEl = currentImage._element;
    let origW = imgEl.naturalWidth || currentImage.width * currentImage.scaleX;
    let origH = imgEl.naturalHeight || currentImage.height * currentImage.scaleY;
    const angle = (currentImage.angle || 0) * Math.PI / 180;
    const absCos = Math.abs(Math.cos(angle));
    const absSin = Math.abs(Math.sin(angle));
    const boundsW = origW * absCos + origH * absSin;
    const boundsH = origW * absSin + origH * absCos;

    const exportCanvas = document.createElement('canvas');
    exportCanvas.width = Math.round(boundsW);
    exportCanvas.height = Math.round(boundsH);
    const exportCtx = exportCanvas.getContext('2d');

    exportCtx.save();
    exportCtx.translate(exportCanvas.width/2, exportCanvas.height/2);
    exportCtx.rotate(angle);
    exportCtx.scale(currentImage.scaleX || 1, currentImage.scaleY || 1);
    exportCtx.drawImage(imgEl, -imgEl.width/2, -imgEl.height/2);
    exportCtx.restore();

    const dataURL = exportCanvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'edited.png';
    link.click();
});

// Filtry / Jas / Kontrast / Sytost
let activeFilter = null;

function applyFilters() {
    if (!currentImage) return;

    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    document.getElementById('brightnessVal').textContent = `${Math.round(brightness * 100)}%`;
    document.getElementById('contrastVal').textContent = `${Math.round(contrast * 100)}%`;
    document.getElementById('saturationVal').textContent = `${Math.round(saturation * 100)}%`;

    const filters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];

    if (activeFilter) filters.push(activeFilter);

    currentImage.filters = filters;
    currentImage.applyFilters();
    canvas.renderAll();
}

// Filtry
document.querySelectorAll('.filter-thumb').forEach(thumb => {
    thumb.addEventListener('click', () => {
        document.querySelectorAll('.filter-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');

        const type = thumb.getAttribute('data-filter');
        switch (type) {
            case 'grayscale': activeFilter = new fabric.Image.filters.Grayscale(); break;
            case 'sepia': activeFilter = new fabric.Image.filters.Sepia(); break;
            case 'invert': activeFilter = new fabric.Image.filters.Invert(); break;
            case 'blur': activeFilter = new fabric.Image.filters.Blur({ blur: 0.3 }); break;
            case 'pixelate': activeFilter = new fabric.Image.filters.Pixelate({ blocksize: 6 }); break;
            case 'sharpen': activeFilter = new fabric.Image.filters.Convolute({ matrix: [0, -1, 0, -1, 5, -1, 0, -1, 0] }); break;
            default: activeFilter = null; break;
        }

        applyFilters();
    });
});

// Posuvníky
document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', applyFilters);
});


// Drag / Pan
let isDragging=false, lastPosX, lastPosY;
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
});

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

</script>

</x-layout>
