<x-layout>
    <x-slot:heading>Editor obrázku</x-slot:heading>

    <div class="flex h-screen">
    <!-- Sidebar vlevo -->
    <div class="w-72 bg-gray-100 border-r border-gray-300 p-4 overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Úpravy obrázku</h2>

        <p id="imageSize" class="text-gray-600 font-semibold mb-1"></p>
        <p id="rotationAngle" class="text-gray-600 font-semibold mb-3"></p>

        <!-- Režimy a akce -->
        <div class="space-y-2 mb-4">
           <button id="toggleMode" class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
        Režim: Změnit velikost
    </button>
    <button id="cropBtn" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">
        Oříznout
    </button>
</div>

<details open class="mb-4 bg-white rounded-lg shadow p-4">
    <summary class="cursor-pointer font-bold text-lg text-green-700">
        Nastavení Exportu
    </summary>

    <div class="space-y-3 mt-3">
        <!-- Formát souboru -->
        <div>
            <label for="exportFormat" class="block text-sm font-medium text-gray-700 mb-1">
                Formát souboru:
            </label>
            <select id="exportFormat" class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                <option value="png">PNG (bezeztrátový)</option>
                <option value="jpeg">JPEG (menší velikost)</option>
                <option value="webp">WEBP (webové stránky)</option>
            </select>
        </div>

        <!-- Obsah exportu -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Obsah exportu:
            </label>
            <div class="flex flex-col gap-2 bg-gray-50 p-3 rounded-md border border-gray-200">
                <label class="inline-flex items-center">
                    <input type="radio" name="exportContent" value="canvas" checked class="form-radio text-green-600">
                    <span class="ml-2 text-sm">Celý Canvas (vč. pozadí 900x600)</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="exportContent" value="image" class="form-radio text-green-600">
                    <span class="ml-2 text-sm">Jen obrázek (bez prázdného místa)</span>
                </label>
            </div>
        </div>

        <!-- Tlačítko exportu -->
        <button id="startDownloadBtn" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
            Stáhnout
        </button>
    </div>
</details>

        <!-- Filtry -->
        <details open class="mb-4 bg-white rounded-lg shadow p-3">
            <summary class="cursor-pointer font-medium">Filtry</summary>
            <div id="filterPreview" class="flex flex-wrap gap-2 mt-2">
    <img src="/thumbnails/original.png" class="filter-thumb" data-filter="original">
    <img src="/thumbnails/grayscale.png" class="filter-thumb" data-filter="grayscale">
    <img src="/thumbnails/sepia.png" class="filter-thumb" data-filter="sepia">
    <img src="/thumbnails/invert.png" class="filter-thumb" data-filter="invert">
    <img src="/thumbnails/blur.png" class="filter-thumb" data-filter="blur">
    <img src="/thumbnails/sharpen.png" class="filter-thumb" data-filter="sharpen">
</div>
        </details>

        <!-- Jas / Kontrast / Sytost -->
        <details open class="bg-white rounded-lg shadow p-3">
            <summary class="cursor-pointer font-medium">Úrovně</summary>
            <div class="space-y-3 mt-2">
                <label class="flex items-center gap-2">
                    Jas:
                    <input type="range" id="brightness" min="-1" max="1" step="0.1" value="0" class="w-full">
                </label>
                <label class="flex items-center gap-2">
                    Kontrast:
                    <input type="range" id="contrast" min="-1" max="1" step="0.1" value="0" class="w-full">
                </label>
                <label class="flex items-center gap-2">
                    Sytost:
                    <input type="range" id="saturation" min="-1" max="1" step="0.1" value="0" class="w-full">
                </label>
            </div>
        </details>
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




canvas.on("object:rotating", function(e) {
    if (!e.target) return;
    keepInsideCanvas(e.target);
});

*/

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
        originalImageWidth = img.width;
        originalImageHeight = img.height;
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

    currentImage.cloneAsImage(function(imgWithFilter) {
        const tempCanvas = document.createElement('canvas');
        const ctx = tempCanvas.getContext('2d');

        const width = imgWithFilter.width;
        const height = imgWithFilter.height;

        tempCanvas.width = width;
        tempCanvas.height = height;

        ctx.save();
        ctx.translate(width/2, height/2);
        ctx.rotate(currentImage.angle * Math.PI / 180);
        ctx.scale(currentImage.scaleX, currentImage.scaleY);
        ctx.drawImage(imgWithFilter._element, -imgWithFilter.width/2, -imgWithFilter.height/2);
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

        const finalW = img.getScaledWidth();
        const finalH = img.getScaledHeight();
        
        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = Math.round(finalW);
        exportCanvas.height = Math.round(finalH);
        const exportCtx = exportCanvas.getContext('2d');
        
        
        const originalScaleX = img.scaleX;
        const originalScaleY = img.scaleY;
        const originalAngle = img.angle;
        
        img.set({ scaleX: 1, scaleY: 1, angle: 0 });
        img.setCoords();
        
        exportCtx.save();
        exportCtx.translate(finalW / 2, finalH / 2);
        exportCtx.rotate(originalAngle * Math.PI / 180);
        exportCtx.scale(originalScaleX, originalScaleY);
        
        exportCtx.drawImage(
            img._element, 
            -img.width / 2, 
            -img.height / 2, 
            img.width, 
            img.height
        );
        exportCtx.restore();

        img.set({ scaleX: originalScaleX, scaleY: originalScaleY, angle: originalAngle });
        img.setCoords();
        canvas.renderAll(); 

        dataURL = exportCanvas.toDataURL(mimeType, quality);
    }
    
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = filename;
    link.click();
});

</script>

</x-layout>
