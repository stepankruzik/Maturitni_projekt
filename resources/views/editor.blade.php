<x-layout>
    <x-slot:heading>Editor obrázku</x-slot:heading>

    <div class="flex flex-col items-center space-y-4">
        <p id="imageSize" class="text-gray-600 font-semibold"></p>
        <!-- Canvas -->
        <canvas id="canvas" class="border border-gray-300 shadow-lg"></canvas>

        <!-- Tlačítka -->
        <div class="flex gap-2">
            <button id="toggleMode" class="px-4 py-2 bg-yellow-500 text-white rounded">Režim: Změnit velikost</button>
            <button id="cropBtn" class="px-4 py-2 bg-purple-500 text-white rounded">Oříznout</button>
            <button id="grayscale" class="px-4 py-2 bg-gray-500 text-white rounded">Odstíny šedi</button>
            <button id="download" class="px-4 py-2 bg-green-500 text-white rounded">Stáhnout</button>
        </div>
    </div>
        <!-- Jas kontrast-->
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
    <div class="flex gap-4 items-center mt-2">
    <label>Filtr:</label>
    <select id="filterSelect">
        <option value="none">Žádný</option>
        <option value="sepia">Sepia</option>
        <option value="invert">Invert</option>
        <option value="blur">Blur</option>
        <option value="sharpen">Sharpen</option>
        <option value="pixelate">Pixelate</option>
    </select>
</div>
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
const canvas = new fabric.Canvas('canvas');
let currentImage = null;
let cropRect = null;
let mode = 'resize';

// Načtení obrázku
const imgUrl = "{{ asset($imagePath) }}";
fabric.Image.fromURL(imgUrl, (img) => {
    const padding = 100;
    canvas.setWidth(img.width + padding);
    canvas.setHeight(img.height + padding);

    img.set({
        left: canvas.width / 2,
        top: canvas.height / 2,
        originX: 'center',
        originY: 'center',
        selectable: true,
        hasRotatingPoint: true,
        cornerStyle: 'circle'
    });

    canvas.add(img);
    canvas.setActiveObject(img);
    currentImage = img;
    canvas.renderAll();
    updateImageSize();
});

// (resize / crop)
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

// Oříznutí
document.getElementById('cropBtn').addEventListener('click', () => {
    if (mode !== 'crop' || !cropRect || !currentImage) return;

    //  pomocné plátno
    const tempCanvas = document.createElement('canvas');
    const ctx = tempCanvas.getContext('2d');

    const imgEl = currentImage._element;
    const width = imgEl.naturalWidth;
    const height = imgEl.naturalHeight;

    tempCanvas.width = width;
    tempCanvas.height = height;

    // Překreslení obrázku 
    ctx.save();
    ctx.translate(width / 2, height / 2);
    ctx.rotate((currentImage.angle * Math.PI) / 180);
    ctx.scale(currentImage.scaleX, currentImage.scaleY);
    ctx.drawImage(imgEl, -imgEl.width / 2, -imgEl.height / 2);
    ctx.restore();

    // Výřez podle cropRectu
    const rect = cropRect.getBoundingRect(true);
    const canvasCenter = { x: canvas.width / 2, y: canvas.height / 2 };

    const cropX = rect.left - (canvasCenter.x - width / 2);
    const cropY = rect.top - (canvasCenter.y - height / 2);

    const croppedCanvas = document.createElement('canvas');
    croppedCanvas.width = rect.width;
    croppedCanvas.height = rect.height;

    const croppedCtx = croppedCanvas.getContext('2d');
    croppedCtx.drawImage(
        tempCanvas,
        cropX,
        cropY,
        rect.width,
        rect.height,
        0,
        0,
        rect.width,
        rect.height
    );

    const croppedData = croppedCanvas.toDataURL('image/png');

    fabric.Image.fromURL(croppedData, (img) => {
        canvas.clear();
        img.set({
            left: canvas.width / 2,
            top: canvas.height / 2,
            originX: 'center',
            originY: 'center',
            selectable: true,
            hasRotatingPoint: true
        });
        canvas.add(img);
        currentImage = img;
        cropRect = null;
        mode = 'resize';
        document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
    });
});

// Grayscale 
document.getElementById('grayscale').addEventListener('click', () => {
    if (!currentImage) return;
    currentImage.filters = [new fabric.Image.filters.Grayscale()];
    currentImage.applyFilters();
    canvas.renderAll();
});

// Download
document.getElementById('download').addEventListener('click', () => {
    const dataURL = canvas.toDataURL({ format: 'png', quality: 1 });
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'edited.png';
    link.click();
});

//  Zoom a posun
let isDragging = false;
let lastPosX, lastPosY;

/*canvas.on('mouse:wheel', (opt) => {
    const delta = opt.e.deltaY;
    let zoom = canvas.getZoom();
    zoom *= 0.999 ** delta;
    zoom = Math.min(Math.max(zoom, 0.5), 5);
    canvas.zoomToPoint({ x: opt.e.offsetX, y: opt.e.offsetY }, zoom);
    opt.e.preventDefault();
    opt.e.stopPropagation();
});*/

canvas.on('mouse:down', (opt) => {
    const evt = opt.e;
    if (evt.altKey) {
        isDragging = true;
        canvas.selection = false;
        lastPosX = evt.clientX;
        lastPosY = evt.clientY;
    }
});

canvas.on('mouse:move', (opt) => {
    if (isDragging) {
        const e = opt.e;
        const vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - lastPosX;
        vpt[5] += e.clientY - lastPosY;
        canvas.requestRenderAll();
        lastPosX = e.clientX;
        lastPosY = e.clientY;
    }
});

canvas.on('mouse:up', () => {
    isDragging = false;
    canvas.selection = true;
});

//  Jas / Kontrast / Sytost
const updateFilters = () => {
    if (!currentImage) return;
    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    document.getElementById('brightnessVal').textContent = `${Math.round(brightness * 100)}%`;
    document.getElementById('contrastVal').textContent = `${Math.round(contrast * 100)}%`;
    document.getElementById('saturationVal').textContent = `${Math.round(saturation * 100)}%`;

    currentImage.filters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];
    currentImage.applyFilters();
    canvas.renderAll();
};

document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input =>
    input.addEventListener('input', updateFilters)
);
// Filtry
document.getElementById('filterSelect').addEventListener('change', () => {
    if (!currentImage) return;

    let filter = null;
    switch (document.getElementById('filterSelect').value) {
        case 'sepia':
            filter = new fabric.Image.filters.Sepia();
            break;
        case 'invert':
            filter = new fabric.Image.filters.Invert();
            break;
        case 'blur':
            filter = new fabric.Image.filters.Blur({ blur: 0.5 });
            break;
        case 'sharpen':
            filter = new fabric.Image.filters.Convolute({
                matrix: [0, -1, 0, -1, 5, -1, 0, -1, 0]
            });
            break;
        case 'pixelate':
            filter = new fabric.Image.filters.Pixelate({ blocksize: 8 });
            break;
    }

    // zachováme jas/kontrast/sytost
    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    const filters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];

    if (filter) filters.push(filter);

    currentImage.filters = filters;
    currentImage.applyFilters();
    canvas.renderAll();
});

// pocita velikost
function updateImageSize() {
    if (!currentImage) return;
    const width = Math.round(currentImage.width * currentImage.scaleX);
    const height = Math.round(currentImage.height * currentImage.scaleY);
    document.getElementById('imageSize').textContent = `Velikost: ${width} × ${height} px`;
}
canvas.on('object:modified', () => {
    updateImageSize();
});

</script>

</x-layout>
